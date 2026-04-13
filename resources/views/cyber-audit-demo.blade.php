<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber AI Audit - AI-Powered Cybersecurity Assessment</title>
    <meta name="description" content="Experience AI-powered cybersecurity assessment with our interactive demo. Get instant insights into your security posture.">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    
    <style>
        :root {
            --primary-color: #2563eb;
            --secondary-color: #1e40af;
            --accent-color: #3b82f6;
            --dark-bg: #0f172a;
            --card-bg: #1e293b;
            --text-light: #f8fafc;
            /* Lighten muted text for better contrast on dark backgrounds */
            --text-muted: #cbd5e1; /* slate-300 */
            --border-color: #334155;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Inter', sans-serif;
            background: var(--dark-bg);
            color: var(--text-light);
            line-height: 1.6;
        }

        .hero-section {
            background: linear-gradient(135deg, #1e3a8a 0%, #1e40af 50%, #3b82f6 100%);
            padding: 120px 0 80px;
            position: relative;
            overflow: hidden;
        }

        .hero-section::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M 10 0 L 0 0 0 10" fill="none" stroke="rgba(255,255,255,0.1)" stroke-width="0.5"/></pattern></defs><rect width="100" height="100" fill="url(%23grid)"/></svg>');
            opacity: 0.3;
        }

        .hero-content {
            position: relative;
            z-index: 2;
        }

        .hero-title {
            font-size: 3.5rem;
            font-weight: 700;
            margin-bottom: 1.5rem;
            background: linear-gradient(45deg, #ffffff, #e2e8f0);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .hero-subtitle {
            font-size: 1.25rem;
            color: var(--text-muted);
            margin-bottom: 2rem;
            max-width: 600px;
        }
        /* Ensure Bootstrap's .text-muted uses our higher-contrast muted color */
        .text-muted { color: var(--text-muted) !important; }

        .demo-section {
            padding: 80px 0;
            background: var(--card-bg);
        }

        .demo-card {
            background: var(--dark-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .chat-container {
            height: 400px;
            overflow-y: auto;
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 1rem;
            background: var(--dark-bg);
            margin-bottom: 1rem;
        }

        .chat-message {
            margin-bottom: 1rem;
            padding: 1rem 1.25rem;
            border-radius: 16px;
            max-width: 85%;
            position: relative;
            animation: messageSlideIn 0.3s ease-out;
        }

        .chat-message.user {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            margin-left: auto;
            box-shadow: 0 4px 12px rgba(37, 99, 235, 0.3);
        }

        .chat-message.ai {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }

        .chat-message strong {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
        }

        .chat-message.user strong {
            color: rgba(255, 255, 255, 0.9);
        }

        .chat-message.ai strong {
            color: var(--primary-color);
        }

        @keyframes messageSlideIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .demo-input {
            background: var(--card-bg);
            border: 2px solid var(--border-color);
            color: var(--text-light);
            border-radius: 12px;
            padding: 1rem 1.25rem;
            font-size: 1rem;
            transition: all 0.3s ease;
        }

        .demo-input:focus {
            background: var(--card-bg);
            border-color: var(--primary-color);
            color: var(--text-light);
            box-shadow: 0 0 0 0.3rem rgba(37, 99, 235, 0.2);
            outline: none;
        }

        .demo-input::placeholder {
            color: var(--text-muted);
            opacity: 0.7;
        }

        .input-group .btn {
            border-radius: 12px;
            margin-left: 0.5rem;
            padding: 1rem 1.5rem;
            transition: all 0.3s ease;
        }

        .input-group .btn:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 20px rgba(37, 99, 235, 0.3);
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .features-section {
            padding: 80px 0;
            background: var(--dark-bg);
        }

        .feature-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            padding: 2rem;
            text-align: center;
            transition: transform 0.3s ease;
        }

        .feature-card:hover {
            transform: translateY(-5px);
        }

        .feature-icon {
            font-size: 3rem;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .cta-section {
            padding: 80px 0;
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--secondary-color) 100%);
            text-align: center;
        }

        .footer {
            background: var(--card-bg);
            padding: 2rem 0;
            text-align: center;
            color: var(--text-muted);
        }

        .typing-indicator {
            display: none;
            padding: 0.75rem 1rem;
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 12px;
            margin-bottom: 1rem;
        }

        .typing-dots {
            display: inline-block;
        }

        .typing-dots span {
            display: inline-block;
            width: 8px;
            height: 8px;
            border-radius: 50%;
            background: var(--text-muted);
            margin: 0 2px;
            animation: typing 1.4s infinite ease-in-out;
        }

        .typing-dots span:nth-child(1) { animation-delay: -0.32s; }
        .typing-dots span:nth-child(2) { animation-delay: -0.16s; }

        @keyframes typing {
            0%, 80%, 100% { transform: scale(0); }
            40% { transform: scale(1); }
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-section {
                padding: 80px 0 60px;
            }
        }
    </style>
</head>
<body>
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row hero-content">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="hero-title">🔒 Cyber AI Audit .com</h1>
                    <p class="hero-subtitle">
                        Experience the future of cybersecurity assessment with our AI-powered platform. 
                        Get instant insights into your security posture and receive personalized recommendations.
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="#demo" class="btn btn-primary btn-lg">
                            <i class="bi bi-play-circle me-2"></i>Try Demo
                        </a>
                        <a href="https://houston1.oldlinecyber.com/landing2" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-envelope me-2"></i>Get Started
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Demo Section -->
    <section id="demo" class="demo-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="text-center mb-5">
                        <h2 class="h1 mb-3">Interactive AI Demo</h2>
                        <p class="text-muted">Ask our AI cybersecurity expert anything about your security concerns</p>
                    </div>
                    
                    <div class="demo-card">
                        <div class="chat-container" id="chatContainer">
                            <div class="chat-message ai">
                                <strong>Cyber AI Assistant:</strong> Hello! I'm your AI cybersecurity consultant. I can help you assess your security posture, identify vulnerabilities, and provide recommendations. What security concerns would you like to discuss today?
                            </div>
                        </div>
                        
                        <div class="typing-indicator" id="typingIndicator">
                            <div class="typing-dots">
                                <span></span>
                                <span></span>
                                <span></span>
                            </div>
                            AI is thinking...
                        </div>
                        
                        <div class="input-group">
                            <input type="text" class="form-control demo-input" id="messageInput" 
                                   placeholder="Ask about network security, data protection, compliance, etc..." 
                                   onkeypress="handleKeyPress(event)">
                            <button class="btn btn-primary" onclick="sendMessage()">
                                <i class="bi bi-send"></i>
                            </button>
                        </div>
                        
                        <div class="mt-3">
                            <small class="text-muted">
                                <i class="bi bi-lightbulb me-1"></i>
                                Try asking: "How can I protect against ransomware?" or "What are the latest cybersecurity threats?"
                            </small>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="h1 mb-3">Why Choose Cyber AI Audit?</h2>
                    <p class="text-muted">Powered by advanced AI technology to provide comprehensive security insights</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">🤖</div>
                        <h4>AI-Powered Analysis</h4>
                        <p class="text-muted">Advanced machine learning algorithms analyze your security posture and provide intelligent recommendations.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">⚡</div>
                        <h4>Instant Insights</h4>
                        <p class="text-muted">Get immediate feedback and actionable recommendations without waiting for manual security audits.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">🛡️</div>
                        <h4>Comprehensive Coverage</h4>
                        <p class="text-muted">Covers network security, data protection, compliance, threat intelligence, and more.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h2 class="h1 mb-3">Ready to Secure Your Business?</h2>
                    <p class="mb-4">Join hundreds of businesses that trust our AI-powered cybersecurity assessment platform.</p>
                    <a href="https://houston1.oldlinecyber.com/landing2" class="btn btn-light btn-lg">
                        <i class="bi bi-shield-check me-2"></i>Start Your Security Assessment
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p>&copy; 2024 Cyber AI Audit. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Demo responses for different cybersecurity topics
        const demoResponses = {
            "ransomware": "Ransomware protection requires a multi-layered approach:\n\n1. **Regular Backups**: Maintain offline, encrypted backups\n2. **Email Security**: Implement advanced filtering and user training\n3. **Network Segmentation**: Isolate critical systems\n4. **Patch Management**: Keep all systems updated\n5. **Access Controls**: Use least-privilege principles\n6. **Monitoring**: Deploy EDR/XDR solutions\n\nWould you like me to elaborate on any of these areas?",
            
            "threats": "Current top cybersecurity threats include:\n\n1. **Ransomware-as-a-Service**: Easily accessible to criminals\n2. **Supply Chain Attacks**: Targeting software dependencies\n3. **AI-Powered Attacks**: Automated, sophisticated phishing\n4. **Cloud Security**: Misconfigurations and data breaches\n5. **IoT Vulnerabilities**: Expanding attack surface\n6. **Social Engineering**: Human manipulation tactics\n\nWhat specific threat concerns you most?",
            
            "compliance": "Key compliance frameworks include:\n\n1. **GDPR**: Data protection for EU citizens\n2. **HIPAA**: Healthcare data security\n3. **SOX**: Financial reporting controls\n4. **PCI DSS**: Payment card security\n5. **ISO 27001**: Information security management\n6. **NIST CSF**: Cybersecurity framework\n\nWhich compliance requirements apply to your industry?",
            
            "network": "Network security best practices:\n\n1. **Firewalls**: Next-generation firewall deployment\n2. **VPN**: Secure remote access solutions\n3. **Network Monitoring**: Real-time threat detection\n4. **Segmentation**: Micro-segmentation for critical assets\n5. **Encryption**: Data in transit and at rest\n6. **Access Control**: Zero-trust network architecture\n\nWould you like a detailed network security assessment?",
            
            "data": "Data protection strategies:\n\n1. **Classification**: Identify sensitive data types\n2. **Encryption**: End-to-end data encryption\n3. **Access Controls**: Role-based permissions\n4. **Monitoring**: Data access and usage tracking\n5. **Backup**: Regular, secure data backups\n6. **Disposal**: Secure data destruction procedures\n\nWhat type of data are you most concerned about protecting?",
            
            "firewall": "Firewall best practices:\n\n1. **Next-Generation Firewalls**: Deploy NGFWs with deep packet inspection\n2. **Default Deny**: Block all traffic by default, allow only necessary\n3. **Segmentation**: Create security zones for different network areas\n4. **Regular Updates**: Keep firewall rules and firmware current\n5. **Monitoring**: Log and monitor all firewall activity\n6. **Testing**: Regularly test firewall effectiveness\n\nWould you like specific firewall recommendations?",
            
            "password": "Password security best practices:\n\n1. **Strong Passwords**: Use 12+ characters with complexity\n2. **Password Managers**: Use reputable password managers\n3. **Multi-Factor Authentication**: Enable MFA everywhere possible\n4. **Regular Changes**: Update passwords regularly\n5. **Unique Passwords**: Never reuse passwords across accounts\n6. **Training**: Educate users on password security\n\nWhat's your current password policy?",
            
            "phishing": "Phishing protection strategies:\n\n1. **Email Filtering**: Advanced spam and phishing filters\n2. **User Training**: Regular security awareness training\n3. **Multi-Factor Authentication**: Protect against credential theft\n4. **URL Filtering**: Block known malicious domains\n5. **Reporting**: Encourage users to report suspicious emails\n6. **Simulations**: Regular phishing simulation exercises\n\nHow often do you conduct security training?"
        };

        const defaultResponses = [
            "I'd be happy to help you with that! Could you provide more specific details about your security concern?",
            "That's an important security consideration. Let me know what specific aspects you'd like to explore further.",
            "Great question! I can provide detailed guidance on this topic. What's your current security setup?",
            "I understand your concern. Let's dive deeper into this area. What specific challenges are you facing?",
            "That's a great security question. Let me help you understand the best practices for this area.",
            "I can provide comprehensive guidance on this topic. What specific aspects would you like to focus on?"
        ];

        // Initialize chat when page loads
        document.addEventListener('DOMContentLoaded', function() {
            console.log('Chat demo initialized');
            
            // Focus on input when page loads
            const messageInput = document.getElementById('messageInput');
            if (messageInput) {
                messageInput.focus();
            }
        });

        function sendMessage() {
            const input = document.getElementById('messageInput');
            const message = input.value.trim();
            
            if (!message) {
                console.log('Empty message, not sending');
                return;
            }
            
            console.log('Sending message:', message);
            
            // Add user message
            addMessage(message, 'user');
            input.value = '';
            
            // Show typing indicator
            showTypingIndicator();
            
            // Simulate AI response with random delay
            const delay = 1000 + Math.random() * 2000;
            setTimeout(() => {
                hideTypingIndicator();
                const response = generateResponse(message);
                addMessage(response, 'ai');
            }, delay);
        }

        function addMessage(text, sender) {
            const container = document.getElementById('chatContainer');
            if (!container) {
                console.error('Chat container not found');
                return;
            }
            
            const messageDiv = document.createElement('div');
            messageDiv.className = `chat-message ${sender}`;
            
            if (sender === 'ai') {
                messageDiv.innerHTML = `<strong>Cyber AI Assistant:</strong> ${text.replace(/\n/g, '<br>')}`;
            } else {
                messageDiv.innerHTML = `<strong>You:</strong> ${text}`;
            }
            
            container.appendChild(messageDiv);
            container.scrollTop = container.scrollHeight;
            
            console.log('Message added:', sender, text.substring(0, 50) + '...');
        }

        function generateResponse(message) {
            const lowerMessage = message.toLowerCase();
            
            // Check for specific keywords
            for (const [keyword, response] of Object.entries(demoResponses)) {
                if (lowerMessage.includes(keyword)) {
                    console.log('Found keyword match:', keyword);
                    return response;
                }
            }
            
            // Return random default response
            const randomResponse = defaultResponses[Math.floor(Math.random() * defaultResponses.length)];
            console.log('Using default response');
            return randomResponse;
        }

        function showTypingIndicator() {
            const indicator = document.getElementById('typingIndicator');
            if (indicator) {
                indicator.style.display = 'block';
                const container = document.getElementById('chatContainer');
                if (container) {
                    container.scrollTop = container.scrollHeight;
                }
            }
        }

        function hideTypingIndicator() {
            const indicator = document.getElementById('typingIndicator');
            if (indicator) {
                indicator.style.display = 'none';
            }
        }

        function handleKeyPress(event) {
            if (event.key === 'Enter') {
                event.preventDefault();
                sendMessage();
            }
        }

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Add click event listener to send button as backup
        document.addEventListener('DOMContentLoaded', function() {
            const sendButton = document.querySelector('button[onclick="sendMessage()"]');
            if (sendButton) {
                sendButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    sendMessage();
                });
            }
        });
    </script>
</body>
</html>
