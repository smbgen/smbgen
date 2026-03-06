@extends('layouts.admin')

@section('content')
<div class="space-y-6">
    <!-- Header -->
    <div class="flex justify-between items-center">
        <div>
            <h1 class="text-3xl font-bold text-gray-100">Lead Details</h1>
            <p class="text-gray-400">View complete lead information</p>
        </div>
        <div class="flex gap-3">
            <a href="{{ route('admin.leads.index') }}" class="btn-secondary">
                <i class="fas fa-arrow-left mr-2"></i>Back to Leads
            </a>
            @if(!$existingClient)
            <form action="{{ route('admin.leads.convert', $lead) }}" 
                  method="POST" 
                  class="inline"
                  onsubmit="return confirm('Convert this lead to a client? This will provision a client account and send them access credentials.');">
                @csrf
                <button type="submit" class="btn-primary">
                    <i class="fas fa-user-plus mr-2"></i>Convert to Client
                </button>
            </form>
            @endif
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

    <!-- Existing Client Warning -->
    @if($existingClient)
    <div class="card bg-yellow-900/20 border-yellow-700">
        <div class="p-6">
            <div class="flex items-start gap-4">
                <div class="bg-yellow-600/30 rounded-full p-3">
                    <i class="fas fa-exclamation-triangle text-yellow-300 text-xl"></i>
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-yellow-300 mb-2">Already a Client</h3>
                    <p class="text-yellow-200 mb-4">This lead has already been converted to a client.</p>
                    <a href="{{ route('clients.show', $existingClient) }}" class="btn-primary">
                        <i class="fas fa-user mr-2"></i>View Client Profile
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Main Information -->
        <div class="lg:col-span-2 space-y-6">
            <!-- Contact Information -->
            <div class="card">
                <div class="bg-gray-700 px-6 py-4 rounded-t-lg">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-user mr-2"></i>Contact Information
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-400">Name</label>
                        <p class="text-lg text-gray-100 mt-1">{{ $lead->name }}</p>
                    </div>
                    <div>
                        <label class="text-sm font-medium text-gray-400">Email</label>
                        <p class="text-lg text-gray-100 mt-1">
                            <a href="mailto:{{ $lead->email }}" class="text-blue-400 hover:text-blue-300">
                                {{ $lead->email }}
                            </a>
                        </p>
                    </div>
                    @if($lead->notification_email && $lead->notification_email !== $lead->email)
                    <div>
                        <label class="text-sm font-medium text-gray-400">Notification Email</label>
                        <p class="text-lg text-gray-100 mt-1">
                            <a href="mailto:{{ $lead->notification_email }}" class="text-blue-400 hover:text-blue-300">
                                {{ $lead->notification_email }}
                            </a>
                        </p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Message -->
            @if($lead->message)
            <div class="card">
                <div class="bg-gray-700 px-6 py-4 rounded-t-lg">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-comment mr-2"></i>Message
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-300 whitespace-pre-wrap">{{ $lead->message }}</p>
                </div>
            </div>
            @endif

            <!-- Custom Form Data -->
            @if($lead->form_data && count($lead->form_data) > 0)
            <div class="card">
                <div class="bg-gray-700 px-6 py-4 rounded-t-lg">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-list mr-2"></i>Additional Information
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach($lead->form_data as $key => $value)
                        <div>
                            <dt class="text-sm font-medium text-gray-400 mb-1">
                                {{ ucwords(str_replace('_', ' ', $key)) }}
                            </dt>
                            <dd class="text-gray-100">
                                @if(is_array($value))
                                    {{ implode(', ', $value) }}
                                @else
                                    {{ $value }}
                                @endif
                            </dd>
                        </div>
                        @endforeach
                    </dl>
                </div>
            </div>
            @endif
        </div>

        <!-- Sidebar -->
        <div class="space-y-6">
            <!-- Source Information -->
            <div class="card">
                <div class="bg-gray-700 px-6 py-4 rounded-t-lg">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-info-circle mr-2"></i>Source
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    @if($lead->cmsPage)
                    <div>
                        <label class="text-sm font-medium text-gray-400">CMS Page</label>
                        <p class="text-gray-100 mt-1">{{ $lead->cmsPage->title }}</p>
                        @if(config('business.features.cms'))
                        <a href="{{ route('admin.cms.edit', $lead->cmsPage) }}" 
                           class="text-sm text-blue-400 hover:text-blue-300 mt-2 inline-block">
                            <i class="fas fa-edit mr-1"></i>Edit Page
                        </a>
                        @endif
                    </div>
                    @endif
                    
                    @if($lead->source_site)
                    <div>
                        <label class="text-sm font-medium text-gray-400">Source Site</label>
                        <p class="text-gray-100 mt-1">{{ $lead->source_site }}</p>
                    </div>
                    @endif

                    @if($lead->referer)
                    <div>
                        <label class="text-sm font-medium text-gray-400">Referrer</label>
                        <p class="text-gray-100 mt-1 text-xs break-all">{{ $lead->referer }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Metadata -->
            <div class="card">
                <div class="bg-gray-700 px-6 py-4 rounded-t-lg">
                    <h3 class="text-lg font-semibold text-white">
                        <i class="fas fa-clock mr-2"></i>Metadata
                    </h3>
                </div>
                <div class="p-6 space-y-4">
                    <div>
                        <label class="text-sm font-medium text-gray-400">Submitted</label>
                        <p class="text-gray-100 mt-1">{{ $lead->created_at->format('M d, Y') }}</p>
                        <p class="text-sm text-gray-400">{{ $lead->created_at->format('h:i A') }}</p>
                        <p class="text-xs text-gray-500 mt-1">{{ $lead->created_at->diffForHumans() }}</p>
                    </div>

                    @if($lead->ip_address)
                    <div>
                        <label class="text-sm font-medium text-gray-400">IP Address</label>
                        <p class="text-gray-100 mt-1 font-mono text-sm">{{ $lead->ip_address }}</p>
                    </div>
                    @endif

                    @if($lead->user_agent)
                    <div>
                        <label class="text-sm font-medium text-gray-400">Browser</label>
                        <p class="text-gray-100 mt-1 text-xs break-words">{{ $lead->user_agent }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Actions -->
            <div class="card bg-red-900/20 border-red-700">
                <div class="bg-red-800/30 px-6 py-4 rounded-t-lg">
                    <h3 class="text-lg font-semibold text-red-300">
                        <i class="fas fa-exclamation-triangle mr-2"></i>Danger Zone
                    </h3>
                </div>
                <div class="p-6">
                    <p class="text-gray-300 text-sm mb-4">Delete this lead permanently. This action cannot be undone.</p>
                    <form action="{{ route('admin.leads.destroy', $lead) }}" 
                          method="POST" 
                          onsubmit="return confirm('Are you sure you want to delete this lead? This action cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-medium px-4 py-2 rounded-lg transition-colors">
                            <i class="fas fa-trash mr-2"></i>Delete Lead
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
