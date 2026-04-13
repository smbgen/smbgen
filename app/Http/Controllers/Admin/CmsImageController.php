<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\CmsImage;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CmsImageController extends Controller
{
    /**
     * API endpoint for TinyMCE integration
     */
    public function api(Request $request)
    {
        $images = CmsImage::orderByDesc('created_at')
            ->limit(100)
            ->get()
            ->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->getUrl(),
                    'alt_text' => $image->alt_text,
                    'original_name' => $image->original_name,
                    'width' => $image->width,
                    'height' => $image->height,
                    'size' => $image->formatted_size,
                ];
            });

        return response()->json(['images' => $images]);
    }

    /**
     * Display a listing of CMS images
     */
    public function index(Request $request)
    {
        $query = CmsImage::query();

        // Filter by search term
        if ($request->has('search') && ! empty($request->search)) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('original_name', 'like', "%{$search}%")
                    ->orWhere('alt_text', 'like', "%{$search}%");
            });
        }

        // Filter by file type
        if ($request->has('type') && ! empty($request->type)) {
            $query->where('mime_type', 'like', $request->type.'/%');
        }

        // Sort options
        $sortBy = $request->get('sort', 'newest');
        switch ($sortBy) {
            case 'oldest':
                $query->orderBy('created_at', 'asc');
                break;
            case 'name':
                $query->orderBy('original_name', 'asc');
                break;
            case 'size':
                $query->orderBy('size', 'desc');
                break;
            case 'newest':
            default:
                $query->orderBy('created_at', 'desc');
                break;
        }

        $images = $query->paginate(20)->withQueryString();

        return view('admin.cms.images.index', compact('images'));
    }

    /**
     * Show the form for creating a new resource
     */
    public function create()
    {
        return view('admin.cms.images.create');
    }

    /**
     * Store a newly uploaded image
     */
    public function store(Request $request)
    {
        $request->validate([
            'images' => 'required|array|min:1|max:50', // Allow up to 50 images at once
            'images.*' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp|max:5120', // 5MB max each
            'bulk_alt_text' => 'nullable|string|max:255',
            'alt_texts' => 'nullable|array',
            'alt_texts.*' => 'nullable|string|max:255',
        ]);

        $uploadedImages = [];
        $files = $request->file('images');
        $bulkAltText = $request->bulk_alt_text;
        $altTexts = $request->alt_texts ?? [];

        foreach ($files as $index => $file) {
            // Generate unique filename
            $filename = 'cms-'.time().'-'.Str::random(10).'.'.$file->getClientOriginalExtension();

            // Store in public_cloud disk with public visibility for S3 access
            $disk = Storage::disk('public_cloud');
            $path = $disk->putFileAs('cms/images', $file, $filename, 'public');

            // Get file metadata
            $mimeType = $file->getMimeType();
            $fileSize = $file->getSize();

            // Get dimensions if possible
            $width = null;
            $height = null;
            try {
                $imageInfo = getimagesize($file->getPathname());
                if ($imageInfo) {
                    $width = $imageInfo[0];
                    $height = $imageInfo[1];
                }
            } catch (\Exception $e) {
                // Ignore dimension extraction errors
            }

            // Determine alt text (individual or bulk)
            $altText = $altTexts[$index] ?? $bulkAltText;

            $cmsImage = CmsImage::create([
                'filename' => $filename,
                'original_name' => $file->getClientOriginalName(),
                'path' => $path,
                'mime_type' => $mimeType,
                'size' => $fileSize,
                'width' => $width,
                'height' => $height,
                'alt_text' => $altText,
                'user_id' => auth()->id(),
            ]);

            // Log activity for each image upload
            \App\Services\ActivityLogger::logFileUpload($cmsImage, [
                'type' => 'cms_image',
                'size' => $fileSize,
                'dimensions' => $width && $height ? "{$width}x{$height}" : null,
            ]);

            $uploadedImages[] = $cmsImage;
        }

        $count = count($uploadedImages);

        return redirect()->route('admin.cms.images.index')
            ->with('success', $count === 1
                ? 'Image uploaded successfully.'
                : "{$count} images uploaded successfully.");
    }

    /**
     * Display the specified image
     */
    public function show(CmsImage $image)
    {
        return view('admin.cms.images.show', compact('image'));
    }

    /**
     * Show the form for editing the specified image
     */
    public function edit(CmsImage $image)
    {
        return view('admin.cms.images.edit', compact('image'));
    }

    /**
     * Update the specified image
     */
    public function update(Request $request, CmsImage $image)
    {
        $request->validate([
            'alt_text' => 'nullable|string|max:255',
        ]);

        $image->update([
            'alt_text' => $request->alt_text,
        ]);

        return redirect()->route('admin.cms.images.index')
            ->with('success', 'Image updated successfully.');
    }

    /**
     * Remove the specified image
     */
    public function destroy(CmsImage $image)
    {
        // Log activity before deletion
        \App\Services\ActivityLogger::logFileDelete($image);

        // Delete from storage
        Storage::disk($image->getStorageDisk())->delete($image->path);

        // Delete from database
        $image->delete();

        return redirect()->route('admin.cms.images.index')
            ->with('success', 'Image deleted successfully.');
    }

    /**
     * Bulk delete images
     */
    public function bulkDelete(Request $request)
    {
        $request->validate([
            'image_ids' => 'required|array|min:1',
            'image_ids.*' => 'required|exists:cms_images,id',
        ]);

        $imageIds = $request->image_ids;
        $images = CmsImage::whereIn('id', $imageIds)->get();

        $deletedCount = 0;
        foreach ($images as $image) {
            // Only allow deleting images uploaded by the current user or if admin
            if ($image->user_id === auth()->id() || auth()->user()->is_admin) {
                // Log activity before deletion
                \App\Services\ActivityLogger::logFileDelete($image);

                Storage::disk($image->getStorageDisk())->delete($image->path);
                $image->delete();
                $deletedCount++;
            }
        }

        return redirect()->route('admin.cms.images.index')
            ->with('success', "{$deletedCount} image(s) deleted successfully.");
    }

    /**
     * API endpoint to get images for TinyMCE or other editors
     */
    public function apiList(Request $request)
    {
        $images = CmsImage::latest()
            ->when($request->search, function ($query) use ($request) {
                $query->where('original_name', 'like', '%'.$request->search.'%');
            })
            ->paginate(12);

        return response()->json([
            'images' => $images->map(function ($image) {
                return [
                    'id' => $image->id,
                    'url' => $image->getUrl(),
                    'thumbnail' => $image->getUrl(), // For now, same as full image
                    'name' => $image->original_name,
                    'size' => $image->formatted_size,
                    'uploaded_at' => $image->created_at->format('M j, Y'),
                ];
            }),
            'pagination' => [
                'current_page' => $images->currentPage(),
                'last_page' => $images->lastPage(),
                'per_page' => $images->perPage(),
                'total' => $images->total(),
            ],
        ]);
    }

    /**
     * API endpoint to get recent uploads for the upload form
     */
    public function apiRecent()
    {
        $recentImages = CmsImage::latest()
            ->where('user_id', auth()->id())
            ->limit(12)
            ->get();

        return response()->json($recentImages->map(function ($image) {
            return [
                'id' => $image->id,
                'url' => $image->getUrl(),
                'filename' => $image->original_name,
                'size' => $image->formatted_size,
                'uploaded_at' => $image->created_at->diffForHumans(),
            ];
        }));
    }
}
