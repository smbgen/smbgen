@extends('layouts.admin')

@section('content')
<div class="py-6">
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title flex items-center gap-3">
                <i class="fas fa-plug text-primary-400"></i>
                Claude MCP Server
            </h1>
            <p class="admin-page-subtitle">Connect this CRM to Claude Cowork, Claude Desktop, or Claude Code via the Model Context Protocol</p>
        </div>
        <div class="flex items-center gap-3">
            <a href="{{ route('admin.mcp.download') }}" class="btn btn-primary">
                <i class="fas fa-download mr-2"></i>Download Stdio Config
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-100 dark:bg-green-900 border border-green-400 dark:border-green-600 text-green-700 dark:text-green-200 px-4 py-3 rounded mb-6">
            <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
        </div>
    @endif

    {{-- ── Hosted endpoint hero ──────────────────────────────────────────── --}}
    <div class="bg-gradient-to-r from-primary-700 to-primary-600 rounded-xl p-6 mb-6 text-white">
        <div class="flex items-start justify-between gap-4 flex-wrap">
            <div>
                <p class="text-primary-200 text-xs font-semibold uppercase tracking-wider mb-1">Claude Cowork / Claude Desktop — Custom Connector URL</p>
                <div class="flex items-center gap-3 mt-2">
                    <code id="mcpUrl" class="text-xl font-mono font-bold break-all">{{ $app_url }}/mcp</code>
                    <button onclick="copyMcpUrl()" class="flex-shrink-0 p-2 bg-white/10 hover:bg-white/20 rounded-lg transition-colors" title="Copy URL">
                        <i class="fas fa-copy text-sm"></i>
                    </button>
                </div>
                <p class="text-primary-200 text-sm mt-2">
                    @if($secret_configured)
                        <i class="fas fa-check-circle mr-1"></i>Secret token configured — ready to connect
                    @else
                        <i class="fas fa-exclamation-triangle mr-1 text-yellow-300"></i>Set <code class="bg-white/10 px-1 rounded">MCP_SECRET</code> in your .env first
                    @endif
                </p>
            </div>
            <div class="flex-shrink-0 text-right">
                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 bg-white/15 rounded-full text-xs font-medium">
                    <span class="w-2 h-2 rounded-full {{ $secret_configured ? 'bg-green-400' : 'bg-yellow-400' }} animate-pulse"></span>
                    {{ $secret_configured ? 'Live' : 'Needs config' }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Left column ─────────────────────────────────────────────── --}}
        <div class="lg:col-span-2 space-y-6">

            {{-- Status --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Status</h3>
                <div class="space-y-3">

                    {{-- Secret token --}}
                    <div class="flex items-center justify-between p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        <div class="flex items-center gap-3">
                            @if($secret_configured)
                                <i class="fas fa-key text-green-500 text-xl"></i>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Secret Token Configured</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400 font-mono flex items-center gap-2">
                                        <span id="secret-status-display">{{ $secret_masked }}</span>
                                        <button onclick="toggleSecret('status')" id="secret-status-btn" title="Reveal" class="text-gray-400 hover:text-white transition-colors text-xs">
                                            <i class="fas fa-eye"></i>
                                        </button>
                                    </p>
                                </div>
                            @else
                                <i class="fas fa-exclamation-triangle text-yellow-500 text-xl"></i>
                                <div>
                                    <p class="font-medium text-gray-900 dark:text-white">Secret Token Missing</p>
                                    <p class="text-sm text-gray-500 dark:text-gray-400">Add <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">MCP_SECRET</code> to your <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">.env</code> file</p>
                                </div>
                            @endif
                        </div>
                        <form method="POST" action="{{ route('admin.mcp.rotate') }}" onsubmit="return confirm('Rotate the MCP secret? You will need to reconnect Claude.')">
                            @csrf
                            <button type="submit" class="text-sm text-gray-500 hover:text-red-500 dark:text-gray-400 dark:hover:text-red-400 transition-colors">
                                <i class="fas fa-sync-alt mr-1"></i>Rotate
                            </button>
                        </form>
                    </div>

                    {{-- Hosted endpoint --}}
                    <div class="flex items-center gap-3 p-4 border border-green-200 dark:border-green-800 bg-green-50 dark:bg-green-900/20 rounded-lg">
                        <i class="fas fa-check-circle text-green-500 text-xl flex-shrink-0"></i>
                        <div>
                            <p class="font-medium text-gray-900 dark:text-white">Streamable HTTP Endpoint Active</p>
                            <p class="text-sm text-gray-500 dark:text-gray-400 font-mono">POST {{ $app_url }}/mcp</p>
                            <p class="text-xs text-gray-400 mt-0.5">No Node.js required — runs directly on your Laravel app</p>
                        </div>
                    </div>

                    {{-- Local server path (for stdio / Claude Code CLI) --}}
                    <div class="flex items-center gap-3 p-4 border border-gray-200 dark:border-gray-700 rounded-lg">
                        @if($path_configured && $server_built)
                            <i class="fas fa-check-circle text-green-500 text-xl flex-shrink-0"></i>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Stdio Server Built <span class="text-xs font-normal text-gray-400">(Claude Code CLI)</span></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 font-mono break-all">{{ $local_path }}</p>
                            </div>
                        @elseif($path_configured)
                            <i class="fas fa-exclamation-triangle text-yellow-500 text-xl flex-shrink-0"></i>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Stdio Path Set — Not Built</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Run <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">cd mcp-server && npm run build</code> locally</p>
                            </div>
                        @else
                            <i class="fas fa-info-circle text-gray-400 text-xl flex-shrink-0"></i>
                            <div>
                                <p class="font-medium text-gray-900 dark:text-white">Stdio Server <span class="text-xs font-normal text-gray-400">(optional — Claude Code CLI only)</span></p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">Only needed for Claude Code CLI. Use the URL above for Claude Cowork/Desktop.</p>
                            </div>
                        @endif
                    </div>

                </div>
            </div>

            {{-- Connection methods --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-5">How to Connect</h3>

                {{-- Tab switcher --}}
                <div class="flex gap-2 mb-6 border-b border-gray-200 dark:border-gray-700">
                    <button onclick="showTab('cowork')" id="tab-cowork"
                        class="tab-btn px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors border-primary-500 text-primary-600 dark:text-primary-400">
                        <i class="fas fa-desktop mr-1.5"></i>Claude Cowork / Desktop
                    </button>
                    <button onclick="showTab('cli')" id="tab-cli"
                        class="tab-btn px-4 py-2.5 text-sm font-medium border-b-2 -mb-px transition-colors border-transparent text-gray-500 hover:text-gray-700 dark:text-gray-400">
                        <i class="fas fa-terminal mr-1.5"></i>Claude Code CLI
                    </button>
                </div>

                {{-- Cowork tab --}}
                <div id="panel-cowork">
                    <ol class="space-y-5">
                        <li class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-600/20 text-primary-400 flex items-center justify-center font-bold text-sm">1</div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white mb-1">Open Claude → Settings → Connectors</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">In Claude Cowork or Claude Desktop, navigate to <strong>Settings → Connectors → Add custom connector</strong>.</p>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-600/20 text-primary-400 flex items-center justify-center font-bold text-sm">2</div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white mb-1">Enter the connector URL</p>
                                <div class="bg-gray-900 rounded-lg px-4 py-3 flex items-center justify-between group mt-2">
                                    <code class="text-green-400 text-sm font-mono">{{ $app_url }}/mcp</code>
                                    <button onclick="copyCode(this, '{{ $app_url }}/mcp')" class="text-gray-500 hover:text-white opacity-0 group-hover:opacity-100 transition-all ml-3 text-xs">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-600/20 text-primary-400 flex items-center justify-center font-bold text-sm">3</div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white mb-1">Set the Authorization header</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Add a custom header named <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">Authorization</code> with value:</p>
                                <div class="bg-gray-900 rounded-lg px-4 py-3 flex items-center justify-between group">
                                    <code id="secret-bearer-display" class="text-yellow-300 text-sm font-mono">Bearer {{ $secret_configured ? $secret_masked : '(your MCP_SECRET)' }}</code>
                                    @if($secret_configured)
                                        <div class="flex items-center gap-2 opacity-0 group-hover:opacity-100 transition-all ml-3">
                                            <button onclick="toggleSecret('bearer')" id="secret-bearer-btn" title="Reveal" class="text-gray-500 hover:text-white text-xs">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button onclick="copyBearer()" class="text-gray-500 hover:text-white text-xs">
                                                <i class="fas fa-copy"></i>
                                            </button>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-600/20 text-primary-400 flex items-center justify-center font-bold text-sm">4</div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white mb-1">Enable the connector and test</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">Enable it in the Search &amp; tools menu, then try:</p>
                                <ul class="space-y-1">
                                    @foreach([
                                        'List all pending bookings this week',
                                        'Show me the 5 most recent leads',
                                        'Create a new CMS page titled "Spring Promo"',
                                        'Find clients named Smith and create monday.com tasks for each',
                                    ] as $example)
                                        <li class="flex items-start gap-2">
                                            <i class="fas fa-comment-dots text-primary-400 mt-0.5 text-xs flex-shrink-0"></i>
                                            <span class="text-sm text-gray-600 dark:text-gray-300 italic">"{{ $example }}"</span>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </li>
                    </ol>
                </div>

                {{-- CLI tab --}}
                <div id="panel-cli" class="hidden">
                    <ol class="space-y-5">
                        <li class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-600/20 text-primary-400 flex items-center justify-center font-bold text-sm">1</div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white mb-1">Build the stdio server <span class="text-xs font-normal text-gray-400">(once, on your local machine)</span></p>
                                <div class="bg-gray-900 rounded-lg px-4 py-3 flex items-center justify-between group mt-2">
                                    <code class="text-green-400 text-sm font-mono">cd mcp-server &amp;&amp; npm install &amp;&amp; npm run build</code>
                                    <button onclick="copyCode(this, 'cd mcp-server && npm install && npm run build')" class="text-gray-500 hover:text-white opacity-0 group-hover:opacity-100 transition-all ml-3 text-xs">
                                        <i class="fas fa-copy"></i>
                                    </button>
                                </div>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-600/20 text-primary-400 flex items-center justify-center font-bold text-sm">2</div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white mb-1">Download the stdio config</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400 mb-2">
                                    Click <strong>Download Stdio Config</strong> (top right) to get a pre-filled <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">prtl7-mcp-config.json</code>.
                                    Merge the <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">mcpServers</code> block into <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">~/.claude/claude_desktop_config.json</code>.
                                </p>
                                <a href="{{ route('admin.mcp.download') }}" class="inline-flex items-center gap-2 text-sm text-primary-500 hover:text-primary-400">
                                    <i class="fas fa-download"></i>Download config
                                </a>
                            </div>
                        </li>
                        <li class="flex gap-4">
                            <div class="flex-shrink-0 w-8 h-8 rounded-full bg-primary-600/20 text-primary-400 flex items-center justify-center font-bold text-sm">3</div>
                            <div class="flex-1">
                                <p class="font-medium text-gray-900 dark:text-white mb-1">Restart Claude Code and verify</p>
                                <p class="text-sm text-gray-500 dark:text-gray-400">
                                    Reopen Claude Code. Run <code class="bg-gray-100 dark:bg-gray-700 px-1 rounded">/mcp</code> to confirm <strong>prtl7-crm</strong> appears in the tool list.
                                </p>
                            </div>
                        </li>
                    </ol>
                </div>
            </div>

            {{-- Tool Reference --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">
                    Available Tools
                    <span class="ml-2 text-sm font-normal text-gray-500">({{ count($tools) }} tools)</span>
                </h3>

                @php $currentDomain = null; @endphp
                @foreach($tools as $tool)
                    @if($tool['domain'] !== $currentDomain)
                        @php $currentDomain = $tool['domain']; @endphp
                        <div class="text-xs font-semibold text-gray-500 uppercase tracking-wider mt-5 mb-2 first:mt-0">
                            {{ $currentDomain }}
                        </div>
                    @endif
                    <div class="flex items-start gap-3 py-2.5 border-b border-gray-100 dark:border-gray-700/50 last:border-0">
                        <span class="flex-shrink-0 mt-0.5 px-1.5 py-0.5 rounded text-xs font-medium
                            {{ $tool['rw'] === 'write'
                                ? 'bg-orange-100 dark:bg-orange-900/30 text-orange-700 dark:text-orange-400'
                                : 'bg-blue-100 dark:bg-blue-900/30 text-blue-700 dark:text-blue-400' }}">
                            {{ strtoupper($tool['rw']) }}
                        </span>
                        <div class="min-w-0">
                            <code class="text-sm font-mono text-gray-900 dark:text-gray-200">{{ $tool['name'] }}</code>
                            <p class="text-xs text-gray-500 dark:text-gray-400 mt-0.5">{{ $tool['description'] }}</p>
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

        {{-- ── Right column ─────────────────────────────────────────────── --}}
        <div class="space-y-6">

            {{-- Copy URL card --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Connector URL</h3>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg px-3 py-2.5 flex items-center justify-between gap-2 mb-3">
                    <code class="text-xs text-primary-500 font-mono break-all">{{ $app_url }}/mcp</code>
                    <button onclick="copyMcpUrl()" class="flex-shrink-0 text-gray-400 hover:text-primary-500 transition-colors">
                        <i class="fas fa-copy text-sm"></i>
                    </button>
                </div>
                <div class="space-y-2">
                    <a href="{{ route('admin.mcp.download') }}"
                       class="flex items-center gap-3 w-full px-4 py-2.5 bg-primary-600 hover:bg-primary-700 text-white rounded-lg transition-colors font-medium text-sm">
                        <i class="fas fa-download"></i>
                        Download Stdio Config
                    </a>
                    <button onclick="copyFullConfig()"
                        class="flex items-center gap-3 w-full px-4 py-2.5 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-900 dark:text-white rounded-lg transition-colors font-medium text-sm">
                        <i class="fas fa-copy"></i>
                        Copy Stdio Config JSON
                    </button>
                </div>
            </div>

            {{-- Stdio Config Preview --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
                <div class="flex items-center justify-between mb-3">
                    <h3 class="text-sm font-semibold text-gray-900 dark:text-white">Stdio Config <span class="text-xs font-normal text-gray-400">(CLI only)</span></h3>
                    <button onclick="copyFullConfig()" class="text-xs text-gray-400 hover:text-primary-400 transition-colors">
                        <i class="fas fa-copy mr-1"></i>Copy
                    </button>
                </div>
                <div class="bg-gray-900 rounded-lg p-4 overflow-auto max-h-64">
                    <pre id="configJson" class="text-xs text-green-400 font-mono whitespace-pre">{{ $config_json }}</pre>
                </div>
            </div>

            {{-- Security note --}}
            <div class="bg-yellow-50 dark:bg-yellow-900/20 border border-yellow-200 dark:border-yellow-700 rounded-lg p-4">
                <div class="flex gap-3">
                    <i class="fas fa-shield-alt text-yellow-500 mt-0.5 flex-shrink-0"></i>
                    <div>
                        <p class="text-sm font-medium text-yellow-800 dark:text-yellow-300">Security Note</p>
                        <p class="text-xs text-yellow-700 dark:text-yellow-400 mt-1">
                            The MCP_SECRET is the only thing protecting this endpoint. Treat it like a password. Use <strong>Rotate</strong> to invalidate it if ever exposed.
                        </p>
                    </div>
                </div>
            </div>

            {{-- Architecture --}}
            <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-4">
                <h3 class="text-sm font-semibold text-gray-900 dark:text-white mb-3">Architecture</h3>
                <div class="space-y-1 text-xs text-gray-500 dark:text-gray-400">
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded bg-blue-100 dark:bg-blue-900/30 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-robot text-blue-500 text-xs"></i>
                        </div>
                        <span>Claude Cowork / Desktop / Code</span>
                    </div>
                    <div class="pl-3 border-l-2 border-primary-500/40 ml-3 py-1">
                        <span class="text-primary-400 font-medium">↓ POST /mcp (JSON-RPC)</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded bg-red-100 dark:bg-red-900/30 flex items-center justify-center flex-shrink-0">
                            <i class="fab fa-laravel text-red-500 text-xs"></i>
                        </div>
                        <span>Laravel McpHttpController</span>
                    </div>
                    <div class="pl-3 border-l-2 border-gray-200 dark:border-gray-600 ml-3 py-1">
                        <span class="text-gray-400">↓ Eloquent ORM</span>
                    </div>
                    <div class="flex items-center gap-2">
                        <div class="w-6 h-6 rounded bg-green-100 dark:bg-green-900/30 flex items-center justify-center flex-shrink-0">
                            <i class="fas fa-database text-green-500 text-xs"></i>
                        </div>
                        <span>Your database</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script>
function showTab(name) {
    ['cowork','cli'].forEach(t => {
        document.getElementById('panel-' + t).classList.toggle('hidden', t !== name);
        const btn = document.getElementById('tab-' + t);
        btn.classList.toggle('border-primary-500', t === name);
        btn.classList.toggle('text-primary-600', t === name);
        btn.classList.toggle('dark:text-primary-400', t === name);
        btn.classList.toggle('border-transparent', t !== name);
        btn.classList.toggle('text-gray-500', t !== name);
    });
}

function copyCode(btn, text) {
    navigator.clipboard.writeText(text).then(() => {
        const icon = btn.querySelector('i');
        icon.className = 'fas fa-check';
        setTimeout(() => icon.className = 'fas fa-copy', 1500);
    });
}

function copyMcpUrl() {
    navigator.clipboard.writeText('{{ $app_url }}/mcp').then(() => {
        document.querySelectorAll('[onclick="copyMcpUrl()"] i').forEach(i => {
            i.className = 'fas fa-check text-green-400';
            setTimeout(() => i.className = 'fas fa-copy text-sm', 1500);
        });
    });
}

const SECRET_MASKED = '{{ $secret_masked }}';
const SECRET_FULL   = '{{ $secret_full }}';
const BEARER_MASKED = 'Bearer ' + SECRET_MASKED;
const BEARER_FULL   = 'Bearer ' + SECRET_FULL;

const secretState = { status: false, bearer: false };

function toggleSecret(key) {
    secretState[key] = !secretState[key];
    const revealed = secretState[key];

    if (key === 'status') {
        document.getElementById('secret-status-display').textContent = revealed ? SECRET_FULL : SECRET_MASKED;
        const icon = document.querySelector('#secret-status-btn i');
        icon.className = revealed ? 'fas fa-eye-slash' : 'fas fa-eye';
    } else {
        document.getElementById('secret-bearer-display').textContent = revealed ? BEARER_FULL : BEARER_MASKED;
        const icon = document.querySelector('#secret-bearer-btn i');
        icon.className = revealed ? 'fas fa-eye-slash' : 'fas fa-eye';
    }
}

function copyBearer() {
    navigator.clipboard.writeText(BEARER_FULL).then(() => {
        const btn = document.querySelector('[onclick="copyBearer()"]');
        const icon = btn.querySelector('i');
        icon.className = 'fas fa-check';
        setTimeout(() => icon.className = 'fas fa-copy', 1500);
    });
}

function copyFullConfig() {
    const text = document.getElementById('configJson').textContent;
    navigator.clipboard.writeText(text).then(() => {
        document.querySelectorAll('[onclick="copyFullConfig()"]').forEach(btn => {
            const orig = btn.innerHTML;
            btn.innerHTML = btn.innerHTML.replace(/Copy[\w\s]*/g, 'Copied!').replace('fa-copy', 'fa-check');
            setTimeout(() => btn.innerHTML = orig, 1500);
        });
    });
}
</script>
@endpush
@endsection
