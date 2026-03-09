<?php

namespace App\Http\Controllers\Mcp;

use App\Http\Controllers\Controller;
use App\Models\Booking;
use App\Models\Client;
use App\Models\CmsPage;
use App\Models\LeadForm;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Str;

/**
 * MCP Streamable HTTP Transport
 *
 * Implements the Model Context Protocol (Streamable HTTP, 2024-11-05) as a
 * single stateless POST endpoint. All tool calls return plain JSON — no SSE
 * streaming is used since every CRM operation is a simple request/response.
 *
 * Connect in Claude: Settings → Connectors → Add custom connector
 * URL: https://yourapp.com/mcp
 * Auth header: Authorization: Bearer <MCP_SECRET>
 */
class McpHttpController extends Controller
{
    // -------------------------------------------------------------------------
    // MCP Tool Registry
    // -------------------------------------------------------------------------

    private const TOOLS = [
        // ── Clients ──────────────────────────────────────────────────────────
        [
            'name'        => 'list_clients',
            'description' => 'List CRM clients. Optionally search by name, email, or phone. Returns id, name, email, phone, property_address, is_active, last_login_at.',
            'inputSchema' => [
                'type'       => 'object',
                'properties' => [
                    'search'      => ['type' => 'string',  'description' => 'Search name, email, or phone'],
                    'active_only' => ['type' => 'boolean', 'description' => 'Return only active clients'],
                    'limit'       => ['type' => 'integer', 'description' => 'Max results (default 50)'],
                ],
            ],
        ],
        [
            'name'        => 'get_client',
            'description' => 'Get full details of a single client by ID.',
            'inputSchema' => [
                'type'       => 'object',
                'properties' => ['id' => ['type' => 'integer', 'description' => 'Client ID']],
                'required'   => ['id'],
            ],
        ],
        [
            'name'        => 'create_client',
            'description' => 'Create a new CRM client record.',
            'inputSchema' => [
                'type'       => 'object',
                'properties' => [
                    'name'             => ['type' => 'string',  'description' => 'Full name (required)'],
                    'email'            => ['type' => 'string',  'description' => 'Email address — must be unique'],
                    'phone'            => ['type' => 'string',  'description' => 'Phone number'],
                    'property_address' => ['type' => 'string',  'description' => 'Service / property address'],
                    'notes'            => ['type' => 'string',  'description' => 'Internal notes'],
                    'is_active'        => ['type' => 'boolean', 'description' => 'Active status (default true)'],
                ],
                'required' => ['name', 'email'],
            ],
        ],
        [
            'name'        => 'update_client',
            'description' => "Update an existing client's phone, address, notes, or active status.",
            'inputSchema' => [
                'type'       => 'object',
                'properties' => [
                    'id'               => ['type' => 'integer', 'description' => 'Client ID (required)'],
                    'name'             => ['type' => 'string',  'description' => 'Full name'],
                    'phone'            => ['type' => 'string',  'description' => 'Phone number'],
                    'property_address' => ['type' => 'string',  'description' => 'Service / property address'],
                    'notes'            => ['type' => 'string',  'description' => 'Internal notes'],
                    'is_active'        => ['type' => 'boolean', 'description' => 'Active status'],
                ],
                'required' => ['id'],
            ],
        ],

        // ── Leads ─────────────────────────────────────────────────────────────
        [
            'name'        => 'list_leads',
            'description' => 'List lead form submissions. Filter by date range or search by name/email.',
            'inputSchema' => [
                'type'       => 'object',
                'properties' => [
                    'search' => ['type' => 'string',  'description' => 'Search name or email'],
                    'from'   => ['type' => 'string',  'description' => 'Start date YYYY-MM-DD'],
                    'to'     => ['type' => 'string',  'description' => 'End date YYYY-MM-DD'],
                    'limit'  => ['type' => 'integer', 'description' => 'Max results (default 50)'],
                ],
            ],
        ],
        [
            'name'        => 'get_lead',
            'description' => 'Get full details of a lead submission including all custom form data.',
            'inputSchema' => [
                'type'       => 'object',
                'properties' => ['id' => ['type' => 'integer', 'description' => 'Lead ID']],
                'required'   => ['id'],
            ],
        ],

        // ── Bookings ──────────────────────────────────────────────────────────
        [
            'name'        => 'list_bookings',
            'description' => 'List bookings. Filter by status (pending/confirmed/cancelled), date range, or customer search.',
            'inputSchema' => [
                'type'       => 'object',
                'properties' => [
                    'status' => ['type' => 'string',  'description' => 'pending | confirmed | cancelled'],
                    'from'   => ['type' => 'string',  'description' => 'Start date YYYY-MM-DD'],
                    'to'     => ['type' => 'string',  'description' => 'End date YYYY-MM-DD'],
                    'search' => ['type' => 'string',  'description' => 'Search customer name or email'],
                    'limit'  => ['type' => 'integer', 'description' => 'Max results (default 50)'],
                ],
            ],
        ],
        [
            'name'        => 'get_booking',
            'description' => 'Get full details of a single booking including assigned staff.',
            'inputSchema' => [
                'type'       => 'object',
                'properties' => ['id' => ['type' => 'integer', 'description' => 'Booking ID']],
                'required'   => ['id'],
            ],
        ],
        [
            'name'        => 'create_booking',
            'description' => 'Create a new booking.',
            'inputSchema' => [
                'type'       => 'object',
                'properties' => [
                    'customer_name'    => ['type' => 'string',  'description' => 'Customer full name (required)'],
                    'customer_email'   => ['type' => 'string',  'description' => 'Customer email (required)'],
                    'customer_phone'   => ['type' => 'string',  'description' => 'Customer phone'],
                    'booking_date'     => ['type' => 'string',  'description' => 'Date YYYY-MM-DD (required)'],
                    'booking_time'     => ['type' => 'string',  'description' => 'Time HH:MM 24h (required)'],
                    'duration'         => ['type' => 'integer', 'description' => 'Duration in minutes (default 30)'],
                    'property_address' => ['type' => 'string',  'description' => 'Service address'],
                    'notes'            => ['type' => 'string',  'description' => 'Internal notes'],
                    'staff_id'         => ['type' => 'integer', 'description' => 'Assigned staff user ID'],
                ],
                'required' => ['customer_name', 'customer_email', 'booking_date', 'booking_time'],
            ],
        ],
        [
            'name'        => 'update_booking_status',
            'description' => 'Update a booking status to pending, confirmed, or cancelled.',
            'inputSchema' => [
                'type'       => 'object',
                'properties' => [
                    'id'     => ['type' => 'integer', 'description' => 'Booking ID (required)'],
                    'status' => ['type' => 'string',  'description' => 'pending | confirmed | cancelled (required)'],
                    'notes'  => ['type' => 'string',  'description' => 'Optional notes about the change'],
                ],
                'required' => ['id', 'status'],
            ],
        ],

        // ── CMS Pages ─────────────────────────────────────────────────────────
        [
            'name'        => 'list_cms_pages',
            'description' => 'List CMS pages. Optionally filter to published only or search by title/slug.',
            'inputSchema' => [
                'type'       => 'object',
                'properties' => [
                    'published_only' => ['type' => 'boolean', 'description' => 'Only return published pages'],
                    'search'         => ['type' => 'string',  'description' => 'Search title or slug'],
                    'limit'          => ['type' => 'integer', 'description' => 'Max results (default 50)'],
                ],
            ],
        ],
        [
            'name'        => 'get_cms_page',
            'description' => 'Get full content of a CMS page including body HTML and form configuration.',
            'inputSchema' => [
                'type'       => 'object',
                'properties' => ['id' => ['type' => 'integer', 'description' => 'Page ID']],
                'required'   => ['id'],
            ],
        ],
        [
            'name'        => 'create_cms_page',
            'description' => 'Create a new CMS page. Slug is auto-generated from the title if omitted.',
            'inputSchema' => [
                'type'       => 'object',
                'properties' => [
                    'title'                   => ['type' => 'string',  'description' => 'Page title (required)'],
                    'slug'                    => ['type' => 'string',  'description' => 'URL slug (auto-generated if omitted)'],
                    'body_content'            => ['type' => 'string',  'description' => 'Main HTML body content'],
                    'head_content'            => ['type' => 'string',  'description' => 'HTML injected into <head>'],
                    'cta_text'                => ['type' => 'string',  'description' => 'Call-to-action button label'],
                    'cta_url'                 => ['type' => 'string',  'description' => 'Call-to-action URL'],
                    'is_published'            => ['type' => 'boolean', 'description' => 'Publish immediately (default false)'],
                    'show_navbar'             => ['type' => 'boolean', 'description' => 'Show site navbar (default true)'],
                    'show_footer'             => ['type' => 'boolean', 'description' => 'Show site footer (default true)'],
                    'has_form'                => ['type' => 'boolean', 'description' => 'Include a lead capture form'],
                    'notification_email'      => ['type' => 'string',  'description' => 'Email for form submission alerts'],
                    'form_submit_button_text' => ['type' => 'string',  'description' => 'Form submit button label'],
                    'form_success_message'    => ['type' => 'string',  'description' => 'Message shown after form submission'],
                ],
                'required' => ['title'],
            ],
        ],
        [
            'name'        => 'update_cms_page',
            'description' => 'Update an existing CMS page content, publish status, or form settings.',
            'inputSchema' => [
                'type'       => 'object',
                'properties' => [
                    'id'                      => ['type' => 'integer', 'description' => 'Page ID (required)'],
                    'title'                   => ['type' => 'string',  'description' => 'Page title'],
                    'body_content'            => ['type' => 'string',  'description' => 'Main HTML body content'],
                    'head_content'            => ['type' => 'string',  'description' => 'HTML injected into <head>'],
                    'cta_text'                => ['type' => 'string',  'description' => 'CTA button label'],
                    'cta_url'                 => ['type' => 'string',  'description' => 'CTA URL'],
                    'is_published'            => ['type' => 'boolean', 'description' => 'Publish or unpublish'],
                    'show_navbar'             => ['type' => 'boolean', 'description' => 'Show site navbar'],
                    'show_footer'             => ['type' => 'boolean', 'description' => 'Show site footer'],
                    'has_form'                => ['type' => 'boolean', 'description' => 'Toggle lead capture form'],
                    'notification_email'      => ['type' => 'string',  'description' => 'Email for form alerts'],
                    'form_submit_button_text' => ['type' => 'string',  'description' => 'Form submit button label'],
                    'form_success_message'    => ['type' => 'string',  'description' => 'Post-submit message'],
                ],
                'required' => ['id'],
            ],
        ],

        // ── Users ─────────────────────────────────────────────────────────────
        [
            'name'        => 'list_users',
            'description' => 'List system users. Filter by role: user | client | company_administrator.',
            'inputSchema' => [
                'type'       => 'object',
                'properties' => [
                    'role'   => ['type' => 'string',  'description' => 'user | client | company_administrator'],
                    'search' => ['type' => 'string',  'description' => 'Search name or email'],
                    'limit'  => ['type' => 'integer', 'description' => 'Max results (default 50)'],
                ],
            ],
        ],
        [
            'name'        => 'get_user',
            'description' => 'Get details of a single system user by ID.',
            'inputSchema' => [
                'type'       => 'object',
                'properties' => ['id' => ['type' => 'integer', 'description' => 'User ID']],
                'required'   => ['id'],
            ],
        ],
    ];

    // -------------------------------------------------------------------------
    // Entry point
    // -------------------------------------------------------------------------

    public function handle(Request $request): JsonResponse|Response
    {
        $body   = $request->json()->all();
        $method = $body['method'] ?? '';
        $id     = $body['id'] ?? null;       // null means it's a notification
        $params = $body['params'] ?? [];

        // Notifications have no id and require no response body
        if ($id === null && in_array($method, ['notifications/initialized', 'notifications/cancelled'], true)) {
            return response()->noContent();
        }

        return match ($method) {
            'initialize'  => $this->rpcInitialize($id, $params),
            'tools/list'  => $this->rpcToolsList($id),
            'tools/call'  => $this->rpcToolsCall($id, $params),
            'ping'        => $this->rpcPong($id),
            default       => $this->rpcError($id, -32601, "Method not found: {$method}"),
        };
    }

    // -------------------------------------------------------------------------
    // MCP protocol handlers
    // -------------------------------------------------------------------------

    private function rpcInitialize(mixed $id, array $params): JsonResponse
    {
        return $this->rpcOk($id, [
            'protocolVersion' => '2024-11-05',
            'capabilities'    => ['tools' => ['listChanged' => false]],
            'serverInfo'      => ['name' => 'prtl7-crm', 'version' => '1.0.0'],
        ]);
    }

    private function rpcToolsList(mixed $id): JsonResponse
    {
        return $this->rpcOk($id, ['tools' => self::TOOLS]);
    }

    private function rpcToolsCall(mixed $id, array $params): JsonResponse
    {
        $name = $params['name'] ?? '';
        $args = $params['arguments'] ?? [];

        try {
            $data = $this->dispatchTool($name, $args);

            return $this->rpcOk($id, [
                'content' => [['type' => 'text', 'text' => json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)]],
            ]);
        } catch (\InvalidArgumentException $e) {
            return $this->rpcError($id, -32602, $e->getMessage());
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
            return $this->rpcOk($id, [
                'content' => [['type' => 'text', 'text' => 'Not found.']],
                'isError' => true,
            ]);
        } catch (\Throwable $e) {
            return $this->rpcOk($id, [
                'content' => [['type' => 'text', 'text' => 'Error: ' . $e->getMessage()]],
                'isError' => true,
            ]);
        }
    }

    private function rpcPong(mixed $id): JsonResponse
    {
        return $this->rpcOk($id, []);
    }

    // -------------------------------------------------------------------------
    // Tool dispatch
    // -------------------------------------------------------------------------

    private function dispatchTool(string $name, array $a): array
    {
        return match ($name) {
            // Clients
            'list_clients'  => $this->listClients($a),
            'get_client'    => $this->getClient($a),
            'create_client' => $this->createClient($a),
            'update_client' => $this->updateClient($a),
            // Leads
            'list_leads'    => $this->listLeads($a),
            'get_lead'      => $this->getLead($a),
            // Bookings
            'list_bookings'        => $this->listBookings($a),
            'get_booking'          => $this->getBooking($a),
            'create_booking'       => $this->createBooking($a),
            'update_booking_status'=> $this->updateBookingStatus($a),
            // CMS
            'list_cms_pages'  => $this->listCmsPages($a),
            'get_cms_page'    => $this->getCmsPage($a),
            'create_cms_page' => $this->createCmsPage($a),
            'update_cms_page' => $this->updateCmsPage($a),
            // Users
            'list_users' => $this->listUsers($a),
            'get_user'   => $this->getUser($a),

            default => throw new \InvalidArgumentException("Unknown tool: {$name}"),
        };
    }

    // -------------------------------------------------------------------------
    // Client tools
    // -------------------------------------------------------------------------

    private function listClients(array $a): array
    {
        $q = Client::query();

        if ($search = $a['search'] ?? null) {
            $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%")
                ->orWhere('phone', 'like', "%{$search}%"));
        }

        if ($a['active_only'] ?? false) {
            $q->where('is_active', true);
        }

        $clients = $q->orderByDesc('created_at')
            ->limit($a['limit'] ?? 50)
            ->get(['id', 'name', 'email', 'phone', 'property_address', 'is_active', 'last_login_at', 'created_at']);

        return ['count' => $clients->count(), 'clients' => $clients->toArray()];
    }

    private function getClient(array $a): array
    {
        $client = Client::findOrFail($a['id']);

        return [
            'client'               => $client->toArray(),
            'provisioning_status'  => $client->provisioning_status_label,
            'has_google'           => $client->hasGoogleLinked(),
        ];
    }

    private function createClient(array $a): array
    {
        $this->require($a, ['name', 'email']);

        $client = Client::create([
            'name'             => $a['name'],
            'email'            => $a['email'],
            'phone'            => $a['phone'] ?? null,
            'property_address' => $a['property_address'] ?? null,
            'notes'            => $a['notes'] ?? null,
            'is_active'        => $a['is_active'] ?? true,
        ]);

        return ['client' => $client->toArray()];
    }

    private function updateClient(array $a): array
    {
        $this->require($a, ['id']);
        $client = Client::findOrFail($a['id']);

        $client->update(array_filter([
            'name'             => $a['name'] ?? null,
            'phone'            => $a['phone'] ?? null,
            'property_address' => $a['property_address'] ?? null,
            'notes'            => $a['notes'] ?? null,
            'is_active'        => $a['is_active'] ?? null,
        ], fn ($v) => $v !== null));

        return ['client' => $client->fresh()->toArray()];
    }

    // -------------------------------------------------------------------------
    // Lead tools
    // -------------------------------------------------------------------------

    private function listLeads(array $a): array
    {
        $q = LeadForm::with('cmsPage:id,title,slug');

        if ($search = $a['search'] ?? null) {
            $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        if ($from = $a['from'] ?? null) $q->whereDate('created_at', '>=', $from);
        if ($to   = $a['to']   ?? null) $q->whereDate('created_at', '<=', $to);

        $leads = $q->orderByDesc('created_at')->limit($a['limit'] ?? 50)->get();

        return ['count' => $leads->count(), 'leads' => $leads->toArray()];
    }

    private function getLead(array $a): array
    {
        $this->require($a, ['id']);

        return ['lead' => LeadForm::with('cmsPage:id,title,slug')->findOrFail($a['id'])->toArray()];
    }

    // -------------------------------------------------------------------------
    // Booking tools
    // -------------------------------------------------------------------------

    private function listBookings(array $a): array
    {
        $q = Booking::with('staff:id,name,email');

        if ($status = $a['status'] ?? null) $q->where('status', $status);
        if ($from   = $a['from']   ?? null) $q->whereDate('booking_date', '>=', $from);
        if ($to     = $a['to']     ?? null) $q->whereDate('booking_date', '<=', $to);

        if ($search = $a['search'] ?? null) {
            $q->where(fn ($q) => $q
                ->where('customer_name', 'like', "%{$search}%")
                ->orWhere('customer_email', 'like', "%{$search}%"));
        }

        $bookings = $q->orderByDesc('booking_date')
            ->orderByDesc('booking_time')
            ->limit($a['limit'] ?? 50)
            ->get();

        return ['count' => $bookings->count(), 'bookings' => $bookings->toArray()];
    }

    private function getBooking(array $a): array
    {
        $this->require($a, ['id']);

        return ['booking' => Booking::with(['staff:id,name,email', 'user:id,name,email'])->findOrFail($a['id'])->toArray()];
    }

    private function createBooking(array $a): array
    {
        $this->require($a, ['customer_name', 'customer_email', 'booking_date', 'booking_time']);

        $booking = Booking::create([
            'customer_name'    => $a['customer_name'],
            'customer_email'   => $a['customer_email'],
            'customer_phone'   => $a['customer_phone'] ?? null,
            'booking_date'     => $a['booking_date'],
            'booking_time'     => $a['booking_time'],
            'duration'         => $a['duration'] ?? 30,
            'property_address' => $a['property_address'] ?? null,
            'notes'            => $a['notes'] ?? null,
            'staff_id'         => $a['staff_id'] ?? null,
        ]);

        return ['booking' => $booking->fresh(['staff'])->toArray()];
    }

    private function updateBookingStatus(array $a): array
    {
        $this->require($a, ['id', 'status']);

        if (! in_array($a['status'], ['pending', 'confirmed', 'cancelled'])) {
            throw new \InvalidArgumentException("status must be pending, confirmed, or cancelled");
        }

        $booking = Booking::findOrFail($a['id']);
        $booking->update(array_filter([
            'status' => $a['status'],
            'notes'  => $a['notes'] ?? null,
        ], fn ($v) => $v !== null));

        return ['booking' => $booking->fresh()->toArray()];
    }

    // -------------------------------------------------------------------------
    // CMS tools
    // -------------------------------------------------------------------------

    private function listCmsPages(array $a): array
    {
        $q = CmsPage::query();

        if ($a['published_only'] ?? false) $q->published();

        if ($search = $a['search'] ?? null) {
            $q->where(fn ($q) => $q
                ->where('title', 'like', "%{$search}%")
                ->orWhere('slug', 'like', "%{$search}%"));
        }

        $pages = $q->orderByDesc('updated_at')
            ->limit($a['limit'] ?? 50)
            ->get(['id', 'slug', 'title', 'is_published', 'has_form', 'show_navbar', 'show_footer', 'updated_at']);

        return ['count' => $pages->count(), 'pages' => $pages->toArray()];
    }

    private function getCmsPage(array $a): array
    {
        $this->require($a, ['id']);

        return ['page' => CmsPage::findOrFail($a['id'])->toArray()];
    }

    private function createCmsPage(array $a): array
    {
        $this->require($a, ['title']);

        // Auto-generate slug
        $slug = $a['slug'] ?? null;
        if (empty($slug)) {
            $base = Str::slug($a['title']);
            $slug = $base;
            $i = 1;
            while (CmsPage::where('slug', $slug)->exists()) {
                $slug = "{$base}-{$i}";
                $i++;
            }
        }

        $page = CmsPage::create([
            'title'                   => $a['title'],
            'slug'                    => $slug,
            'body_content'            => $a['body_content'] ?? null,
            'head_content'            => $a['head_content'] ?? null,
            'cta_text'                => $a['cta_text'] ?? null,
            'cta_url'                 => $a['cta_url'] ?? null,
            'is_published'            => $a['is_published'] ?? false,
            'show_navbar'             => $a['show_navbar'] ?? true,
            'show_footer'             => $a['show_footer'] ?? true,
            'has_form'                => $a['has_form'] ?? false,
            'notification_email'      => $a['notification_email'] ?? null,
            'form_submit_button_text' => $a['form_submit_button_text'] ?? null,
            'form_success_message'    => $a['form_success_message'] ?? null,
        ]);

        return ['page' => $page->toArray()];
    }

    private function updateCmsPage(array $a): array
    {
        $this->require($a, ['id']);

        $page = CmsPage::findOrFail($a['id']);

        $fillable = ['title', 'body_content', 'head_content', 'cta_text', 'cta_url',
                     'is_published', 'show_navbar', 'show_footer', 'has_form',
                     'notification_email', 'form_submit_button_text', 'form_success_message'];

        $updates = [];
        foreach ($fillable as $field) {
            if (array_key_exists($field, $a)) {
                $updates[$field] = $a[$field];
            }
        }

        $page->update($updates);

        return ['page' => $page->fresh()->toArray()];
    }

    // -------------------------------------------------------------------------
    // User tools
    // -------------------------------------------------------------------------

    private function listUsers(array $a): array
    {
        $q = User::query();

        if ($role   = $a['role']   ?? null) $q->where('role', $role);
        if ($search = $a['search'] ?? null) {
            $q->where(fn ($q) => $q
                ->where('name', 'like', "%{$search}%")
                ->orWhere('email', 'like', "%{$search}%"));
        }

        $users = $q->orderByDesc('created_at')
            ->limit($a['limit'] ?? 50)
            ->get(['id', 'name', 'email', 'role', 'email_verified_at', 'created_at']);

        return ['count' => $users->count(), 'users' => $users->toArray()];
    }

    private function getUser(array $a): array
    {
        $this->require($a, ['id']);
        $user = User::findOrFail($a['id']);

        return [
            'user'                 => $user->only(['id', 'name', 'email', 'role', 'email_verified_at', 'created_at', 'updated_at']),
            'has_google_calendar'  => $user->hasGoogleCalendar(),
            'is_administrator'     => $user->isAdministrator(),
            'is_client'            => $user->isClient(),
        ];
    }

    // -------------------------------------------------------------------------
    // Helpers
    // -------------------------------------------------------------------------

    /** Throw if any required keys are missing from the args array. */
    private function require(array $a, array $keys): void
    {
        $missing = array_filter($keys, fn ($k) => ! array_key_exists($k, $a) || $a[$k] === null || $a[$k] === '');
        if ($missing) {
            throw new \InvalidArgumentException('Missing required arguments: ' . implode(', ', $missing));
        }
    }

    private function rpcOk(mixed $id, array $result): JsonResponse
    {
        return response()->json(['jsonrpc' => '2.0', 'id' => $id, 'result' => $result]);
    }

    private function rpcError(mixed $id, int $code, string $message): JsonResponse
    {
        return response()->json([
            'jsonrpc' => '2.0',
            'id'      => $id,
            'error'   => ['code' => $code, 'message' => $message],
        ]);
    }
}
