<?php

namespace App\Http\Controllers;

use App\Services\EmailTrackingService;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class EmailTrackingController extends Controller
{
    protected EmailTrackingService $trackingService;

    public function __construct(EmailTrackingService $trackingService)
    {
        $this->trackingService = $trackingService;
    }

    /**
     * Track email open via pixel
     * Public route - no authentication required
     */
    public function trackOpen(Request $request, string $trackingId): Response
    {
        // Record the open event
        $this->trackingService->recordOpen($trackingId, [
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        // Return a transparent 1x1 GIF image
        $gif = base64_decode('R0lGODlhAQABAIAAAAAAAP///yH5BAEAAAAALAAAAAABAAEAAAIBRAA7');

        return response($gif)
            ->header('Content-Type', 'image/gif')
            ->header('Content-Length', strlen($gif))
            ->header('Cache-Control', 'no-cache, no-store, must-revalidate, private')
            ->header('Pragma', 'no-cache')
            ->header('Expires', '0');
    }

    /**
     * Track email link click and redirect
     * Public route - no authentication required
     */
    public function trackClick(Request $request, string $trackingId)
    {
        // Record the click event
        $this->trackingService->recordClick($trackingId);

        // Get the original URL from query parameter
        $originalUrl = base64_decode($request->query('url', ''));

        // Validate URL
        if (empty($originalUrl) || ! filter_var($originalUrl, FILTER_VALIDATE_URL)) {
            abort(404);
        }

        // Redirect to the original URL
        return redirect()->away($originalUrl);
    }
}
