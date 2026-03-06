@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="bg-gray-800/60 border border-gray-700 rounded-xl shadow-xl overflow-hidden">
        <div class="px-6 py-4 bg-gray-800/80 border-b border-gray-700 text-white font-semibold">Payment Integration Test</div>
        <div class="p-6">
            <div class="mb-6 rounded-md border border-sky-500/30 bg-sky-500/10 px-4 py-3 text-sky-100">
                <h5 class="font-semibold mb-1">🧪 Testing Stripe Payment Integration</h5>
                <p class="text-sky-200/90">This page helps verify your Stripe setup. Ensure:</p>
                <ul class="list-disc list-inside text-sky-100/90 text-sm mt-1">
                    <li>Stripe test keys are set in the environment</li>
                    <li>A webhook endpoint is created in your Stripe dashboard</li>
                </ul>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                <div class="rounded-lg border border-gray-700 bg-gray-900/40 p-5 text-center hover:shadow-lg hover:shadow-black/30 transition">
                    <h5 class="text-white font-semibold">🔒 Cyber Audit</h5>
                    <p class="text-gray-300 text-sm mt-1">Complete cybersecurity assessment</p>
                    <div class="mt-4">
                        <x-payment-button 
                            :amount="49900" 
                            description="Cybersecurity Audit"
                            payment_type="product"
                            label="Purchase Audit"
                            class="inline-flex items-center justify-center rounded-md bg-indigo-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-400/70"
                        />
                    </div>
                </div>

                <div class="rounded-lg border border-gray-700 bg-gray-900/40 p-5 text-center hover:shadow-lg hover:shadow-black/30 transition">
                    <h5 class="text-white font-semibold">🔍 SEO Analysis</h5>
                    <p class="text-gray-300 text-sm mt-1">AI-powered SEO optimization</p>
                    <div class="mt-4">
                        <x-payment-button 
                            :amount="29900" 
                            description="SEO Analysis Package"
                            payment_type="product"
                            label="Purchase SEO"
                            class="inline-flex items-center justify-center rounded-md bg-emerald-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-emerald-500 focus:outline-none focus:ring-2 focus:ring-emerald-400/70"
                        />
                    </div>
                </div>

                <div class="rounded-lg border border-gray-700 bg-gray-900/40 p-5 text-center hover:shadow-lg hover:shadow-black/30 transition">
                    <h5 class="text-white font-semibold">💬 Consultation</h5>
                    <p class="text-gray-300 text-sm mt-1">1-hour expert consultation</p>
                    <div class="mt-4">
                        <x-payment-button 
                            :amount="19900" 
                            description="Expert Consultation"
                            payment_type="product"
                            label="Book Consultation"
                            class="inline-flex items-center justify-center rounded-md bg-sky-600 px-4 py-2 text-sm font-medium text-white shadow hover:bg-sky-500 focus:outline-none focus:ring-2 focus:ring-sky-400/70"
                        />
                    </div>
                </div>
            </div>

            <div class="mt-6">
                <h5 class="text-white font-semibold mb-2">🔧 Environment Check</h5>
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div class="rounded-md border border-gray-700 bg-gray-900/40 p-4">
                        <p class="text-gray-200"><strong>Stripe Public Key:</strong>
                            @php $pub = config('business.integrations.stripe.public_key'); @endphp
                            <span class="ml-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $pub ? 'bg-emerald-500/20 text-emerald-300' : 'bg-red-500/20 text-red-300' }}">
                                {{ $pub ? 'Configured' : 'Missing' }}
                            </span>
                        </p>
                    </div>
                    <div class="rounded-md border border-gray-700 bg-gray-900/40 p-4">
                        <p class="text-gray-200"><strong>Stripe Secret Key:</strong>
                            @php $sec = config('business.integrations.stripe.secret_key'); @endphp
                            <span class="ml-2 inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {{ $sec ? 'bg-emerald-500/20 text-emerald-300' : 'bg-red-500/20 text-red-300' }}">
                                {{ $sec ? 'Configured' : 'Missing' }}
                            </span>
                        </p>
                    </div>
                </div>

                @if(!config('business.integrations.stripe.public_key') || !config('business.integrations.stripe.secret_key'))
                    <div class="mt-4 rounded-md border border-amber-500/30 bg-amber-500/10 px-4 py-3 text-amber-100">
                        <strong>⚠️ Stripe Configuration Missing</strong><br>
                        Add your Stripe keys to your .env file:<br>
                        <code class="text-amber-200">STRIPE_PUBLIC_KEY=pk_test_...</code><br>
                        <code class="text-amber-200">STRIPE_SECRET_KEY=sk_test_...</code>
                    </div>
                @endif
            </div>

            <div class="mt-6">
                <h5 class="text-white font-semibold mb-2">🃏 Test Card Numbers</h5>
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 text-gray-200">
                    <div class="rounded-md border border-gray-700 bg-gray-900/40 p-4">
                        <strong>Success:</strong><br>
                        <code>4242 4242 4242 4242</code>
                    </div>
                    <div class="rounded-md border border-gray-700 bg-gray-900/40 p-4">
                        <strong>Decline:</strong><br>
                        <code>4000 0000 0000 0002</code>
                    </div>
                    <div class="rounded-md border border-gray-700 bg-gray-900/40 p-4">
                        <strong>Auth Required:</strong><br>
                        <code>4000 0025 0000 3155</code>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

