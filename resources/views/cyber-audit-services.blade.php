<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cyber AI Audit Services - Small Business Cybersecurity Consulting</title>
    <meta name="description" content="Professional cybersecurity audit services for small businesses. Choose from our tiered service offerings to protect your business with AI-powered security assessments.">
    
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
            --success-color: #10b981;
            --warning-color: #f59e0b;
            --danger-color: #ef4444;
            --dark-bg: #0f172a;
            --card-bg: #1e293b;
            --text-light: #f8fafc;
            --text-muted: #94a3b8;
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

        .services-section {
            padding: 80px 0;
            background: var(--card-bg);
        }

        .service-card {
            background: var(--dark-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            margin-bottom: 2rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .service-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
        }

        .service-card.featured {
            border: 2px solid var(--primary-color);
            transform: scale(1.05);
        }

        .service-card.featured::before {
            content: 'MOST POPULAR';
            position: absolute;
            top: 0;
            right: 0;
            background: var(--primary-color);
            color: white;
            padding: 0.5rem 1rem;
            font-size: 0.75rem;
            font-weight: 600;
            border-radius: 0 16px 0 16px;
        }

        .service-tier {
            font-size: 0.875rem;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 1px;
            margin-bottom: 0.5rem;
        }

        .service-title {
            font-size: 1.5rem;
            font-weight: 600;
            margin-bottom: 1rem;
            color: var(--text-light);
        }

        .service-price {
            font-size: 2.5rem;
            font-weight: 700;
            color: var(--primary-color);
            margin-bottom: 1rem;
        }

        .service-price .period {
            font-size: 1rem;
            color: var(--text-muted);
            font-weight: 400;
        }

        .service-features {
            list-style: none;
            padding: 0;
            margin-bottom: 2rem;
        }

        .service-features li {
            padding: 0.5rem 0;
            color: var(--text-light);
            position: relative;
            padding-left: 1.5rem;
        }

        .service-features li::before {
            content: '✓';
            position: absolute;
            left: 0;
            color: var(--success-color);
            font-weight: 600;
        }

        .service-features li.not-included {
            color: var(--text-muted);
        }

        .service-features li.not-included::before {
            content: '×';
            color: var(--text-muted);
        }

        .btn-primary {
            background: var(--primary-color);
            border: none;
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-primary:hover {
            background: var(--secondary-color);
            transform: translateY(-2px);
        }

        .btn-outline {
            background: transparent;
            border: 2px solid var(--primary-color);
            color: var(--primary-color);
            padding: 0.75rem 1.5rem;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s ease;
            width: 100%;
        }

        .btn-outline:hover {
            background: var(--primary-color);
            color: white;
            transform: translateY(-2px);
        }

        .form-section {
            padding: 80px 0;
            background: var(--dark-bg);
        }

        .form-card {
            background: var(--card-bg);
            border: 1px solid var(--border-color);
            border-radius: 16px;
            padding: 2rem;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
        }

        .form-control {
            background: var(--dark-bg);
            border: 1px solid var(--border-color);
            color: var(--text-light);
            border-radius: 8px;
            padding: 0.75rem 1rem;
        }

        .form-control:focus {
            background: var(--dark-bg);
            border-color: var(--primary-color);
            color: var(--text-light);
            box-shadow: 0 0 0 0.3rem rgba(37, 99, 235, 0.2);
        }

        .form-control::placeholder {
            color: var(--text-muted);
        }

        .form-label {
            color: var(--text-light);
            font-weight: 500;
            margin-bottom: 0.5rem;
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

        .badge {
            font-size: 0.75rem;
            padding: 0.25rem 0.5rem;
            border-radius: 4px;
            font-weight: 500;
        }

        .badge-success {
            background: var(--success-color);
            color: white;
        }

        .badge-warning {
            background: var(--warning-color);
            color: white;
        }

        .badge-danger {
            background: var(--danger-color);
            color: white;
        }

        @media (max-width: 768px) {
            .hero-title {
                font-size: 2.5rem;
            }
            
            .hero-section {
                padding: 80px 0 60px;
            }

            .service-card.featured {
                transform: none;
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
                    <h1 class="hero-title">🔒 Cyber AI Audit Services</h1>
                    <p class="hero-subtitle">
                        Professional cybersecurity consulting for small businesses. Choose the perfect audit tier to protect your business with AI-powered security assessments and expert guidance.
                    </p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="#services" class="btn btn-primary btn-lg">
                            <i class="bi bi-shield-check me-2"></i>View Services
                        </a>
                        <a href="#contact" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-envelope me-2"></i>Get Started
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Services Section -->
    <section id="services" class="services-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center mb-5">
                    <h2 class="h1 mb-3">Choose Your Security Tier</h2>
                    <p class="text-muted">Select the perfect cybersecurity audit package for your business needs</p>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Basic Tier -->
                <div class="col-lg-4">
                    <div class="service-card">
                        <div class="service-tier">Basic</div>
                        <h3 class="service-title">Security Assessment</h3>
                        <div class="service-price">
                            $997 <span class="period">one-time</span>
                        </div>
                        <ul class="service-features">
                            <li>AI-powered security questionnaire</li>
                            <li>Basic vulnerability assessment</li>
                            <li>Security posture report (15 pages)</li>
                            <li>Top 10 security recommendations</li>
                            <li>Email support (48-hour response)</li>
                            <li>30-day follow-up consultation</li>
                            <li class="not-included">Penetration testing</li>
                            <li class="not-included">Compliance audit</li>
                            <li class="not-included">Ongoing monitoring</li>
                        </ul>
                        <button class="btn btn-outline" onclick="selectService('Basic', 997)">
                            Choose Basic
                        </button>
                    </div>
                </div>

                <!-- Professional Tier -->
                <div class="col-lg-4">
                    <div class="service-card featured">
                        <div class="service-tier">Professional</div>
                        <h3 class="service-title">Comprehensive Audit</h3>
                        <div class="service-price">
                            $2,497 <span class="period">one-time</span>
                        </div>
                        <ul class="service-features">
                            <li>Everything in Basic, plus:</li>
                            <li>Comprehensive security assessment</li>
                            <li>Network vulnerability scan</li>
                            <li>Detailed audit report (30+ pages)</li>
                            <li>Custom security roadmap</li>
                            <li>Priority support (24-hour response)</li>
                            <li>90-day implementation guidance</li>
                            <li>Compliance framework review</li>
                            <li>Security policy templates</li>
                            <li>Employee training recommendations</li>
                        </ul>
                        <button class="btn btn-primary" onclick="selectService('Professional', 2497)">
                            Choose Professional
                        </button>
                    </div>
                </div>

                <!-- Enterprise Tier -->
                <div class="col-lg-4">
                    <div class="service-card">
                        <div class="service-tier">Enterprise</div>
                        <h3 class="service-title">Full-Service Security</h3>
                        <div class="service-price">
                            $4,997 <span class="period">one-time</span>
                        </div>
                        <ul class="service-features">
                            <li>Everything in Professional, plus:</li>
                            <li>Penetration testing</li>
                            <li>Social engineering assessment</li>
                            <li>Comprehensive compliance audit</li>
                            <li>Security architecture review</li>
                            <li>Incident response planning</li>
                            <li>6-month implementation support</li>
                            <li>Quarterly security reviews</li>
                            <li>Dedicated security consultant</li>
                            <li>Custom security training program</li>
                        </ul>
                        <button class="btn btn-outline" onclick="selectService('Enterprise', 4997)">
                            Choose Enterprise
                        </button>
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
                    <h2 class="h1 mb-3">Why Choose Our Cyber Audit Services?</h2>
                    <p class="text-muted">AI-powered insights combined with expert cybersecurity consulting</p>
                </div>
            </div>
            
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">🤖</div>
                        <h4>AI-Powered Analysis</h4>
                        <p class="text-muted">Advanced machine learning algorithms analyze your security posture and provide intelligent, data-driven recommendations.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">👨‍💼</div>
                        <h4>Expert Consultants</h4>
                        <p class="text-muted">Certified cybersecurity professionals with years of experience in protecting small businesses from cyber threats.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">📊</div>
                        <h4>Actionable Reports</h4>
                        <p class="text-muted">Receive detailed, easy-to-understand reports with prioritized recommendations and implementation guidance.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">🛡️</div>
                        <h4>Compliance Ready</h4>
                        <p class="text-muted">Ensure your business meets industry standards and regulatory requirements with our compliance-focused audits.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">⚡</div>
                        <h4>Quick Turnaround</h4>
                        <p class="text-muted">Get your security assessment completed within 5-10 business days, depending on the tier selected.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-card">
                        <div class="feature-icon">🔗</div>
                        <h4>CLIENTBRIDGE Integration</h4>
                        <p class="text-muted">Seamlessly integrate with our CLIENTBRIDGE platform for ongoing file sharing, messaging, and collaboration.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Contact Form Section -->
    <section id="contact" class="form-section">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto">
                    <div class="text-center mb-5">
                        <h2 class="h1 mb-3">Get Started with Your Cyber Audit</h2>
                        <p class="text-muted">Fill out the form below and we'll get back to you within 24 hours to discuss your security needs</p>
                    </div>
                    
                    <div class="form-card">
                        @if (session('success'))
                            <div class="alert alert-success text-center mb-4">
                                {{ session('success') }}
                            </div>
                        @endif
                        @if (session('failure'))
                            <div class="alert alert-danger text-center mb-4">
                                {{ session('failure') }}
                            </div>
                        @endif

                        <form action="{{ route('leadform.store') }}" method="POST">
                            @csrf
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="name" class="form-label">Full Name *</label>
                                    <input type="text" class="form-control" id="name" name="name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="email" class="form-label">Email Address *</label>
                                    <input type="email" class="form-control" id="email" name="email" required>
                                </div>
                            </div>
                            
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label for="company" class="form-label">Company Name</label>
                                    <input type="text" class="form-control" id="company" name="company">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label for="phone" class="form-label">Phone Number</label>
                                    <input type="tel" class="form-control" id="phone" name="phone">
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="service_tier" class="form-label">Preferred Service Tier *</label>
                                <select class="form-control" id="service_tier" name="service_tier" required>
                                    <option value="">Select a service tier</option>
                                    <option value="Basic">Basic - Security Assessment ($997)</option>
                                    <option value="Professional">Professional - Comprehensive Audit ($2,497)</option>
                                    <option value="Enterprise">Enterprise - Full-Service Security ($4,997)</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="business_type" class="form-label">Business Type *</label>
                                <select class="form-control" id="business_type" name="business_type" required>
                                    <option value="">Select your business type</option>
                                    <option value="Healthcare">Healthcare</option>
                                    <option value="Financial Services">Financial Services</option>
                                    <option value="Legal">Legal</option>
                                    <option value="Real Estate">Real Estate</option>
                                    <option value="Retail">Retail</option>
                                    <option value="Manufacturing">Manufacturing</option>
                                    <option value="Technology">Technology</option>
                                    <option value="Consulting">Consulting</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="employee_count" class="form-label">Number of Employees</label>
                                <select class="form-control" id="employee_count" name="employee_count">
                                    <option value="">Select employee count</option>
                                    <option value="1-10">1-10 employees</option>
                                    <option value="11-25">11-25 employees</option>
                                    <option value="26-50">26-50 employees</option>
                                    <option value="51-100">51-100 employees</option>
                                    <option value="100+">100+ employees</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="security_concerns" class="form-label">Primary Security Concerns</label>
                                <div class="row">
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="security_concerns[]" value="Data Breach" id="concern1">
                                            <label class="form-check-label" for="concern1">Data Breach Protection</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="security_concerns[]" value="Ransomware" id="concern2">
                                            <label class="form-check-label" for="concern2">Ransomware Protection</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="security_concerns[]" value="Compliance" id="concern3">
                                            <label class="form-check-label" for="concern3">Regulatory Compliance</label>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="security_concerns[]" value="Employee Training" id="concern4">
                                            <label class="form-check-label" for="concern4">Employee Security Training</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="security_concerns[]" value="Network Security" id="concern5">
                                            <label class="form-check-label" for="concern5">Network Security</label>
                                        </div>
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="security_concerns[]" value="Cloud Security" id="concern6">
                                            <label class="form-check-label" for="concern6">Cloud Security</label>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mb-3">
                                <label for="message" class="form-label">Additional Information</label>
                                <textarea class="form-control" id="message" name="message" rows="4" 
                                    placeholder="Tell us about your current security setup, specific concerns, or any questions you have..."></textarea>
                            </div>

                            <input type="hidden" name="source_site" value="{{ request()->getHost() }}">
                            <input type="hidden" name="source_page" value="cyber-audit-services">
                            
                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="bi bi-shield-check me-2"></i>Request Cyber Audit Consultation
                            </button>
                        </form>
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
                    <p class="mb-4">Join hundreds of small businesses that trust our AI-powered cybersecurity assessment platform.</p>
                    <div class="d-flex gap-3 justify-content-center flex-wrap">
                        <a href="#contact" class="btn btn-light btn-lg">
                            <i class="bi bi-shield-check me-2"></i>Start Your Security Assessment
                        </a>
                        <a href="/cyber-audit-demo" class="btn btn-outline-light btn-lg">
                            <i class="bi bi-play-circle me-2"></i>Try Our Demo
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Cyber AI Audit Services</h5>
                    <p class="text-muted">Professional cybersecurity consulting for small businesses</p>
                </div>
                <div class="col-md-6 text-md-end">
                    <p class="text-muted">
                        <a href="https://clientbridge.app" target="_blank" class="text-decoration-none">CLIENTBRIDGE.app</a> | 
                        <a href="/cyber-audit-demo" class="text-decoration-none">Demo</a>
                    </p>
                </div>
            </div>
            <hr class="my-3" style="border-color: var(--border-color);">
            <p class="text-center text-muted mb-0">&copy; 2024 Cyber AI Audit. All rights reserved.</p>
        </div>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        function selectService(tier, price) {
            document.getElementById('service_tier').value = tier;
            
            // Scroll to contact form
            document.getElementById('contact').scrollIntoView({
                behavior: 'smooth',
                block: 'start'
            });
            
            // Highlight the selected tier
            const options = document.querySelectorAll('#service_tier option');
            options.forEach(option => {
                if (option.value === tier) {
                    option.selected = true;
                }
            });
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

        // Form validation
        document.addEventListener('DOMContentLoaded', function() {
            const form = document.querySelector('form');
            if (form) {
                form.addEventListener('submit', function(e) {
                    const requiredFields = form.querySelectorAll('[required]');
                    let isValid = true;
                    
                    requiredFields.forEach(field => {
                        if (!field.value.trim()) {
                            isValid = false;
                            field.classList.add('is-invalid');
                        } else {
                            field.classList.remove('is-invalid');
                        }
                    });
                    
                    if (!isValid) {
                        e.preventDefault();
                        alert('Please fill in all required fields.');
                    }
                });
            }
        });
    </script>
</body>
</html>
