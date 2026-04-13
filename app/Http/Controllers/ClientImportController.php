<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\ClientImport;
use App\Services\ActivityLogger;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ClientImportController extends Controller
{
    /**
     * Display the import page
     */
    public function index()
    {
        $recentImports = ClientImport::where('user_id', auth()->id())
            ->latest()
            ->take(5)
            ->get();

        return view('admin.clients.import.index', compact('recentImports'));
    }

    /**
     * Handle CSV/VCF file upload and validation
     */
    public function upload(Request $request)
    {
        $request->validate([
            'csv_file' => 'required|file|mimes:csv,txt,vcf,vcard|max:10240',
        ]);

        $file = $request->file('csv_file');
        $filename = time().'_'.$file->getClientOriginalName();
        $path = $file->storeAs('imports', $filename, 'private');

        // Parse file based on extension
        $extension = strtolower($file->getClientOriginalExtension());

        if (in_array($extension, ['vcf', 'vcard'])) {
            $csvData = $this->parseVcf(Storage::disk('private')->get($path));
        } else {
            $csvData = $this->parseCsv(Storage::disk('private')->get($path));
        }

        if (empty($csvData)) {
            Storage::delete($path);

            return back()->with('error', 'The CSV file is empty or invalid.');
        }

        // Create import record
        $import = ClientImport::create([
            'user_id' => auth()->id(),
            'filename' => $filename,
            'status' => 'pending',
            'total_rows' => count($csvData),
        ]);

        // Log the import initiation
        ActivityLogger::log(
            action: 'client_import_started',
            description: "Started client import: {$filename} ({$import->total_rows} rows)",
            subject: $import,
            properties: ['filename' => $filename, 'total_rows' => $import->total_rows]
        );

        // Store parsed data temporarily in session for preview
        session(['import_data_'.$import->id => $csvData]);

        return redirect()->route('clients.import.preview', $import)
            ->with('success', 'CSV uploaded successfully. Please review the data before importing.');
    }

    /**
     * Show preview of CSV data before importing
     */
    public function preview(ClientImport $clientImport)
    {
        if ($clientImport->user_id !== auth()->id()) {
            abort(403);
        }

        $csvData = session('import_data_'.$clientImport->id);

        if (! $csvData) {
            return redirect()->route('clients.import.index')
                ->with('error', 'Import data not found. Please upload the file again.');
        }

        // Validate each row and collect errors
        $validatedData = [];
        $errors = [];

        foreach ($csvData as $index => $row) {
            $validator = $this->validateRow($row, $index);

            if ($validator->fails()) {
                $errors[$index] = $validator->errors()->all();
            } else {
                $validatedData[$index] = $row;
            }
        }

        return view('admin.clients.import.preview', [
            'import' => $clientImport,
            'data' => $csvData,
            'validatedData' => $validatedData,
            'errors' => $errors,
        ]);
    }

    /**
     * Process the import
     */
    public function process(ClientImport $clientImport)
    {
        if ($clientImport->user_id !== auth()->id()) {
            abort(403);
        }

        $csvData = session('import_data_'.$clientImport->id);

        if (! $csvData) {
            return redirect()->route('clients.import.index')
                ->with('error', 'Import data not found. Please upload the file again.');
        }

        $clientImport->update(['status' => 'processing']);

        $successCount = 0;
        $failCount = 0;
        $errors = [];

        DB::beginTransaction();

        try {
            foreach ($csvData as $index => $row) {
                $validator = $this->validateRow($row, $index);

                if ($validator->fails()) {
                    $failCount++;
                    $errors[$index] = $validator->errors()->all();

                    continue;
                }

                try {
                    Client::create([
                        'name' => $row['name'] ?? null,
                        'email' => $row['email'] ?? null,
                        'phone' => $row['phone'] ?? null,
                        'property_address' => $row['property_address'] ?? null,
                        'notes' => $row['notes'] ?? null,
                        'source_site' => $row['source_site'] ?? 'import',
                    ]);
                    $successCount++;
                } catch (\Exception $e) {
                    $failCount++;
                    $errors[$index] = ['Database error: '.$e->getMessage()];
                }
            }

            $clientImport->update([
                'status' => 'completed',
                'successful_imports' => $successCount,
                'failed_imports' => $failCount,
                'errors' => $errors,
            ]);

            // Log the import completion
            ActivityLogger::log(
                action: 'client_import_completed',
                description: "Completed client import: {$clientImport->filename} ({$successCount} succeeded, {$failCount} failed)",
                subject: $clientImport,
                properties: [
                    'filename' => $clientImport->filename,
                    'successful_imports' => $successCount,
                    'failed_imports' => $failCount,
                    'total_rows' => $clientImport->total_rows,
                ]
            );

            DB::commit();

            // Clear session data
            session()->forget('import_data_'.$clientImport->id);

            return redirect()->route('clients.import.index')
                ->with('success', "Import completed! {$successCount} clients imported successfully".
                    ($failCount > 0 ? ", {$failCount} failed." : '.'));
        } catch (\Exception $e) {
            DB::rollBack();

            $clientImport->update([
                'status' => 'failed',
                'errors' => ['general' => [$e->getMessage()]],
            ]);

            // Log the import failure
            ActivityLogger::log(
                action: 'client_import_failed',
                description: "Client import failed: {$clientImport->filename} - {$e->getMessage()}",
                subject: $clientImport,
                properties: [
                    'filename' => $clientImport->filename,
                    'error' => $e->getMessage(),
                    'total_rows' => $clientImport->total_rows,
                ]
            );

            return redirect()->route('clients.import.index')
                ->with('error', 'Import failed: '.$e->getMessage());
        }
    }

    /**
     * Show import history
     */
    public function history()
    {
        $imports = ClientImport::where('user_id', auth()->id())
            ->latest()
            ->paginate(20);

        return view('admin.clients.import.history', compact('imports'));
    }

    /**
     * Parse CSV file
     */
    private function parseCsv(string $fileContents): array
    {
        $rows = [];
        $file = fopen('php://temp', 'r+');
        fwrite($file, $fileContents);
        rewind($file);

        if ($file !== false) {
            $header = fgetcsv($file, 1000, ',');

            if (! $header) {
                fclose($file);

                return [];
            }

            // Normalize header names
            $header = array_map(function ($col) {
                return strtolower(trim(str_replace(' ', '_', $col)));
            }, $header);

            while (($data = fgetcsv($file, 1000, ',')) !== false) {
                if (count($data) === count($header)) {
                    $rows[] = array_combine($header, $data);
                }
            }

            fclose($file);
        }

        return $rows;
    }

    /**
     * Parse VCF/vCard file
     */
    private function parseVcf(string $fileContents): array
    {
        $rows = [];
        $content = $fileContents;

        // Split into individual vCards
        $vcards = preg_split('/BEGIN:VCARD/i', $content);

        foreach ($vcards as $vcard) {
            if (empty(trim($vcard))) {
                continue;
            }

            $row = [
                'name' => '',
                'email' => '',
                'phone' => '',
                'property_address' => '',
                'notes' => '',
                'source_site' => 'phone_contacts',
            ];

            // Extract name (FN or N field)
            if (preg_match('/FN:(.*?)[\r\n]/i', $vcard, $matches)) {
                $row['name'] = trim($matches[1]);
            } elseif (preg_match('/N:(.*?)[\r\n]/i', $vcard, $matches)) {
                // N format is: Family;Given;Middle;Prefix;Suffix
                $nameParts = explode(';', trim($matches[1]));
                $row['name'] = trim(($nameParts[1] ?? '').' '.($nameParts[0] ?? ''));
            }

            // Extract email
            if (preg_match('/EMAIL[^:]*:(.*?)[\r\n]/i', $vcard, $matches)) {
                $row['email'] = trim($matches[1]);
            }

            // Extract phone
            if (preg_match('/TEL[^:]*:(.*?)[\r\n]/i', $vcard, $matches)) {
                $row['phone'] = trim($matches[1]);
            }

            // Extract address (ADR field)
            if (preg_match('/ADR[^:]*:(.*?)[\r\n]/i', $vcard, $matches)) {
                // ADR format is: POBox;Extended;Street;City;State;Zip;Country
                $addrParts = explode(';', trim($matches[1]));
                $addrComponents = array_filter([
                    $addrParts[2] ?? '', // Street
                    $addrParts[3] ?? '', // City
                    $addrParts[4] ?? '', // State
                    $addrParts[5] ?? '', // Zip
                ]);
                $row['property_address'] = trim(implode(', ', $addrComponents));
            }

            // Extract notes
            if (preg_match('/NOTE[^:]*:(.*?)[\r\n]/i', $vcard, $matches)) {
                $row['notes'] = trim($matches[1]);
            }

            // Only add if we have at least a name or phone number
            if (! empty($row['name']) || ! empty($row['phone'])) {
                // If no name but has phone, use phone as name
                if (empty($row['name']) && ! empty($row['phone'])) {
                    $row['name'] = $row['phone'];
                }
                $rows[] = $row;
            }
        }

        return $rows;
    }

    /**
     * Validate a single row
     */
    private function validateRow(array $row, int $index): \Illuminate\Validation\Validator
    {
        return Validator::make($row, [
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'property_address' => 'nullable|string|max:500',
            'notes' => 'nullable|string',
            'source_site' => 'nullable|string|max:100',
        ], [
            'name.required' => 'Row '.($index + 1).': Name is required',
            'email.email' => 'Row '.($index + 1).': Invalid email format',
        ]);
    }
}
