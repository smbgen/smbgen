<?php

namespace App\Services;

use App\Models\InspectionReport;
use Google_Client;
use Google_Service_Drive;
use Google_Service_Drive_DriveFile;
use Illuminate\Support\Facades\Log;

class GoogleDriveService
{
    protected $client;

    protected $service;

    public function __construct()
    {
        if (! class_exists('\Google_Client')) {
            throw new \RuntimeException('Google API client not installed. Run: composer require google/apiclient');
        }

        $this->client = new Google_Client;
        $this->client->setClientId(config('services.google.client_id'));
        $this->client->setClientSecret(config('services.google.client_secret'));
        $this->client->setAccessType('offline');
    }

    /**
     * Store an inspection report to Google Drive
     *
     * @param  string  $refreshToken  User's Google refresh token
     * @param  string  $folderId  Parent folder ID (optional)
     * @return array ['success' => bool, 'file_id' => string|null, 'error' => string|null]
     */
    public function storeInspectionReport(InspectionReport $report, string $refreshToken, ?string $folderId = null): array
    {
        try {
            // Refresh the access token
            $token = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);

            if (isset($token['error'])) {
                Log::error('Google Drive token refresh failed', [
                    'error' => $token['error_description'] ?? $token['error'],
                ]);

                return [
                    'success' => false,
                    'file_id' => null,
                    'error' => 'Token refresh failed: '.($token['error_description'] ?? $token['error']),
                ];
            }

            $this->service = new Google_Service_Drive($this->client);

            // Generate the report content as HTML
            $content = $this->generateReportHtml($report);

            // Create file metadata
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $this->generateFileName($report),
                'mimeType' => 'application/vnd.google-apps.document', // Google Docs format
            ]);

            // If folder ID is provided, set the parent
            if ($folderId) {
                $fileMetadata->setParents([$folderId]);
            }

            // Upload the file
            $file = $this->service->files->create(
                $fileMetadata,
                [
                    'data' => $content,
                    'mimeType' => 'text/html',
                    'uploadType' => 'multipart',
                    'fields' => 'id, name, webViewLink',
                ]
            );

            Log::info('Inspection report stored to Google Drive', [
                'report_id' => $report->id,
                'file_id' => $file->id,
                'file_name' => $file->name,
            ]);

            // Update the report with Google Drive file ID
            $report->google_drive_file_id = $file->id;
            $report->google_drive_link = $file->webViewLink;
            $report->save();

            return [
                'success' => true,
                'file_id' => $file->id,
                'web_link' => $file->webViewLink,
                'error' => null,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to store inspection report to Google Drive', [
                'report_id' => $report->id,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            return [
                'success' => false,
                'file_id' => null,
                'error' => $e->getMessage(),
            ];
        }
    }

    /**
     * Generate HTML content for the inspection report
     */
    protected function generateReportHtml(InspectionReport $report): string
    {
        $html = '<html><head><meta charset="UTF-8"><style>';
        $html .= 'body { font-family: Arial, sans-serif; line-height: 1.6; max-width: 800px; margin: 0 auto; padding: 20px; }';
        $html .= 'h1 { color: #2c3e50; border-bottom: 3px solid #3498db; padding-bottom: 10px; }';
        $html .= 'h2 { color: #34495e; margin-top: 30px; }';
        $html .= '.info { background: #ecf0f1; padding: 15px; border-radius: 5px; margin: 20px 0; }';
        $html .= '.info p { margin: 5px 0; }';
        $html .= '.section { margin: 20px 0; }';
        $html .= '</style></head><body>';

        $html .= '<h1>Inspection Report: '.$this->escapeHtml($report->summary_title).'</h1>';

        $html .= '<div class="info">';
        $html .= '<p><strong>Client:</strong> '.$this->escapeHtml($report->client_name).'</p>';
        if ($report->client_email) {
            $html .= '<p><strong>Email:</strong> '.$this->escapeHtml($report->client_email).'</p>';
        }
        if ($report->client_phone) {
            $html .= '<p><strong>Phone:</strong> '.$this->escapeHtml($report->client_phone).'</p>';
        }
        if ($report->client_address) {
            $html .= '<p><strong>Address:</strong> '.$this->escapeHtml($report->client_address).'</p>';
        }
        if ($report->consult_date) {
            $html .= '<p><strong>Consultation Date:</strong> '.$report->consult_date->format('F j, Y g:i A').'</p>';
        }
        $html .= '<p><strong>Report Created:</strong> '.$report->created_at->format('F j, Y g:i A').'</p>';
        $html .= '</div>';

        if ($report->body_explanation) {
            $html .= '<div class="section">';
            $html .= '<h2>Explanation</h2>';
            $html .= '<p>'.$this->escapeHtml($report->body_explanation).'</p>';
            $html .= '</div>';
        }

        if ($report->body_suggested_actions) {
            $html .= '<div class="section">';
            $html .= '<h2>Suggested Actions</h2>';
            $html .= '<p>'.$this->escapeHtml($report->body_suggested_actions).'</p>';
            $html .= '</div>';
        }

        $html .= '</body></html>';

        return $html;
    }

    /**
     * Generate a standardized filename for the report
     */
    protected function generateFileName(InspectionReport $report): string
    {
        $date = $report->consult_date ? $report->consult_date->format('Y-m-d') : $report->created_at->format('Y-m-d');
        $clientName = preg_replace('/[^a-zA-Z0-9_-]/', '_', $report->client_name);

        return "Inspection_Report_{$date}_{$clientName}.html";
    }

    /**
     * Escape HTML entities
     */
    protected function escapeHtml(string $text): string
    {
        return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
    }

    /**
     * Find or create the "Inspection Reports" folder in Google Drive
     *
     * @param  string  $refreshToken  User's Google refresh token
     * @return array ['success' => bool, 'folder_id' => string|null, 'error' => string|null]
     */
    public function findOrCreateInspectionReportsFolder(string $refreshToken): array
    {
        try {
            // Refresh the access token
            $token = $this->client->fetchAccessTokenWithRefreshToken($refreshToken);

            if (isset($token['error'])) {
                return [
                    'success' => false,
                    'folder_id' => null,
                    'error' => 'Token refresh failed: '.($token['error_description'] ?? $token['error']),
                ];
            }

            $this->service = new Google_Service_Drive($this->client);

            // Search for existing folder
            $folderName = 'Inspection Reports';
            $query = "name = '{$folderName}' and mimeType = 'application/vnd.google-apps.folder' and trashed = false";
            $results = $this->service->files->listFiles([
                'q' => $query,
                'spaces' => 'drive',
                'fields' => 'files(id, name)',
            ]);

            if (count($results->getFiles()) > 0) {
                // Folder exists, return the first one
                $folder = $results->getFiles()[0];

                return [
                    'success' => true,
                    'folder_id' => $folder->id,
                    'error' => null,
                ];
            }

            // Folder doesn't exist, create it
            $fileMetadata = new Google_Service_Drive_DriveFile([
                'name' => $folderName,
                'mimeType' => 'application/vnd.google-apps.folder',
            ]);

            $folder = $this->service->files->create($fileMetadata, [
                'fields' => 'id',
            ]);

            Log::info('Created Inspection Reports folder in Google Drive', [
                'folder_id' => $folder->id,
            ]);

            return [
                'success' => true,
                'folder_id' => $folder->id,
                'error' => null,
            ];

        } catch (\Exception $e) {
            Log::error('Failed to find or create Inspection Reports folder', [
                'error' => $e->getMessage(),
            ]);

            return [
                'success' => false,
                'folder_id' => null,
                'error' => $e->getMessage(),
            ];
        }
    }
}
