<?php

use App\Http\Controllers\Mcp\BookingMcpController;
use App\Http\Controllers\Mcp\ClientMcpController;
use App\Http\Controllers\Mcp\CmsMcpController;
use App\Http\Controllers\Mcp\LeadMcpController;
use App\Http\Controllers\Mcp\McpHttpController;
use App\Http\Controllers\Mcp\UserMcpController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| MCP Routes
|--------------------------------------------------------------------------
| MCP_SECRET env var protects all routes below.
|
| Streamable HTTP endpoint (Claude Cowork / Claude Desktop custom connector):
|   POST https://yourapp.com/mcp
|   Authorization: Bearer <MCP_SECRET>
|
| Internal REST API (used by the local Node.js stdio MCP server):
|   https://yourapp.com/mcp/v1/*
|
*/

// ── Streamable HTTP MCP endpoint ────────────────────────────────────────────
// Single POST endpoint implementing MCP JSON-RPC 2024-11-05.
// Add this URL as a custom connector in Claude Cowork / Claude Desktop.
Route::middleware('mcp.auth')->post('/mcp', [McpHttpController::class, 'handle']);

// ── Internal REST API ────────────────────────────────────────────────────────
Route::middleware('mcp.auth')->prefix('mcp/v1')->group(function () {

    // Clients (CRM contacts)
    Route::get('/clients', [ClientMcpController::class, 'index']);
    Route::get('/clients/{id}', [ClientMcpController::class, 'show']);
    Route::post('/clients', [ClientMcpController::class, 'store']);
    Route::patch('/clients/{id}', [ClientMcpController::class, 'update']);

    // Leads
    Route::get('/leads', [LeadMcpController::class, 'index']);
    Route::get('/leads/{id}', [LeadMcpController::class, 'show']);

    // Bookings
    Route::get('/bookings', [BookingMcpController::class, 'index']);
    Route::get('/bookings/{id}', [BookingMcpController::class, 'show']);
    Route::post('/bookings', [BookingMcpController::class, 'store']);
    Route::patch('/bookings/{id}/status', [BookingMcpController::class, 'updateStatus']);

    // CMS Pages
    Route::get('/cms/pages', [CmsMcpController::class, 'index']);
    Route::get('/cms/pages/{id}', [CmsMcpController::class, 'show']);
    Route::post('/cms/pages', [CmsMcpController::class, 'store']);
    Route::patch('/cms/pages/{id}', [CmsMcpController::class, 'update']);

    // Users
    Route::get('/users', [UserMcpController::class, 'index']);
    Route::get('/users/{id}', [UserMcpController::class, 'show']);

});
