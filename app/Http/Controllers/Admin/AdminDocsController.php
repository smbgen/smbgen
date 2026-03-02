<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;

class AdminDocsController extends Controller
{
    public function index(Request $request)
    {
        if (! Auth::user() || ! Auth::user()->isAdministrator()) {
            abort(403);
        }

        $docsPath = base_path('app/docs');
        $files = collect(File::files($docsPath))
            ->filter(fn ($f) => Str::endsWith($f->getFilename(), '.md'))
            ->map(fn ($f) => [
                'name' => $f->getFilename(),
                'path' => $f->getRealPath(),
            ])
            ->sortBy('name')
            ->values();

        $selected = $request->query('file');
        $content = null;
        $html = null;
        $error = null;
        if ($selected) {
            $file = $files->first(fn ($f) => $f['name'] === $selected);
            if ($file) {
                try {
                    $content = File::get($file['path']);
                } catch (\Exception $e) {
                    $error = 'Unable to read file.';
                }
                if ($content !== null) {
                    if (! class_exists('Parsedown')) {
                        $parsedownPath = base_path('vendor/erusev/parsedown/Parsedown.php');
                        if (file_exists($parsedownPath)) {
                            require $parsedownPath;
                        } else {
                            $error = 'Parsedown library not found.';
                        }
                    }
                    if (class_exists('Parsedown')) {
                        try {
                            $html = (new \Parsedown)->text($content);
                        } catch (\Exception $e) {
                            $error = 'Error parsing markdown.';
                        }
                    } elseif (! $error) {
                        $error = 'Parsedown class not available.';
                    }
                }
            } else {
                $error = 'File not found.';
            }
        }

        return view('admin.docs.index', [
            'files' => $files,
            'selected' => $selected,
            'html' => $html,
            'content' => $content,
            'error' => $error,
        ]);
    }
}
