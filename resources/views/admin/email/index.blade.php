@extends('layouts.admin')

@section('title', 'Email Composer')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-6xl mx-auto">

        {{-- Package template banner --}}
        @if($prefill)
        <div class="mb-5 flex items-center justify-between bg-blue-900/30 border border-blue-700 rounded-lg px-4 py-3">
            <div class="flex items-center gap-3">
                <i class="fas fa-envelope text-blue-400"></i>
                <div>
                    <span class="text-blue-200 font-medium">{{ $prefill['file_name'] }}</span>
                    <span class="text-gray-400 text-sm ml-2">from package <span class="text-gray-200">{{ $prefill['package_name'] }}</span></span>
                    @if($prefill['client_name'])
                        <span class="text-gray-400 text-sm ml-1">→ <span class="text-gray-200">{{ $prefill['client_name'] }}</span></span>
                    @endif
                </div>
            </div>
            <a href="{{ route('admin.packages.show', $prefill['package_id']) }}"
                class="text-xs text-gray-400 hover:text-gray-200 flex items-center gap-1">
                <i class="fas fa-arrow-left"></i> Back to package
            </a>
        </div>
        @endif

        <div class="mb-6 flex items-center justify-between">
            <div>
                <h1 class="text-3xl font-bold text-gray-900 dark:text-white mb-2">📧 Email Composer</h1>
                <p class="text-gray-600 dark:text-gray-400">Send emails to clients, users, or custom recipients</p>
            </div>
            @if(\Route::has('admin.email-logs.index'))
            <a href="{{ route('admin.email-logs.index') }}" 
               class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white px-4 py-2 rounded-lg transition-colors flex items-center gap-2">
                <i class="fas fa-chart-line"></i>
                Email Deliverability
            </a>
            @endif
        </div>

        <!-- Flash Messages -->
        @foreach (['success', 'info', 'warning', 'error'] as $msg)
            @if(session($msg))
                <div class="mb-6 p-4 rounded-lg {{ $msg === 'error' ? 'bg-red-500/20 border border-red-500 text-red-300' : 
                    ($msg === 'warning' ? 'bg-yellow-500/20 border border-yellow-500 text-yellow-300' : 
                    ($msg === 'info' ? 'bg-blue-500/20 border border-blue-500 text-blue-300' : 
                    'bg-green-500/20 border border-green-500 text-green-300')) }}">
                    {{ session($msg) }}
                </div>
            @endif
        @endforeach

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Email Composer Form -->
            <div class="lg:col-span-2">
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                    <form action="{{ route('admin.email.send') }}" method="POST" id="emailForm">
                        @csrf
                        @if(!empty($prefill['is_html']))
                            <input type="hidden" name="is_html_body" value="1">
                        @endif

                        <!-- Recipients -->
                        <div class="mb-6 relative">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Recipients <span class="text-red-400">*</span>
                            </label>
                            <textarea name="recipients" id="recipients" rows="2" required
                                      class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                      placeholder="Start typing name or email... (clients, users, bookings)"
                                      autocomplete="off">{{ old('recipients', $prefill['recipient'] ?? '') }}</textarea>
                            
                            <!-- Autocomplete Dropdown -->
                            <div id="emailAutocomplete" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                <!-- Results will be inserted here -->
                            </div>
                            
                            <!-- Loading Indicator -->
                            <div id="autocompleteLoading" class="hidden absolute z-10 w-full mt-1 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg shadow-lg p-3">
                                <div class="flex items-center text-gray-700 dark:text-gray-300 text-sm">
                                    <svg class="animate-spin h-4 w-4 mr-2" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                    </svg>
                                    Searching recipients...
                                </div>
                            </div>
                            
                            <p class="mt-1 text-xs text-gray-600 dark:text-gray-400">Start typing to see clients, users, and booking emails. Separate multiple emails with commas/semicolons</p>
                            @error('recipients')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Quick Add Recipients -->
                        <div class="mb-6 grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quick Add User</label>
                                <select id="quickAddUser" class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select user...</option>
                                    @foreach($users as $user)
                                        <option value="{{ $user->email }}">{{ $user->name }} ({{ $user->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">Quick Add Client</label>
                                <select id="quickAddClient" class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500">
                                    <option value="">Select client...</option>
                                    @foreach($clients as $client)
                                        <option value="{{ $client->email }}">{{ $client->name }} ({{ $client->email }})</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        <!-- Subject -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                                Subject <span class="text-red-400">*</span>
                            </label>
                            <input type="text" name="subject" id="subject" required
                                   value="{{ old('subject', $prefill['subject'] ?? '') }}"
                                   class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500"
                                   placeholder="Email subject">
                            @error('subject')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Message -->
                        <div class="mb-6" id="messageBlock">
                            <div class="flex items-center justify-between mb-2">
                                <label class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Message <span class="text-red-400">*</span>
                                </label>
                                @if(!empty($prefill['is_html']))
                                <div class="flex items-center gap-1 bg-gray-200 dark:bg-gray-700 rounded-lg p-1">
                                    <button type="button" id="tabSource"
                                        onclick="showTab('source')"
                                        class="text-xs px-3 py-1 rounded-md bg-gray-300 dark:bg-gray-600 text-gray-900 dark:text-white transition-colors">
                                        <i class="fas fa-code mr-1"></i>HTML Source
                                    </button>
                                    <button type="button" id="tabPreview"
                                        onclick="showTab('preview')"
                                        class="text-xs px-3 py-1 rounded-md text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-white transition-colors">
                                        <i class="fas fa-eye mr-1"></i>Preview
                                    </button>
                                </div>
                                @endif
                            </div>

                            <div id="sourcePane">
                                <textarea name="message" id="message" rows="16" required
                                          class="w-full bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 text-gray-900 dark:text-white rounded-lg px-3 py-2 focus:ring-blue-500 focus:border-blue-500 font-mono text-sm"
                                          placeholder="Write your message here...">{{ old('message', $prefill['body'] ?? '') }}</textarea>
                            </div>

                            @if(!empty($prefill['is_html']))
                            <div id="previewPane" class="hidden">
                                <iframe id="htmlPreviewFrame"
                                    class="w-full rounded-lg border border-gray-600 bg-white"
                                    style="height: 480px"
                                    sandbox="allow-scripts">
                                </iframe>
                                <p class="text-xs text-gray-500 mt-1">
                                    <i class="fas fa-info-circle mr-1"></i>Preview only — edit in HTML Source tab
                                </p>
                            </div>
                            @endif

                            @error('message')
                                <p class="mt-1 text-sm text-red-400">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Options (hidden for HTML templates — signature not applicable) -->
                        @if(empty($prefill['is_html']))
                        <div class="mb-6">
                            <label class="flex items-center text-gray-700 dark:text-gray-300">
                                <input type="checkbox" name="include_signature" value="1" class="rounded bg-white dark:bg-gray-700 border-gray-300 dark:border-gray-600 text-blue-500 focus:ring-blue-500 mr-2">
                                <span class="text-sm">Include email signature</span>
                            </label>
                        </div>
                        @endif

                        <!-- Actions -->
                        <div class="flex gap-3">
                            <button type="submit" class="flex-1 bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-3 rounded-lg transition-colors">
                                <i class="fas fa-paper-plane mr-2"></i>Send Email
                            </button>
                            <button type="button" onclick="document.getElementById('emailForm').reset()" 
                                    class="bg-gray-200 dark:bg-gray-700 hover:bg-gray-300 dark:hover:bg-gray-600 text-gray-900 dark:text-white font-medium px-6 py-3 rounded-lg transition-colors">
                                Clear
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Email Templates -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">📝 Quick Templates</h3>
                    <div class="space-y-2">
                        <button onclick="loadTemplate('welcome')" class="w-full text-left px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-300 rounded transition-colors text-sm">
                            Welcome Email
                        </button>
                        <button onclick="loadTemplate('login_assistance')" class="w-full text-left px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-300 rounded transition-colors text-sm">
                            <i class="fas fa-sign-in-alt mr-2 text-blue-400"></i>Login Help
                        </button>
                        <button onclick="loadTemplate('password_reset')" class="w-full text-left px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-300 rounded transition-colors text-sm">
                            <i class="fas fa-key mr-2 text-yellow-400"></i>Password Reset
                        </button>
                        <button onclick="loadTemplate('booking_reminder')" class="w-full text-left px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-300 rounded transition-colors text-sm">
                            Appointment Reminder
                        </button>
                        <button onclick="loadTemplate('follow_up')" class="w-full text-left px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-300 rounded transition-colors text-sm">
                            Follow-up Email
                        </button>
                        <button onclick="loadTemplate('thank_you')" class="w-full text-left px-3 py-2 bg-gray-100 dark:bg-gray-700 hover:bg-gray-200 dark:hover:bg-gray-600 text-gray-800 dark:text-gray-300 rounded transition-colors text-sm">
                            Thank You Email
                        </button>
                    </div>
                </div>

                <!-- Email Info -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border border-gray-200 dark:border-gray-700">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">ℹ️ Email Info</h3>
                    <div class="space-y-3 text-sm">
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">From:</span>
                            <div class="text-gray-900 dark:text-white font-mono text-xs mt-1">{{ config('mail.from.address', 'Not configured') }}</div>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Mailer:</span>
                            <div class="text-gray-900 dark:text-white font-mono text-xs mt-1">{{ config('mail.default', 'Not configured') }}</div>
                        </div>
                        <div>
                            <span class="text-gray-600 dark:text-gray-400">Host:</span>
                            <div class="text-gray-900 dark:text-white font-mono text-xs mt-1">{{ config('mail.mailers.smtp.host', 'Not configured') }}</div>
                        </div>
                    </div>
                </div>

                @if(config('app.debug'))
                <!-- Debug Panel -->
                <div class="bg-white dark:bg-gray-800 rounded-lg shadow-lg p-6 border-2 border-yellow-600">
                    <h3 class="text-lg font-semibold text-yellow-400 mb-4">🐛 Debug Panel</h3>
                    <div class="space-y-3 text-xs font-mono">
                        <div>
                            <div class="text-gray-600 dark:text-gray-400 mb-1">API Endpoint:</div>
                            <div class="text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-900 p-2 rounded break-all">{{ route('admin.email.booking-emails') }}</div>
                        </div>
                        <div>
                            <div class="text-gray-600 dark:text-gray-400 mb-1">Last Query:</div>
                            <div id="debugLastQuery" class="text-gray-900 dark:text-white bg-gray-100 dark:bg-gray-900 p-2 rounded">-</div>
                        </div>
                        <div>
                            <div class="text-gray-400 mb-1">Results Count:</div>
                            <div id="debugResultsCount" class="text-white bg-gray-900 p-2 rounded">-</div>
                        </div>
                        <div>
                            <div class="text-gray-400 mb-1">Current Word:</div>
                            <div id="debugCurrentWord" class="text-white bg-gray-900 p-2 rounded">-</div>
                        </div>
                        <div>
                            <div class="text-gray-400 mb-1">Dropdown Visible:</div>
                            <div id="debugDropdownVisible" class="text-white bg-gray-900 p-2 rounded">-</div>
                        </div>
                        <div>
                            <div class="text-gray-400 mb-1">Last Error:</div>
                            <div id="debugLastError" class="text-red-400 bg-gray-900 p-2 rounded">-</div>
                        </div>
                        <div class="pt-2">
                            <button onclick="testAutocompleteAPI()" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white px-3 py-2 rounded text-sm">
                                Test API Directly
                            </button>
                        </div>
                        <div>
                            <div class="text-gray-400 mb-1">API Test Result:</div>
                            <div id="debugApiTest" class="text-white bg-gray-900 p-2 rounded max-h-40 overflow-y-auto">Click button to test</div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Recent Emails -->
                @if(!empty($emailHistory))
                <div class="bg-gray-800 rounded-lg shadow-lg p-6">
                    <h3 class="text-lg font-semibold text-white mb-4">📬 Recent Emails</h3>
                    <div class="space-y-3">
                        @foreach(array_slice($emailHistory, 0, 5) as $email)
                            <div class="text-xs">
                                <div class="text-white font-medium truncate">{{ $email['subject'] }}</div>
                                <div class="text-gray-400 truncate">To: {{ $email['to'] }}</div>
                                <div class="text-gray-500">{{ \Carbon\Carbon::parse($email['date'])->diffForHumans() }}</div>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>

<script>
// HTML template tab switching
function showTab(tab) {
    const source  = document.getElementById('sourcePane');
    const preview = document.getElementById('previewPane');
    const tabS    = document.getElementById('tabSource');
    const tabP    = document.getElementById('tabPreview');
    if (!source || !preview) return;

    if (tab === 'preview') {
        source.classList.add('hidden');
        preview.classList.remove('hidden');
        tabS.className = 'text-xs px-3 py-1 rounded-md text-gray-400 hover:text-white transition-colors';
        tabP.className = 'text-xs px-3 py-1 rounded-md bg-gray-600 text-white transition-colors';
        // Render current textarea content into iframe
        const frame = document.getElementById('htmlPreviewFrame');
        const html  = document.getElementById('message').value;
        frame.srcdoc = html;
    } else {
        preview.classList.add('hidden');
        source.classList.remove('hidden');
        tabS.className = 'text-xs px-3 py-1 rounded-md bg-gray-600 text-white transition-colors';
        tabP.className = 'text-xs px-3 py-1 rounded-md text-gray-400 hover:text-white transition-colors';
    }
}

// Clean up recipients field before submission
document.getElementById('emailForm').addEventListener('submit', function(e) {
    const recipientsField = document.getElementById('recipients');
    // Remove trailing commas, semicolons, and extra whitespace
    recipientsField.value = recipientsField.value
        .replace(/[,;]+\s*$/, '')  // Remove trailing separators
        .replace(/\s+/g, ' ')       // Normalize whitespace
        .trim();
});

// Quick add email functionality
document.getElementById('quickAddUser').addEventListener('change', function() {
    if (this.value) {
        addRecipient(this.value);
        this.value = '';
    }
});

document.getElementById('quickAddClient').addEventListener('change', function() {
    if (this.value) {
        addRecipient(this.value);
        this.value = '';
    }
});

function addRecipient(email) {
    const recipientsField = document.getElementById('recipients');
    const currentValue = recipientsField.value.trim();
    
    if (currentValue) {
        recipientsField.value = currentValue + ', ' + email;
    } else {
        recipientsField.value = email;
    }
}

// Load email template
async function loadTemplate(templateType) {
    try {
        const response = await fetch(`{{ route('admin.email.template') }}?template=${templateType}`);
        const data = await response.json();
        
        document.getElementById('subject').value = data.subject;
        document.getElementById('message').value = data.body;
    } catch (error) {
        console.error('Failed to load template:', error);
    }
}

// Debug helper (only updates if debug panel exists)
const isDebugMode = {{ config('app.debug') ? 'true' : 'false' }};
function updateDebug(field, value) {
    if (!isDebugMode) return;
    const el = document.getElementById(field);
    if (el) el.textContent = value;
}

// Test API directly
async function testAutocompleteAPI() {
    const debugEl = document.getElementById('debugApiTest');
    if (!debugEl) {
        console.error('Debug panel not available (APP_DEBUG may be false)');
        return;
    }
    
    debugEl.textContent = 'Testing...';
    
    try {
        const testUrl = `{{ route('admin.email.all-emails') }}?q=test`;
        updateDebug('debugLastQuery', 'test (manual)');
        
        const response = await fetch(testUrl);
        const text = await response.text();
        
        debugEl.textContent = `Status: ${response.status}\n\nResponse:\n${text}`;
        
        if (response.ok) {
            try {
                const json = JSON.parse(text);
                updateDebug('debugResultsCount', json.length + ' results');
                updateDebug('debugLastError', 'None');
            } catch (e) {
                updateDebug('debugLastError', 'Invalid JSON: ' + e.message);
            }
        } else {
            updateDebug('debugLastError', `HTTP ${response.status}: ${response.statusText}`);
        }
    } catch (error) {
        if (debugEl) {
            debugEl.textContent = 'Error: ' + error.message;
        }
        updateDebug('debugLastError', error.message);
    }
}

// Email autocomplete from bookings
(function() {
    const recipientsField = document.getElementById('recipients');
    const autocompleteDiv = document.getElementById('emailAutocomplete');
    let debounceTimer;
    let currentEmails = [];

    // Get the current word being typed (last word after comma/semicolon)
    function getCurrentWord() {
        const cursorPos = recipientsField.selectionStart;
        const textBeforeCursor = recipientsField.value.substring(0, cursorPos);
        const words = textBeforeCursor.split(/[,;]/);
        return words[words.length - 1].trim();
    }

    // Fetch email suggestions
    async function fetchEmailSuggestions(query) {
        updateDebug('debugLastQuery', query);
        updateDebug('debugCurrentWord', query);
        
        if (query.length < 2) {
            autocompleteDiv.classList.add('hidden');
            document.getElementById('autocompleteLoading').classList.add('hidden');
            updateDebug('debugDropdownVisible', 'No (query too short)');
            return;
        }

        try {
            // Show loading indicator
            autocompleteDiv.classList.add('hidden');
            document.getElementById('autocompleteLoading').classList.remove('hidden');
            
            const url = `{{ route('admin.email.all-emails') }}?q=${encodeURIComponent(query)}`;
            if (isDebugMode) console.log('Fetching:', url);
            
            const response = await fetch(url);
            if (isDebugMode) console.log('Response status:', response.status);
            
            // Hide loading indicator
            document.getElementById('autocompleteLoading').classList.add('hidden');
            
            if (!response.ok) {
                throw new Error(`HTTP ${response.status}: ${response.statusText}`);
            }
            
            const emails = await response.json();
            if (isDebugMode) console.log('Emails received:', emails);
            
            updateDebug('debugResultsCount', emails.length);
            updateDebug('debugLastError', 'None');
            
            currentEmails = emails;
            displaySuggestions(emails);
        } catch (error) {
            console.error('Failed to fetch email suggestions:', error);
            document.getElementById('autocompleteLoading').classList.add('hidden');
            updateDebug('debugLastError', error.message);
            updateDebug('debugResultsCount', 'Error');
        }
    }

    // Display suggestions with source badges
    function displaySuggestions(emails) {
        if (isDebugMode) console.log('displaySuggestions called with', emails.length, 'emails');
        
        if (emails.length === 0) {
            autocompleteDiv.classList.add('hidden');
            updateDebug('debugDropdownVisible', 'No (no results)');
            return;
        }

        const sourceColors = {
            'client': 'bg-blue-500/20 text-blue-300',
            'user': 'bg-purple-500/20 text-purple-300',
            'booking': 'bg-green-500/20 text-green-300'
        };

        const sourceIcons = {
            'client': '👤',
            'user': '👨‍💼',
            'booking': '📅'
        };

        const html = emails.map((item, index) => `
            <div class="px-3 py-2 hover:bg-gray-600 cursor-pointer text-sm text-white transition-colors"
                 data-index="${index}"
                 onclick="selectEmail('${item.email.replace(/'/g, "\\'")}')">
                <div class="flex items-center justify-between">
                    <div class="flex-1 min-w-0">
                        <div class="font-medium truncate">${escapeHtml(item.name)}</div>
                        <div class="text-xs text-gray-400 truncate">${escapeHtml(item.email)}</div>
                        ${item.company ? `<div class="text-xs text-gray-500 truncate mt-0.5">${escapeHtml(item.company)}</div>` : ''}
                    </div>
                    <span class="ml-2 px-2 py-0.5 text-xs rounded ${sourceColors[item.source] || 'bg-gray-500/20 text-gray-300'} flex-shrink-0">
                        ${sourceIcons[item.source] || '📧'} ${item.source}
                    </span>
                </div>
            </div>
        `).join('');

        autocompleteDiv.innerHTML = html;
        autocompleteDiv.classList.remove('hidden');
        updateDebug('debugDropdownVisible', 'Yes (' + emails.length + ' items)');
        if (isDebugMode) console.log('Dropdown shown with HTML:', html.substring(0, 100) + '...');
    }

    // Escape HTML to prevent XSS
    function escapeHtml(text) {
        const div = document.createElement('div');
        div.textContent = text;
        return div.innerHTML;
    }

    // Select an email from suggestions
    window.selectEmail = function(email) {
        const cursorPos = recipientsField.selectionStart;
        const textBeforeCursor = recipientsField.value.substring(0, cursorPos);
        const textAfterCursor = recipientsField.value.substring(cursorPos);
        
        // Split by commas/semicolons and replace the last word
        const parts = textBeforeCursor.split(/([,;])/);
        if (parts.length > 0) {
            parts[parts.length - 1] = ' ' + email;
        }
        
        // Only add comma if there's more text after cursor
        const newText = parts.join('') + (textAfterCursor.trim() ? ', ' + textAfterCursor.trim() : '');
        recipientsField.value = newText;
        
        // Place cursor after the inserted email (ready for next entry)
        const newCursorPos = parts.join('').length;
        recipientsField.setSelectionRange(newCursorPos, newCursorPos);
        
        autocompleteDiv.classList.add('hidden');
        recipientsField.focus();
    };

    // Listen for input
    recipientsField.addEventListener('input', function() {
        if (isDebugMode) console.log('Input event triggered');
        clearTimeout(debounceTimer);
        
        const currentWord = getCurrentWord();
        if (isDebugMode) console.log('Current word:', currentWord);
        updateDebug('debugCurrentWord', currentWord || '(empty)');
        
        if (currentWord.length < 2) {
            autocompleteDiv.classList.add('hidden');
            updateDebug('debugDropdownVisible', 'No (query too short: ' + currentWord.length + ' chars)');
            return;
        }

        debounceTimer = setTimeout(() => {
            if (isDebugMode) console.log('Debounce fired, fetching suggestions for:', currentWord);
            fetchEmailSuggestions(currentWord);
        }, 300);
    });

    // Hide suggestions when clicking outside
    document.addEventListener('click', function(e) {
        if (!recipientsField.contains(e.target) && !autocompleteDiv.contains(e.target)) {
            autocompleteDiv.classList.add('hidden');
        }
    });

    // Handle keyboard navigation (optional enhancement)
    recipientsField.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            autocompleteDiv.classList.add('hidden');
        }
    });
})();
</script>
@endsection
