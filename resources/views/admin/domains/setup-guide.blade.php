@extends('layouts.admin')

@section('title', 'Domain Setup Guide')

@section('content')
<div class="max-w-5xl mx-auto py-6">
    <!-- Header -->
    <div class="mb-8">
        <a href="{{ route('admin.domains.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700 dark:text-blue-400 mb-4">
            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
            </svg>
            Back to Domains
        </a>
        <h1 class="text-3xl font-bold text-gray-900 dark:text-white">Domain Connection Guide</h1>
        <p class="mt-2 text-gray-600 dark:text-gray-400">Step-by-step instructions for connecting your custom domain</p>
    </div>

    <!-- Overview Card -->
    <div class="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6 mb-8">
        <div class="flex items-start">
            <div class="flex-shrink-0 bg-blue-600 rounded-lg p-3">
                <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Your Default Domain</h3>
                <p class="text-gray-700 dark:text-gray-300 mb-2">
                    Your ClientBridge instance is accessible at: 
                    <code class="bg-white dark:bg-gray-800 px-2 py-1 rounded text-sm font-mono border border-gray-300 dark:border-gray-600">
                        {{ $defaultDomain ? $defaultDomain->domain : 'your-business.clientbridge.app' }}
                    </code>
                </p>
                <p class="text-gray-600 dark:text-gray-400 text-sm">
                    You can also connect your own custom domain (e.g., www.yourbusiness.com) by following this guide.
                </p>
            </div>
        </div>
    </div>

    <!-- Quick Start Steps -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-8 overflow-hidden">
        <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Quick Start</h2>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- Step 1 -->
                <div class="flex">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">1</div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Add Domain in ClientBridge</h3>
                        <p class="text-gray-600 dark:text-gray-400 mb-2">Navigate to Settings → Domains and add your custom domain.</p>
                        <a href="{{ route('admin.domains.index') }}" class="text-blue-600 hover:text-blue-700 text-sm font-medium">
                            Go to Domain Settings →
                        </a>
                    </div>
                </div>

                <!-- Step 2 -->
                <div class="flex">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">2</div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Configure DNS Settings</h3>
                        <p class="text-gray-600 dark:text-gray-400">Update DNS records at your domain registrar (detailed below).</p>
                    </div>
                </div>

                <!-- Step 3 -->
                <div class="flex">
                    <div class="flex-shrink-0 w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-bold">3</div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Verify Connection</h3>
                        <p class="text-gray-600 dark:text-gray-400">Wait 15-30 minutes for DNS propagation, then verify in ClientBridge.</p>
                    </div>
                </div>

                <!-- Step 4 -->
                <div class="flex">
                    <div class="flex-shrink-0 w-10 h-10 bg-green-600 rounded-full flex items-center justify-center text-white font-bold">✓</div>
                    <div class="ml-4 flex-1">
                        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">SSL Automatically Activated</h3>
                        <p class="text-gray-600 dark:text-gray-400">HTTPS will be enabled within 24 hours of verification.</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- DNS Configuration -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-8 overflow-hidden">
        <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">DNS Configuration</h2>
        </div>
        <div class="p-6">
            <p class="text-gray-600 dark:text-gray-400 mb-6">Choose the appropriate method based on your domain type:</p>

            <!-- Subdomain Method -->
            <div class="mb-8">
                <div class="flex items-center mb-4">
                    <span class="bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-200 text-xs font-semibold px-3 py-1 rounded-full">RECOMMENDED</span>
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white ml-3">Option A: Subdomain (e.g., www.yourbusiness.com)</h3>
                </div>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Add a CNAME record:</p>
                    <div class="bg-gray-900 rounded-md p-4 font-mono text-sm text-green-400 overflow-x-auto">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <span class="text-gray-500">Type:</span>
                                <span class="ml-2">CNAME</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Name:</span>
                                <span class="ml-2">www</span>
                            </div>
                            <div>
                                <span class="text-gray-500">TTL:</span>
                                <span class="ml-2">3600</span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-gray-500">Value:</span>
                            <span class="ml-2">{{ $defaultDomain ? $defaultDomain->domain : 'your-business.clientbridge.app' }}</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Root Domain Method -->
            <div class="mb-6">
                <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-4">Option B: Root Domain (e.g., yourbusiness.com)</h3>
                <div class="bg-gray-50 dark:bg-gray-900 rounded-lg p-4 border border-gray-200 dark:border-gray-700">
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-3">Add an A record:</p>
                    <div class="bg-gray-900 rounded-md p-4 font-mono text-sm text-green-400 overflow-x-auto">
                        <div class="grid grid-cols-3 gap-4">
                            <div>
                                <span class="text-gray-500">Type:</span>
                                <span class="ml-2">A</span>
                            </div>
                            <div>
                                <span class="text-gray-500">Name:</span>
                                <span class="ml-2">@</span>
                            </div>
                            <div>
                                <span class="text-gray-500">TTL:</span>
                                <span class="ml-2">3600</span>
                            </div>
                        </div>
                        <div class="mt-2">
                            <span class="text-gray-500">Value:</span>
                            <span class="ml-2">{{ $serverIp }}</span>
                        </div>
                    </div>
                    <p class="text-xs text-yellow-600 dark:text-yellow-400 mt-3">
                        <strong>Note:</strong> Some registrars support ALIAS or CNAME flattening for root domains.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Provider Guides -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-8 overflow-hidden">
        <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Provider-Specific Instructions</h2>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <!-- GoDaddy -->
                <div class="border-l-4 border-blue-500 pl-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">GoDaddy</h3>
                    <ol class="list-decimal list-inside space-y-1 text-gray-600 dark:text-gray-400">
                        <li>Log in to your GoDaddy account</li>
                        <li>Go to <strong>My Products</strong> → <strong>DNS</strong></li>
                        <li>Click <strong>Add</strong> in the Records section</li>
                        <li>Select record type and fill in details</li>
                        <li>Click <strong>Save</strong></li>
                    </ol>
                </div>

                <!-- Namecheap -->
                <div class="border-l-4 border-orange-500 pl-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Namecheap</h3>
                    <ol class="list-decimal list-inside space-y-1 text-gray-600 dark:text-gray-400">
                        <li>Log in to Namecheap</li>
                        <li>Go to <strong>Domain List</strong> → Select your domain</li>
                        <li>Click <strong>Advanced DNS</strong></li>
                        <li>Click <strong>Add New Record</strong></li>
                        <li>Select type, fill in details, and save</li>
                    </ol>
                </div>

                <!-- Cloudflare -->
                <div class="border-l-4 border-yellow-500 pl-4">
                    <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">Cloudflare</h3>
                    <ol class="list-decimal list-inside space-y-1 text-gray-600 dark:text-gray-400">
                        <li>Log in to Cloudflare</li>
                        <li>Select your domain → <strong>DNS</strong> tab</li>
                        <li>Click <strong>Add record</strong></li>
                        <li>Fill in details</li>
                        <li><strong>Important:</strong> Set Proxy status to <strong>DNS only</strong> (grey cloud)</li>
                        <li>Click <strong>Save</strong></li>
                    </ol>
                </div>
            </div>
        </div>
    </div>

    <!-- Verification -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-8 overflow-hidden">
        <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Verification & SSL</h2>
        </div>
        <div class="p-6 space-y-4">
            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">DNS Propagation</h3>
                <p class="text-gray-600 dark:text-gray-400 mb-2">
                    DNS changes typically take 15-30 minutes to propagate, but can take up to 48 hours in rare cases.
                </p>
                <p class="text-sm text-gray-500 dark:text-gray-500">
                    Check propagation status: <a href="https://dnschecker.org" target="_blank" class="text-blue-600 hover:underline">dnschecker.org</a>
                </p>
            </div>

            <div>
                <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">SSL Certificate</h3>
                <p class="text-gray-600 dark:text-gray-400">
                    Once your domain is verified, an SSL certificate will be automatically generated within 24 hours. Your site will be accessible via HTTPS after activation.
                </p>
            </div>
        </div>
    </div>

    <!-- Troubleshooting -->
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow-md mb-8 overflow-hidden">
        <div class="bg-gray-50 dark:bg-gray-900 px-6 py-4 border-b border-gray-200 dark:border-gray-700">
            <h2 class="text-xl font-semibold text-gray-900 dark:text-white">Troubleshooting</h2>
        </div>
        <div class="p-6">
            <div class="space-y-6">
                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">Domain Not Verifying</h3>
                    <ul class="list-disc list-inside space-y-1 text-gray-600 dark:text-gray-400">
                        <li>Wait longer - DNS can take time to propagate</li>
                        <li>Check for typos in DNS record values</li>
                        <li>Verify correct record type (CNAME vs A)</li>
                        <li>Remove duplicate DNS records</li>
                        <li>For Cloudflare: Ensure proxy is disabled (grey cloud)</li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">"This site can't be reached" Error</h3>
                    <ul class="list-disc list-inside space-y-1 text-gray-600 dark:text-gray-400">
                        <li>DNS hasn't propagated yet (wait 15-30 minutes)</li>
                        <li>DNS record is incorrect</li>
                        <li>Your registrar hasn't applied changes</li>
                    </ul>
                </div>

                <div>
                    <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">SSL Issues</h3>
                    <ul class="list-disc list-inside space-y-1 text-gray-600 dark:text-gray-400">
                        <li>Wait up to 24 hours after domain verification</li>
                        <li>Ensure domain is fully verified first</li>
                        <li>Contact support if SSL doesn't activate after 24 hours</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <!-- Email Warning -->
    <div class="bg-yellow-50 dark:bg-yellow-900/20 border-l-4 border-yellow-400 p-6 mb-8">
        <div class="flex">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-yellow-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path>
                </svg>
            </div>
            <div class="ml-4">
                <h3 class="text-base font-semibold text-yellow-800 dark:text-yellow-200 mb-2">Email Consideration</h3>
                <p class="text-sm text-yellow-700 dark:text-yellow-300 mb-2">
                    If you use email with your domain (e.g., you@yourbusiness.com), be careful when pointing the root domain to ClientBridge.
                </p>
                <p class="text-sm text-yellow-700 dark:text-yellow-300">
                    <strong>Recommendation:</strong> Point only the <code class="bg-yellow-100 dark:bg-yellow-800 px-1 rounded">www</code> subdomain to ClientBridge, and keep your root domain for email.
                </p>
            </div>
        </div>
    </div>

    <!-- Support -->
    <div class="bg-blue-50 dark:bg-blue-900/20 border border-blue-200 dark:border-blue-800 rounded-lg p-6">
        <h3 class="text-lg font-semibold text-blue-900 dark:text-blue-200 mb-2">Need Help?</h3>
        <p class="text-blue-800 dark:text-blue-300 mb-4">
            If you're having trouble connecting your domain, our support team is here to help.
        </p>
        <div class="flex gap-4">
            <a href="mailto:support@clientbridge.app" class="inline-flex items-center px-4 py-2 text-sm font-medium text-white bg-blue-600 hover:bg-blue-700 rounded-md transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                </svg>
                Contact Support
            </a>
            <a href="{{ route('admin.domains.index') }}" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-700 dark:text-gray-300 bg-white dark:bg-gray-700 border border-gray-300 dark:border-gray-600 hover:bg-gray-50 dark:hover:bg-gray-600 rounded-md transition">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Domains
            </a>
        </div>
    </div>
</div>
@endsection
