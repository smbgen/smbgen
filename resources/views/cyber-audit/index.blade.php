@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-6">
    <div class="flex justify-center">
        <div class="w-full lg:w-4/5">
            <!-- Header -->
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h2 class="text-2xl font-bold text-gray-900 dark:text-gray-100 mb-1">🔒 Cyber Audit Assistant</h2>
                    <p class="text-gray-600 dark:text-gray-400 mb-0">AI-powered cybersecurity assessment and guidance</p>
                </div>
                <div class="flex space-x-2">
                    <button id="clearHistory" class="btn-secondary text-sm">
                        🗑️ Clear History
                    </button>
                    <a href="{{ route('dashboard') }}" class="btn-primary text-sm">
                        ← Back to Dashboard
                    </a>
                </div>
            </div>

            <!-- Chat Container -->
            <div class="card shadow-lg">
                <div class="bg-blue-600 text-white px-6 py-4 rounded-t-lg">
                    <div class="flex items-center">
                        <div class="mr-3">
                            <div class="bg-white rounded-full flex items-center justify-center w-10 h-10">
                                <span class="text-blue-600 text-lg">🛡️</span>
                            </div>
                        </div>
                        <div class="flex-1">
                            <h5 class="text-lg font-semibold mb-0">{{ config('app.company_name', 'CLIENTBRIDGE') }} Cyber Audit Bot</h5>
                            <small class="opacity-75">Your AI cybersecurity consultant</small>
                        </div>
                        <div class="ml-auto">
                            <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full bg-green-100 text-green-800">Online</span>
                        </div>
                    </div>
                </div>
                
                <div class="p-0">
                    <!-- Chat Messages -->
                    <div id="chatMessages" class="h-96 overflow-y-auto p-4">
                        <!-- Messages will be inserted here -->
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-800">
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2 mb-3">
                            <button class="btn-secondary text-xs quick-action" data-message="password security">
                                🔐 Passwords
                            </button>
                            <button class="btn-secondary text-xs quick-action" data-message="network security">
                                🛡️ Network
                            </button>
                            <button class="btn-secondary text-xs quick-action" data-message="data backup">
                                💾 Backup
                            </button>
                            <button class="btn-secondary text-xs quick-action" data-message="employee training">
                                👥 Training
                            </button>
                        </div>
                        <div class="grid grid-cols-2 md:grid-cols-4 gap-2">
                            <button class="btn-secondary text-xs quick-action" data-message="compliance">
                                📋 Compliance
                            </button>
                            <button class="btn-secondary text-xs quick-action" data-message="phishing">
                                🎣 Phishing
                            </button>
                            <button class="btn-secondary text-xs quick-action" data-message="general security">
                                🔍 General
                            </button>
                            <button class="btn-success text-xs quick-action" data-message="hello">
                                🚀 Start Audit
                            </button>
                        </div>
                    </div>
                    
                    <!-- Message Input -->
                    <div class="p-4 border-t border-gray-200 dark:border-gray-700">
                        <form id="chatForm" class="flex gap-2">
                            <div class="flex-1">
                                <input type="text" id="messageInput" class="w-full px-3 py-2 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 rounded-lg text-gray-900 dark:text-gray-100 placeholder-gray-400 dark:placeholder-gray-400 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent" placeholder="Ask about cybersecurity best practices..." maxlength="1000">
                            </div>
                            <button type="submit" class="btn-primary" id="sendButton">
                                📤 Send
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.chat-container {
    scroll-behavior: smooth;
}

.message {
    margin-bottom: 1rem;
    animation: fadeIn 0.3s ease-in;
}

.message.user {
    text-align: right;
}

.message.assistant {
    text-align: left;
}

.message-content {
    display: inline-block;
    max-width: 80%;
    padding: 0.75rem 1rem;
    border-radius: 1rem;
    word-wrap: break-word;
}

.message.user .message-content {
    background-color: #3b82f6;
    color: white;
    border-bottom-right-radius: 0.25rem;
}

.message.assistant .message-content {
    background-color: #f3f4f6;
    border: 1px solid #d1d5db;
    border-bottom-left-radius: 0.25rem;
    color: #111827;
}

html.dark .message.assistant .message-content {
    background-color: #374151;
    border: 1px solid #4b5563;
    color: #f3f4f6;
}

.message-timestamp {
    font-size: 0.75rem;
    color: #9ca3af;
    margin-top: 0.25rem;
}

.typing-indicator {
    display: none;
    padding: 0.75rem 1rem;
    background-color: #f3f4f6;
    border: 1px solid #d1d5db;
    border-radius: 1rem;
    border-bottom-left-radius: 0.25rem;
    max-width: 80%;
    color: #111827;
}

html.dark .typing-indicator {
    background-color: #374151;
    border: 1px solid #4b5563;
    color: #f3f4f6;
}

.typing-dots {
    display: inline-block;
    color: inherit;
}

html.dark .typing-dots {
    color: #f3f4f6;
}

.typing-dots::after {
    content: '';
    animation: typing 1.5s infinite;
}

@keyframes typing {
    0%, 20% { content: ''; }
    40% { content: '.'; }
    60% { content: '..'; }
    80%, 100% { content: '...'; }
}

@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}

.quick-action {
    transition: all 0.2s ease;
}

.quick-action:hover {
    transform: translateY(-1px);
    box-shadow: 0 2px 4px rgba(0,0,0,0.1);
}

#messageInput:focus {
    box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    border-color: #3b82f6;
}

html.dark #messageInput {
    background-color: #374151;
    border-color: #4b5563;
    color: #f3f4f6;
}
</style>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const chatMessages = document.getElementById('chatMessages');
    const chatForm = document.getElementById('chatForm');
    const messageInput = document.getElementById('messageInput');
    const sendButton = document.getElementById('sendButton');
    const clearHistoryBtn = document.getElementById('clearHistory');
    
    // Load conversation history
    loadConversationHistory();
    
    // Send welcome message if no history
    if (chatMessages.children.length === 0) {
        setTimeout(() => {
            addMessage('assistant', '🔒 Welcome to your Cyber Audit Assistant! I\'m here to help you assess and improve your cybersecurity posture.\n\nI can help you with:\n• Password and authentication policies\n• Network security and firewalls\n• Data backup and recovery\n• Employee security training\n• Compliance requirements (GDPR, HIPAA, etc.)\n• Phishing and social engineering awareness\n\nWhat aspect of cybersecurity would you like to audit first?');
        }, 500);
    }
    
    // Handle form submission
    chatForm.addEventListener('submit', function(e) {
        e.preventDefault();
        const message = messageInput.value.trim();
        if (message) {
            sendMessage(message);
            messageInput.value = '';
        }
    });
    
    // Handle quick action buttons
    document.querySelectorAll('.quick-action').forEach(button => {
        button.addEventListener('click', function() {
            const message = this.dataset.message;
            sendMessage(message);
        });
    });
    
    // Handle clear history
    clearHistoryBtn.addEventListener('click', function() {
        if (confirm('Are you sure you want to clear the conversation history?')) {
            fetch('{{ route("cyber-audit.clear-history") }}', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Content-Type': 'application/json',
                }
            })
            .then(response => response.json())
            .then(data => {
                chatMessages.innerHTML = '';
                setTimeout(() => {
                    addMessage('assistant', '🔒 Welcome to your Cyber Audit Assistant! I\'m here to help you assess and improve your cybersecurity posture.\n\nI can help you with:\n• Password and authentication policies\n• Network security and firewalls\n• Data backup and recovery\n• Employee security training\n• Compliance requirements (GDPR, HIPAA, etc.)\n• Phishing and social engineering awareness\n\nWhat aspect of cybersecurity would you like to audit first?');
                }, 500);
            });
        }
    });
    
    function sendMessage(message) {
        // Add user message
        addMessage('user', message);
        
        // Show typing indicator
        showTypingIndicator();
        
        // Disable input
        messageInput.disabled = true;
        sendButton.disabled = true;
        
        // Send to server
        fetch('{{ route("cyber-audit.chat") }}', {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ message: message })
        })
        .then(response => response.json())
        .then(data => {
            hideTypingIndicator();
            addMessage('assistant', data.response);
            
            // Re-enable input
            messageInput.disabled = false;
            sendButton.disabled = false;
            messageInput.focus();
        })
        .catch(error => {
            hideTypingIndicator();
            addMessage('assistant', 'Sorry, I encountered an error. Please try again.');
            messageInput.disabled = false;
            sendButton.disabled = false;
            messageInput.focus();
        });
    }
    
    function addMessage(role, content) {
        const messageDiv = document.createElement('div');
        messageDiv.className = `message ${role}`;
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'message-content';
        contentDiv.innerHTML = content.replace(/\n/g, '<br>');
        
        const timestampDiv = document.createElement('div');
        timestampDiv.className = 'message-timestamp';
        timestampDiv.textContent = new Date().toLocaleTimeString();
        
        messageDiv.appendChild(contentDiv);
        messageDiv.appendChild(timestampDiv);
        
        chatMessages.appendChild(messageDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    function showTypingIndicator() {
        const typingDiv = document.createElement('div');
        typingDiv.className = 'message assistant';
        typingDiv.id = 'typingIndicator';
        
        const contentDiv = document.createElement('div');
        contentDiv.className = 'typing-indicator';
        contentDiv.innerHTML = '<span class="typing-dots">AI is thinking</span>';
        
        typingDiv.appendChild(contentDiv);
        chatMessages.appendChild(typingDiv);
        chatMessages.scrollTop = chatMessages.scrollHeight;
    }
    
    function hideTypingIndicator() {
        const typingIndicator = document.getElementById('typingIndicator');
        if (typingIndicator) {
            typingIndicator.remove();
        }
    }
    
    function loadConversationHistory() {
        // This would load from session if needed
        // For now, we'll start fresh each time
    }
});
</script>
@endsection
