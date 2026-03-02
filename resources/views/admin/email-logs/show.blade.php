@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-100">📧 Email Details</h1>
            <p class="text-gray-400">Tracking ID: {{ $emailLog->tracking_id }}</p>
        </div>
        <div class="flex space-x-2">
            @if(in_array($emailLog->status, ['failed', 'bounced']))
                <form action="{{ route('admin.email-logs.resend', $emailLog->id) }}" method="POST" onsubmit="return confirm('Resend this email?')">
                    @csrf
                    <button type="submit" class="btn-primary">
                        <i class="fas fa-redo mr-2"></i>Resend Email
                    </button>
                </form>
            @endif
            <a href="{{ route('admin.email-logs.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Logs
            </a>
        </div>
    </div>

    <!-- Flash Messages -->
    @foreach (['success', 'info', 'warning', 'error'] as $msg)
        @if(session($msg))
            <div class="p-4 rounded-lg {{ $msg === 'error' ? 'bg-red-100 border border-red-400 text-red-700' : 
                ($msg === 'warning' ? 'bg-yellow-100 border border-yellow-400 text-yellow-700' : 
                ($msg === 'info' ? 'bg-blue-100 border border-blue-400 text-blue-700' : 
                'bg-green-100 border border-green-400 text-green-700')) }}">
                {{ session($msg) }}
            </div>
        @endif
    @endforeach

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Left Column: Email Details -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Status Card -->
            <div class="card">
                <div class="bg-gray-800 px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-100">Email Status</h3>
                </div>
                <div class="p-6">
                    <div class="flex items-center justify-between mb-6">
                        <div>
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium {{ 
                                $emailLog->status === 'opened' ? 'bg-green-100 text-green-800' :
                                ($emailLog->status === 'clicked' ? 'bg-purple-100 text-purple-800' :
                                ($emailLog->status === 'delivered' || $emailLog->status === 'sent' ? 'bg-blue-100 text-blue-800' :
                                ($emailLog->status === 'bounced' || $emailLog->status === 'failed' ? 'bg-red-100 text-red-800' :
                                'bg-gray-100 text-gray-800')))
                            }}">
                                {{ $emailLog->status_icon }} {{ ucfirst($emailLog->status) }}
                            </span>
                        </div>
                        <div class="text-sm text-gray-400">
                            Created {{ $emailLog->created_at->diffForHumans() }}
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-gray-400">Sent At</div>
                                <div class="text-gray-100">
                                    {{ $emailLog->formatTimestamp($emailLog->sent_at) }}
                                </div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-400">Delivered At</div>
                                <div class="text-gray-100">
                                    {{ $emailLog->formatTimestamp($emailLog->delivered_at) }}
                                </div>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <div class="text-sm text-gray-400">First Opened</div>
                                <div class="text-gray-100">
                                    {{ $emailLog->formatTimestamp($emailLog->opened_at) }}
                                </div>
                            </div>
                            <div>
                                <div class="text-sm text-gray-400">Last Opened</div>
                                <div class="text-gray-100">
                                    {{ $emailLog->formatTimestamp($emailLog->last_opened_at) }}
                                </div>
                            </div>
                        </div>

                        @if($emailLog->bounced_at)
                            <div class="bg-red-900/20 border border-red-500 rounded-lg p-4">
                                <div class="text-sm text-red-400 font-medium">Bounced At</div>
                                <div class="text-red-300">
                                    {{ $emailLog->formatTimestamp($emailLog->bounced_at) }}
                                </div>
                            </div>
                        @endif
                    </div>
                </div>
            </div>

            <!-- Email Content -->
            <div class="card">
                <div class="bg-gray-800 px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-100">Email Content</h3>
                </div>
                <div class="p-6">
                    <div class="space-y-4">
                        <div>
                            <div class="text-sm text-gray-400 mb-1">To</div>
                            <div class="text-gray-100">{{ $emailLog->to_email }}</div>
                        </div>

                        @if($emailLog->cc_email)
                            <div>
                                <div class="text-sm text-gray-400 mb-1">CC</div>
                                <div class="text-gray-100">{{ $emailLog->cc_email }}</div>
                            </div>
                        @endif

                        <div>
                            <div class="text-sm text-gray-400 mb-1">Subject</div>
                            <div class="text-gray-100 font-medium">{{ $emailLog->subject }}</div>
                        </div>

                        <div>
                            <div class="flex items-center justify-between mb-2">
                                <div class="text-sm text-gray-400">Message Body</div>
                                <div class="flex space-x-2">
                                    <button onclick="showRendered()" id="renderedBtn" class="px-3 py-1 text-xs bg-blue-600 text-white rounded hover:bg-blue-700">
                                        Rendered
                                    </button>
                                    <button onclick="showRaw()" id="rawBtn" class="px-3 py-1 text-xs bg-gray-600 text-gray-300 rounded hover:bg-gray-700">
                                        Raw HTML
                                    </button>
                                    <button onclick="copyToClipboard()" id="copyBtn" class="px-3 py-1 text-xs bg-gray-600 text-gray-300 rounded hover:bg-gray-700">
                                        <i class="fas fa-copy mr-1"></i>Copy
                                    </button>
                                </div>
                            </div>
                            <div id="renderedView" class="bg-white rounded-lg p-4 border border-gray-700 overflow-x-auto">
                                <div class="email-preview-content">
                                    {!! $emailLog->body !!}
                                </div>
                            </div>
                            <div id="rawView" class="hidden bg-gray-800 rounded-lg p-4 border border-gray-700 overflow-x-auto max-h-[600px]">
                                <pre class="text-xs text-gray-300 whitespace-pre-wrap break-words" id="rawContent">{{ htmlspecialchars($emailLog->body) }}</pre>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- SMTP Response / Error -->
            @if($emailLog->smtp_response || $emailLog->error_message)
                <div class="card">
                    <div class="bg-gray-800 px-6 py-4 border-b border-gray-700">
                        <h3 class="text-lg font-semibold text-gray-100">Technical Details</h3>
                    </div>
                    <div class="p-6">
                        @if($emailLog->smtp_response)
                            <div class="mb-4">
                                <div class="text-sm text-gray-400 mb-1">SMTP Response</div>
                                <pre class="bg-gray-800 rounded-lg p-4 text-xs text-green-400 overflow-x-auto">{{ $emailLog->smtp_response }}</pre>
                            </div>
                        @endif

                        @if($emailLog->error_message)
                            <div>
                                <div class="text-sm text-red-400 mb-1">Error Message</div>
                                <pre class="bg-red-900/20 border border-red-500 rounded-lg p-4 text-xs text-red-300 overflow-x-auto">{{ $emailLog->error_message }}</pre>
                            </div>
                        @endif
                    </div>
                </div>
            @endif
        </div>

        <!-- Right Column: Engagement & Metadata -->
        <div class="space-y-6">
            <!-- Engagement Stats -->
            <div class="card">
                <div class="bg-gray-800 px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-100">Engagement</h3>
                </div>
                <div class="p-6 space-y-4">
                    <div class="flex items-center justify-between p-4 bg-green-900/20 rounded-lg">
                        <div>
                            <div class="text-2xl">📨</div>
                            <div class="text-sm text-gray-400 mt-1">Opens</div>
                        </div>
                        <div class="text-3xl font-bold text-green-400">
                            {{ $emailLog->open_count }}
                        </div>
                    </div>

                    <div class="flex items-center justify-between p-4 bg-purple-900/20 rounded-lg">
                        <div>
                            <div class="text-2xl">🖱️</div>
                            <div class="text-sm text-gray-400 mt-1">Clicks</div>
                        </div>
                        <div class="text-3xl font-bold text-purple-400">
                            {{ $emailLog->click_count }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Associated Records -->
            <div class="card">
                <div class="bg-gray-800 px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-100">Associated Records</h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($emailLog->user)
                        <div>
                            <div class="text-sm text-gray-400 mb-1">Sent By</div>
                            <div class="text-gray-100">{{ $emailLog->user->name ?? 'Unknown' }}</div>
                            <div class="text-xs text-gray-500">{{ $emailLog->user->email ?? 'N/A' }}</div>
                        </div>
                    @endif

                    @if($emailLog->booking)
                        <div>
                            <div class="text-sm text-gray-400 mb-1">Related Booking</div>
                            <a href="{{ route('admin.dashboard') }}#booking-{{ $emailLog->booking->id }}" 
                               class="text-blue-400 hover:text-blue-300">
                                Booking #{{ $emailLog->booking->id }}
                            </a>
                            <div class="text-xs text-gray-500 mt-1">
                                {{ $emailLog->booking->customer_name ?? 'Unknown' }} - 
                                {{ optional($emailLog->booking->booking_date)->format('M j, Y') ?? 'N/A' }}
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Tracking Metadata -->
            <div class="card">
                <div class="bg-gray-800 px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-100">Tracking Info</h3>
                </div>
                <div class="p-6 space-y-3">
                    <div>
                        <div class="text-sm text-gray-400 mb-1">Tracking ID</div>
                        <div class="text-xs text-gray-300 font-mono bg-gray-800 p-2 rounded">
                            {{ $emailLog->tracking_id }}
                        </div>
                    </div>

                    @if($emailLog->ip_address)
                        <div>
                            <div class="text-sm text-gray-400 mb-1">IP Address</div>
                            <div class="text-gray-100">{{ $emailLog->ip_address }}</div>
                        </div>
                    @endif

                    @if($emailLog->user_agent)
                        <div>
                            <div class="text-sm text-gray-400 mb-1">User Agent</div>
                            <div class="text-xs text-gray-300 break-all">{{ $emailLog->user_agent }}</div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card">
                <div class="bg-gray-800 px-6 py-4 border-b border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-100">Actions</h3>
                </div>
                <div class="p-6 space-y-2">
                    @if(in_array($emailLog->status, ['failed', 'bounced']))
                        <form action="{{ route('admin.email-logs.resend', $emailLog->id) }}" method="POST" onsubmit="return confirm('Resend this email?')">
                            @csrf
                            <button type="submit" class="btn-primary w-full">
                                <i class="fas fa-redo mr-2"></i>Resend Email
                            </button>
                        </form>
                    @endif

                    <form action="{{ route('admin.email-logs.destroy', $emailLog->id) }}" 
                          method="POST" 
                          onsubmit="return confirm('Delete this email log? This action cannot be undone.')">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger w-full">
                            <i class="fas fa-trash mr-2"></i>Delete Log
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
function showRendered() {
    document.getElementById('renderedView').classList.remove('hidden');
    document.getElementById('rawView').classList.add('hidden');
    document.getElementById('renderedBtn').classList.add('bg-blue-600', 'text-white');
    document.getElementById('renderedBtn').classList.remove('bg-gray-600', 'text-gray-300');
    document.getElementById('rawBtn').classList.remove('bg-blue-600', 'text-white');
    document.getElementById('rawBtn').classList.add('bg-gray-600', 'text-gray-300');
}

function showRaw() {
    document.getElementById('renderedView').classList.add('hidden');
    document.getElementById('rawView').classList.remove('hidden');
    document.getElementById('rawBtn').classList.add('bg-blue-600', 'text-white');
    document.getElementById('rawBtn').classList.remove('bg-gray-600', 'text-gray-300');
    document.getElementById('renderedBtn').classList.remove('bg-blue-600', 'text-white');
    document.getElementById('renderedBtn').classList.add('bg-gray-600', 'text-gray-300');
}

function copyToClipboard() {
    const rawContent = document.getElementById('rawContent');
    const text = rawContent.textContent;
    const copyBtn = document.getElementById('copyBtn');
    
    navigator.clipboard.writeText(text).then(() => {
        // Show success feedback
        const originalHTML = copyBtn.innerHTML;
        copyBtn.innerHTML = '<i class="fas fa-check mr-1"></i>Copied!';
        copyBtn.classList.add('bg-green-600', 'text-white');
        copyBtn.classList.remove('bg-gray-600', 'text-gray-300');
        
        setTimeout(() => {
            copyBtn.innerHTML = originalHTML;
            copyBtn.classList.remove('bg-green-600', 'text-white');
            copyBtn.classList.add('bg-gray-600', 'text-gray-300');
        }, 2000);
    }).catch(err => {
        console.error('Failed to copy:', err);
        alert('Failed to copy to clipboard');
    });
}
</script>
@endsection
