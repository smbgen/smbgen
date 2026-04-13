@extends('layouts.admin')

@section('content')
<div class="py-6 space-y-6">
    <!-- Header -->
    <div class="admin-page-header">
        <div>
            <h1 class="admin-page-title">Client Management</h1>
            <p class="admin-page-subtitle">View and manage all client accounts</p>
        </div>
        <div class="action-buttons">
            @if(\Route::has('clients.import.index'))
            <a href="{{ route('clients.import.index') }}" class="btn-secondary">
                <i class="fas fa-file-upload mr-2"></i>Import Clients
            </a>
            @endif
            <a href="{{ route('clients.create') }}" class="btn-primary">
                <i class="fas fa-user-plus mr-2"></i>Add New Client
            </a>
        </div>
    </div>

    <!-- Flash Messages -->

    @if(session('success'))
        <div class="alert alert-success">
            <i class="fas fa-check-circle"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="alert alert-error">
            <i class="fas fa-exclamation-circle"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    @if(session('warning'))
        <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle"></i>
            <span>{{ session('warning') }}</span>
        </div>
    @endif

    <!-- Search Form -->
    <div class="admin-card">
        <div class="admin-card-body">
        <form method="GET" action="{{ route('clients.index') }}" class="flex items-end space-x-4">
            <div class="flex-1">
                <label for="search" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Search clients</label>
                <input 
                    type="text" 
                    id="search" 
                    name="search" 
                    value="{{ $search ?? '' }}" 
                    placeholder="Search by name, email, or phone..." 
                    class="w-full px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                >
            </div>
            <div class="flex items-center space-x-2">
                <button type="submit" class="btn-primary flex items-center">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="m21 21-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                    Search
                </button>
                @if($search ?? false)
                    <a href="{{ route('clients.index') }}" class="btn-secondary">
                        Clear
                    </a>
                @endif
            </div>
        </form>
        </div>
    </div>

    <!-- Email Quick Send Area -->
    <div class="admin-card border-l-4 border-blue-500">
        <div class="admin-card-header bg-gradient-to-r from-blue-100 to-transparent dark:from-blue-900/30 cursor-pointer" onclick="toggleEmailQuickSend()">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <i class="fas fa-envelope-open-text text-blue-600 dark:text-blue-400 text-lg"></i>
                    <h3 class="admin-card-title">📧 Quick Email Send</h3>
                    <span class="text-xs text-gray-600 dark:text-gray-400">{{ $clients->count() }} {{ $search ? 'filtered' : 'total' }} clients</span>
                </div>
                <button type="button" class="text-gray-500 dark:text-gray-400 hover:text-gray-800 dark:hover:text-gray-200 transition-colors">
                    <i class="fas fa-chevron-down transition-transform" id="emailQuickSendToggle"></i>
                </button>
            </div>
        </div>
        <div id="emailQuickSendContent" class="admin-card-body space-y-4 hidden">
            <form id="quickEmailForm" method="POST" action="{{ route('admin.email.send') }}" class="space-y-4">
                @csrf

                <!-- Recipients Selection -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                        Recipients
                        <span class="text-blue-600 dark:text-blue-400">(check boxes below or use Select button)</span>
                    </label>
                    <div class="flex gap-2">
                        <input 
                            type="hidden" 
                            id="quickEmailRecipients" 
                            name="recipients"
                            value=""
                        >
                        <div class="flex-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg px-3 py-2 min-h-[38px]">
                            <div id="quickEmailRecipientsDisplay" class="flex flex-wrap gap-2">
                                <span class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
                                    <i class="fas fa-info-circle"></i>
                                    Check boxes in the table below to add recipients
                                </span>
                            </div>
                        </div>
                        <button type="button" onclick="selectAllClientsEmails()" class="btn-secondary text-sm flex items-center gap-2">
                            <i class="fas fa-check-double"></i>
                            Select {{ $search ? 'Filtered' : 'All' }}
                        </button>
                        <button type="button" onclick="clearClientsEmails()" class="btn-secondary text-sm flex items-center gap-2">
                            <i class="fas fa-times"></i>
                            Clear
                        </button>
                    </div>
                </div>

                <!-- Subject -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Subject</label>
                    <input 
                        type="text" 
                        id="quickEmailSubject"
                        name="subject" 
                        placeholder="Email subject..."
                        class="w-full px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                    >
                </div>

                <!-- Message -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Message</label>
                    <textarea 
                        id="quickEmailMessage"
                        name="message" 
                        rows="3"
                        placeholder="Write your message..."
                        class="w-full px-4 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 placeholder-gray-500 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent font-mono text-sm"
                    ></textarea>
                </div>

                <!-- Actions -->
                <div class="flex gap-2 justify-end">
                    <button type="reset" class="btn-secondary">
                        <i class="fas fa-redo mr-2"></i>Clear
                    </button>
                    <a href="{{ route('admin.email.index') }}" class="btn-secondary">
                        <i class="fas fa-expand-alt mr-2"></i>Full Composer
                    </a>
                    <button type="submit" class="btn-primary flex items-center gap-2" id="quickEmailSubmitBtn">
                        <i class="fas fa-paper-plane"></i>
                        Send Email
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Google ID Linking Section -->
    @if($unlinkedGoogleAccounts->count() > 0)
    <div class="admin-card border-l-4 border-purple-500">
        <div class="admin-card-header bg-gradient-to-r from-purple-100 to-transparent dark:from-purple-900/30">
            <div class="flex items-center gap-3">
                <i class="fas fa-link text-purple-400 text-lg"></i>
                <h3 class="admin-card-title">🔗 Link Google Accounts</h3>
                <span class="text-xs px-2 py-1 bg-purple-100 dark:bg-purple-600/20 text-purple-700 dark:text-purple-300 rounded-full font-medium">{{ $unlinkedGoogleAccounts->count() }} Available</span>
            </div>
        </div>
        <div class="admin-card-body">
            <p class="text-sm text-gray-700 dark:text-gray-300 mb-4">Found {{ $unlinkedGoogleAccounts->count() }} Google account(s) that can be linked to client accounts:</p>
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                @foreach($unlinkedGoogleAccounts as $account)
                <div class="bg-white dark:bg-gray-700 rounded-lg p-4 border border-gray-300 dark:border-gray-600">
                    <div class="flex items-start justify-between mb-3">
                        <div>
                            <div class="text-sm font-semibold text-gray-900 dark:text-white">{{ $account->client->name }}</div>
                            <div class="text-xs text-gray-600 dark:text-gray-400 mt-1">{{ $account->email }}</div>
                        </div>
                        <span class="text-xs px-2 py-1 bg-purple-100 dark:bg-purple-600/20 text-purple-700 dark:text-purple-300 rounded">Google ID</span>
                    </div>
                    <div class="bg-gray-100 dark:bg-gray-800 rounded px-3 py-2 mb-3">
                        <div class="text-xs text-gray-600 dark:text-gray-500 mb-1">Google ID:</div>
                        <div class="text-xs font-mono text-gray-800 dark:text-gray-200 break-all">{{ $account->google_id }}</div>
                    </div>
                    <form action="{{ route('clients.link-google', $account->client) }}" method="POST" class="flex gap-2">
                        @csrf
                        <input type="hidden" name="google_id" value="{{ $account->google_id }}">
                        <button type="submit" class="flex-1 btn-primary text-sm flex items-center justify-center gap-2">
                            <i class="fas fa-check"></i>
                            Link Account
                        </button>
                    </form>
                </div>
                @endforeach
            </div>
        </div>
    </div>
    @endif

    @if($clients->count() > 0)
        <x-admin.table :headers="['', 'Name', 'Email', 'Phone', 'Source', 'Created', 'Files', 'Actions']">
            @foreach($clients as $client)
                <x-admin.client-row :client="$client" />
            @endforeach
        </x-admin.table>
        
        @if(method_exists($clients, 'hasPages') && $clients->hasPages())
            <div class="mt-6">
                {{ $clients->links() }}
            </div>
        @endif
    @else
        <div class="text-center py-12">
            <div class="text-gray-500 dark:text-gray-400 mb-6">
                <h4 class="text-xl font-semibold mb-2">
                    @if($search ?? false)
                        No clients found matching "{{ $search }}"
                    @else
                        No clients found
                    @endif
                </h4>
                <p>
                    @if($search ?? false)
                        Try adjusting your search criteria or <a href="{{ route('clients.index') }}" class="text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300">view all clients</a>.
                    @else
                        Get started by adding your first client.
                    @endif
                </p>
            </div>
            @if(!($search ?? false))
                <a href="{{ route('clients.create') }}" class="btn-primary">
                    + Add Your First Client
                </a>
            @endif
        </div>
    @endif
</div>

<script>
// Toggle email quick send section
function toggleEmailQuickSend() {
    const content = document.getElementById('emailQuickSendContent');
    const toggle = document.getElementById('emailQuickSendToggle');
    
    content.classList.toggle('hidden');
    toggle.classList.toggle('rotate-180');
    
    // Save preference to localStorage
    localStorage.setItem('emailQuickSendCollapsed', content.classList.contains('hidden'));
}

// Restore email quick send state from localStorage
window.addEventListener('DOMContentLoaded', function() {
    // Default to collapsed, but respect saved preference if user has expanded it
    const isCollapsed = localStorage.getItem('emailQuickSendCollapsed') !== 'false';
    if (isCollapsed) {
        const content = document.getElementById('emailQuickSendContent');
        const toggle = document.getElementById('emailQuickSendToggle');
        content.classList.add('hidden');
        toggle.classList.add('rotate-180');
    } else {
        // If user previously expanded it, keep it expanded
        const toggle = document.getElementById('emailQuickSendToggle');
        toggle.classList.remove('rotate-180');
    }
});

// Get all client emails from the table (only visible/filtered clients)
function getAllClientEmails() {
    const clientEmails = [];
    
    // Get emails from visible checkboxes
    document.querySelectorAll('.client-email-checkbox').forEach(checkbox => {
        if (checkbox.offsetParent !== null) {
            clientEmails.push(checkbox.dataset.email);
        }
    });
    
    return clientEmails;
}

// Select all client emails (from visible/filtered results)
function selectAllClientsEmails() {
    const visibleCheckboxes = document.querySelectorAll('.client-email-checkbox');
    const visibleCount = Array.from(visibleCheckboxes).filter(cb => cb.offsetParent !== null).length;
    
    if (visibleCount === 0) {
        alert('No clients found. Try adjusting your search filter or viewing all clients.');
        return;
    }
    
    // Check all visible checkboxes
    document.querySelectorAll('.client-email-checkbox').forEach(checkbox => {
        if (checkbox.offsetParent !== null) {
            checkbox.checked = true;
        }
    });
    
    updateRecipientsDisplay();
}

// Clear all selected emails
function clearClientsEmails() {
    document.querySelectorAll('.client-email-checkbox').forEach(cb => cb.checked = false);
    updateRecipientsDisplay();
}

// Toggle individual client email selection
function toggleClientEmail(checkbox) {
    const selectedEmails = getSelectedEmails();
    updateRecipientsDisplay();
}

// Get currently selected emails from checkboxes
function getSelectedEmails() {
    const selected = [];
    document.querySelectorAll('.client-email-checkbox:checked').forEach(checkbox => {
        selected.push({
            email: checkbox.dataset.email,
            name: checkbox.dataset.name
        });
    });
    return selected;
}

// Update recipients display with tags
function updateRecipientsDisplay() {
    const selected = getSelectedEmails();
    const display = document.getElementById('quickEmailRecipientsDisplay');
    const hiddenInput = document.getElementById('quickEmailRecipients');
    
    // Update hidden input with comma-separated emails
    hiddenInput.value = selected.map(s => s.email).join(', ');
    
    // Update display with tags
    if (selected.length === 0) {
        display.innerHTML = `<span class="text-sm text-gray-500 dark:text-gray-400 flex items-center gap-2">
            <i class="fas fa-info-circle"></i>
            Check boxes in the table below to add recipients
        </span>`;
        return;
    }
    
    display.innerHTML = selected.map(client => `
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 bg-blue-100 dark:bg-blue-600/20 text-blue-700 dark:text-blue-300 rounded-full text-xs border border-blue-300 dark:border-blue-500/30">
            <i class="fas fa-user text-[10px]"></i>
            <span>${client.name}</span>
        </span>
    `).join('');
}

// Set email recipients and update display
function setEmailRecipients(emails) {
    // Uncheck all first
    document.querySelectorAll('.client-email-checkbox').forEach(cb => cb.checked = false);
    
    // Check matching emails
    emails.forEach(email => {
        const checkbox = document.querySelector(`.client-email-checkbox[data-email="${email}"]`);
        if (checkbox) checkbox.checked = true;
    });
    
    updateRecipientsDisplay();
}

// Toggle all checkboxes
function toggleAllCheckboxes(checked) {
    document.querySelectorAll('.client-email-checkbox').forEach(checkbox => {
        // Only toggle checkboxes that are visible (not filtered out)
        if (checkbox.offsetParent !== null) {
            checkbox.checked = checked;
        }
    });
    updateRecipientsDisplay();
}
document.getElementById('quickEmailForm')?.addEventListener('submit', function(e) {
    const recipients = document.getElementById('quickEmailRecipients').value.trim();
    const subject = document.getElementById('quickEmailSubject').value.trim();
    const message = document.getElementById('quickEmailMessage').value.trim();
    
    if (!recipients) {
        e.preventDefault();
        alert('Please select at least one client to send to');
        return;
    }
    
    if (!subject) {
        e.preventDefault();
        alert('Please enter an email subject');
        return;
    }
    
    if (!message) {
        e.preventDefault();
        alert('Please enter a message');
        return;
    }
});
</script>

@endsection
