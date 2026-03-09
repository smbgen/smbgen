@extends('layouts.public')

@section('content')
<div class="min-h-screen py-12 px-4 sm:px-6 lg:px-8">
    <div class="max-w-2xl mx-auto">
        <!-- Header -->
        <div class="text-center mb-8">
            <div class="inline-flex items-center justify-center w-16 h-16 rounded-full mb-4" style="background-color: {{ \App\Models\CmsCompanyColors::getSettings()->primary_color }};">
                <i class="fas fa-dollar-sign text-white text-2xl"></i>
            </div>
            <h2 class="text-4xl font-bold mb-3" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }};">Process Credit Card Payment</h2>
            <p class="text-lg" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }}99;">Simple and secure payment collection powered by Stripe</p>
        </div>

        <!-- Payment Form -->
        <div class="rounded-2xl shadow-2xl p-8 lg:p-10" style="background-color: {{ \App\Models\CmsCompanyColors::getSettings()->body_background_color }}; border: 1px solid {{ \App\Models\CmsCompanyColors::getSettings()->text_color }}20;">
            <form id="payment-form" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                    <!-- Amount -->
                    <div class="lg:col-span-2">
                        <label for="amount" class="block text-sm font-medium mb-2" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }};">
                            <i class="fas fa-dollar-sign mr-2" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->accent_color }};"></i>Amount
                        </label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 transform -translate-y-1/2 text-xl font-bold" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }}99;">$</span>
                            <input 
                                type="number" 
                                id="amount" 
                                name="amount" 
                                step="0.01" 
                                min="0.50" 
                                required
                                class="w-full pr-4 py-4 rounded-xl text-xl font-semibold focus:outline-none focus:ring-2 transition-all"
                                style="padding-left: 3rem; background-color: #ffffff; border: 2px solid {{ \App\Models\CmsCompanyColors::getSettings()->text_color }}20; color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }}; focus:ring-color: {{ \App\Models\CmsCompanyColors::getSettings()->primary_color }};"
                                placeholder="0.00"
                            >
                        </div>
                    </div>

                    <!-- Customer Name -->
                    <div>
                        <label for="name" class="block text-sm font-medium mb-2" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }};">
                            <i class="fas fa-user mr-2" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->primary_color }};"></i>Customer Name
                        </label>
                        <input 
                            type="text" 
                            id="name" 
                            name="name" 
                            required
                            class="w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-2 transition-all"
                            style="background-color: #ffffff; border: 2px solid {{ \App\Models\CmsCompanyColors::getSettings()->text_color }}20; color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }};"
                            placeholder="John Doe"
                        >
                    </div>

                    <!-- Customer Email -->
                    <div>
                        <label for="email" class="block text-sm font-medium mb-2" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }};">
                            <i class="fas fa-envelope mr-2" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->secondary_color }};"></i>Email Address
                        </label>
                        <input 
                            type="email" 
                            id="email" 
                            name="email" 
                            required
                            class="w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-2 transition-all"
                            style="background-color: #ffffff; border: 2px solid {{ \App\Models\CmsCompanyColors::getSettings()->text_color }}20; color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }};"
                            placeholder="john@example.com"
                        >
                    </div>

                    <!-- Description -->
                    <div class="lg:col-span-2">
                        <label for="description" class="block text-sm font-medium mb-2" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }};">
                            <i class="fas fa-comment-alt mr-2" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->accent_color }};"></i>Description (Optional)
                        </label>
                        <textarea 
                            id="description" 
                            name="description" 
                            rows="3"
                            class="w-full px-4 py-3 rounded-xl focus:outline-none focus:ring-2 transition-all resize-none"
                            style="background-color: #ffffff; border: 2px solid {{ \App\Models\CmsCompanyColors::getSettings()->text_color }}20; color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }};"
                            placeholder="What is this payment for?"
                        ></textarea>
                    </div>
                </div>

                <!-- Card Element -->
                <div class="mt-8">
                    <div class="flex items-center justify-between mb-3">
                        <label class="block text-sm font-medium" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }};">
                            <i class="fas fa-credit-card mr-2" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->primary_color }};"></i>Card Details
                        </label>
                        <span class="text-xs flex items-center gap-1" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }}80;">
                            <svg class="w-3.5 h-3.5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            Entered directly into Stripe — never touches our servers
                        </span>
                    </div>
                    <div id="card-element" class="p-5 rounded-xl" style="background-color: #ffffff; border: 2px solid {{ \App\Models\CmsCompanyColors::getSettings()->text_color }}20;"></div>
                    <div id="card-errors" class="mt-3 text-sm text-red-600 font-medium"></div>
                </div>

                <!-- What happens next -->
                <div class="rounded-xl p-4 text-sm" style="background-color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }}08; border: 1px solid {{ \App\Models\CmsCompanyColors::getSettings()->text_color }}15;">
                    <p class="font-medium mb-1" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }};">What happens when you click Pay Now:</p>
                    <ol class="space-y-1 list-decimal list-inside" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }}80;">
                        <li>Your card is charged the amount shown above</li>
                        <li>A payment receipt is sent by Stripe to your email</li>
                    </ol>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    id="submit-button"
                    class="w-full font-bold py-4 px-6 rounded-xl transition-all duration-200 flex items-center justify-center shadow-lg hover:shadow-xl transform hover:-translate-y-0.5 hover:opacity-90"
                    style="background-color: {{ \App\Models\CmsCompanyColors::getSettings()->primary_color }}; color: #ffffff;"
                >
                    <svg class="animate-spin -ml-1 mr-3 h-6 w-6 text-white hidden" id="loading-spinner" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <i class="fas fa-lock mr-2" id="lock-icon"></i>
                    <span id="button-text" class="text-lg">Pay Now</span>
                </button>
            </form>
            <!-- Success Message -->
            <div id="payment-success" class="hidden mt-6 p-6 rounded-xl shadow-lg" style="background-color: #10B98120; border: 2px solid #10B981;">
                <div class="flex items-start">
                    <div class="flex-shrink-0">
                        <div class="flex items-center justify-center w-12 h-12 rounded-full" style="background-color: #10B981;">
                            <svg class="w-7 h-7 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path>
                            </svg>
                        </div>
                    </div>
                    <div class="ml-4">
                        <p class="text-xl font-bold" style="color: #059669;">Payment Successful!</p>
                        <p class="text-sm mt-1" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }};">Thank you for your payment. An invoice has been generated and emailed to <span id="customer-email" class="font-semibold"></span>.</p>
                        <p class="text-xs mt-2" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }}99;" id="invoice-reference"></p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Security & Info Section -->
        <div class="mt-8 space-y-4">
            <div class="flex items-center justify-center gap-6 text-sm" style="color: {{ \App\Models\CmsCompanyColors::getSettings()->text_color }}99;">
                <div class="flex items-center">
                    <svg class="w-5 h-5 mr-2 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path>
                    </svg>
                    <span>SSL Encrypted</span>
                </div>
                <div class="flex items-center">
                    <svg class="w-8 h-8 mr-2" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M13.976 9.15c-2.172-.806-3.356-1.426-3.356-2.409 0-.831.683-1.305 1.901-1.305 2.227 0 4.515.858 6.09 1.631l.89-5.494C18.252.975 15.697 0 12.165 0 9.667 0 7.589.654 6.104 1.872 4.56 3.147 3.757 4.992 3.757 7.218c0 4.039 2.467 5.76 6.476 7.219 2.585.92 3.445 1.574 3.445 2.583 0 .98-.84 1.545-2.354 1.545-2.618 0-5.357-1.159-7.41-2.273l-.928 5.555C4.864 22.73 7.545 24 10.717 24c2.554 0 4.664-.705 6.104-2.029 1.516-1.391 2.287-3.325 2.287-5.754 0-3.944-2.577-5.732-5.132-7.062z" fill="#635BFF"/>
                    </svg>
                    <span>Powered by Stripe</span>
                </div>
            </div>
            <div class="text-center">
                <p class="text-xs text-gray-500">An invoice will be automatically generated and emailed upon successful payment</p>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
    const stripe = Stripe('{{ config("services.stripe.key") }}');
    const elements = stripe.elements();
    const cardElement = elements.create('card', {
        hidePostalCode: true,
        style: {
            base: {
                color: '{{ \App\Models\CmsCompanyColors::getSettings()->text_color }}',
                fontFamily: '"Helvetica Neue", Helvetica, sans-serif',
                fontSmoothing: 'antialiased',
                fontSize: '16px',
                lineHeight: '24px',
                '::placeholder': {
                    color: '{{ \App\Models\CmsCompanyColors::getSettings()->text_color }}99'
                }
            },
            invalid: {
                color: '#EF4444',
                iconColor: '#EF4444'
            }
        }
    });
    cardElement.mount('#card-element');

    cardElement.on('change', function(event) {
        const displayError = document.getElementById('card-errors');
        if (event.error) {
            displayError.textContent = event.error.message;
        } else {
            displayError.textContent = '';
        }
    });

    const form = document.getElementById('payment-form');
    const submitButton = document.getElementById('submit-button');
    const buttonText = document.getElementById('button-text');
    const loadingSpinner = document.getElementById('loading-spinner');

    form.addEventListener('submit', async function(event) {
        event.preventDefault();

        // Disable submit button
        submitButton.disabled = true;
        buttonText.textContent = 'Processing...';
        loadingSpinner.classList.remove('hidden');

        const formData = new FormData(form);
        const data = Object.fromEntries(formData);

        try {
            // Create payment intent
            const response = await fetch('{{ route("payment.process") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(data)
            });

            const result = await response.json();

            if (result.error) {
                throw new Error(result.error);
            }

            // Confirm payment
            const {error, paymentIntent} = await stripe.confirmCardPayment(result.clientSecret, {
                payment_method: {
                    card: cardElement,
                    billing_details: {
                        name: data.name,
                        email: data.email
                    }
                }
            });

            if (error) {
                // Show error
                document.getElementById('card-errors').textContent = error.message;
                submitButton.disabled = false;
                buttonText.textContent = 'Pay Now';
                loadingSpinner.classList.add('hidden');
            } else if (paymentIntent.status === 'succeeded') {
                // Notify backend of successful payment to send email
                try {
                    await fetch('{{ route("payment.confirm") }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({ invoiceId: result.invoiceId })
                    });
                } catch (e) {
                    console.error('Failed to notify backend:', e);
                }

                // Show success
                form.classList.add('hidden');
                const successDiv = document.getElementById('payment-success');
                successDiv.classList.remove('hidden');
                
                // Show email and invoice reference
                if (result.email) {
                    document.getElementById('customer-email').textContent = result.email;
                }
                if (result.invoiceId) {
                    document.getElementById('invoice-reference').textContent = `Invoice #${result.invoiceId} created`;
                }
            }
        } catch (error) {
            document.getElementById('card-errors').textContent = error.message;
            submitButton.disabled = false;
            buttonText.textContent = 'Pay Now';
            loadingSpinner.classList.add('hidden');
        }
    });
</script>
@endpush
@endsection
