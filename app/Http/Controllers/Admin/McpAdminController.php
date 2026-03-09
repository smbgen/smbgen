<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class McpAdminController extends Controller
{
    /** All tools exposed by the MCP server — used to render the reference table. */
    private const TOOLS = [
        // Clients
        ['domain' => 'Clients', 'name' => 'list_clients',   'rw' => 'read',  'description' => 'List CRM clients with optional search/filter by name, email, phone, or active status.'],
        ['domain' => 'Clients', 'name' => 'get_client',     'rw' => 'read',  'description' => 'Get full details of a single client by ID.'],
        ['domain' => 'Clients', 'name' => 'create_client',  'rw' => 'write', 'description' => 'Create a new CRM client record.'],
        ['domain' => 'Clients', 'name' => 'update_client',  'rw' => 'write', 'description' => "Update an existing client's name, phone, address, notes, or active status."],
        // Leads
        ['domain' => 'Leads',   'name' => 'list_leads',     'rw' => 'read',  'description' => 'List lead form submissions with optional date-range filter or name/email search.'],
        ['domain' => 'Leads',   'name' => 'get_lead',       'rw' => 'read',  'description' => 'Get full details of a lead including all custom form data.'],
        // Bookings
        ['domain' => 'Bookings','name' => 'list_bookings',  'rw' => 'read',  'description' => 'List bookings filtered by status (pending/confirmed/cancelled), date range, or customer search.'],
        ['domain' => 'Bookings','name' => 'get_booking',    'rw' => 'read',  'description' => 'Get full details of a single booking including assigned staff.'],
        ['domain' => 'Bookings','name' => 'create_booking', 'rw' => 'write', 'description' => 'Create a new booking with customer details, date/time, duration, and optional staff assignment.'],
        ['domain' => 'Bookings','name' => 'update_booking_status', 'rw' => 'write', 'description' => 'Update a booking status to pending, confirmed, or cancelled.'],
        // CMS
        ['domain' => 'CMS',     'name' => 'list_cms_pages', 'rw' => 'read',  'description' => 'List CMS pages with optional published-only filter or title/slug search.'],
        ['domain' => 'CMS',     'name' => 'get_cms_page',   'rw' => 'read',  'description' => 'Get full content of a CMS page including body HTML and form configuration.'],
        ['domain' => 'CMS',     'name' => 'create_cms_page','rw' => 'write', 'description' => 'Create a new CMS page. Slug is auto-generated from the title if not provided.'],
        ['domain' => 'CMS',     'name' => 'update_cms_page','rw' => 'write', 'description' => 'Update a CMS page content, publish status, navbar/footer visibility, or form settings.'],
        // Users
        ['domain' => 'Users',   'name' => 'list_users',     'rw' => 'read',  'description' => 'List system users filtered by role (user/client/company_administrator).'],
        ['domain' => 'Users',   'name' => 'get_user',       'rw' => 'read',  'description' => 'Get details of a single system user including Google Calendar connection status.'],
    ];

    /**
     * The MCP server runs on the developer's local machine, not on the server.
     * MCP_SERVER_LOCAL_PATH lets each developer set the path to their local
     * mcp-server/dist/index.js. Falls back to a sensible placeholder so the
     * downloaded config is always valid JSON — just needs that one line edited.
     */
    private function localServerPath(): string
    {
        return env('MCP_SERVER_LOCAL_PATH', '/path/to/prtl7-app/mcp-server/dist/index.js');
    }

    private function buildConfigArray(): array
    {
        return [
            'mcpServers' => [
                'prtl7-crm' => [
                    'command' => 'node',
                    'args'    => [$this->localServerPath()],
                    'env'     => [
                        'PRTL7_URL'        => config('app.url'),
                        'PRTL7_MCP_SECRET' => config('app.mcp_secret') ?? '',
                    ],
                ],
            ],
        ];
    }

    public function index()
    {
        $secret = config('app.mcp_secret');
        $localPath = $this->localServerPath();
        $pathConfigured = env('MCP_SERVER_LOCAL_PATH') !== null;

        return view('admin.mcp.index', [
            'secret_configured'  => ! empty($secret),
            'secret_masked'      => $secret ? '••••••••' . substr($secret, -6) : null,
            'secret_full'        => $secret ?? null,
            'app_url'            => config('app.url'),
            'tools'              => self::TOOLS,
            'config_json'        => json_encode($this->buildConfigArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES),
            'local_path'         => $localPath,
            'path_configured'    => $pathConfigured,
            // server_built only makes sense when the path is a real local path
            'server_built'       => $pathConfigured && file_exists($localPath),
        ]);
    }

    public function downloadConfig()
    {
        return response()
            ->json($this->buildConfigArray(), 200, [], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES)
            ->header('Content-Disposition', 'attachment; filename="prtl7-mcp-config.json"');
    }

    public function rotateToken(Request $request)
    {
        $newToken = Str::random(64);

        $envPath = base_path('.env');

        if (! is_writable($envPath)) {
            return redirect()->route('admin.mcp.index')
                ->withErrors(['error' => 'Cannot write to .env. Update MCP_SECRET manually in your environment.']);
        }

        $current = file_get_contents($envPath);

        $updated = str_contains($current, 'MCP_SECRET=')
            ? preg_replace('/^MCP_SECRET=.*/m', "MCP_SECRET={$newToken}", $current)
            : $current . "\nMCP_SECRET={$newToken}\n";

        file_put_contents($envPath, $updated);

        \Illuminate\Support\Facades\Artisan::call('config:clear');

        return redirect()->route('admin.mcp.index')
            ->with('success', 'MCP secret rotated. Re-download the config and update your local Claude Code settings.');
    }
}
