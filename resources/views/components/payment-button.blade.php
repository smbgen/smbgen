@props([
    'amount' => 49900, 
    'description' => 'Cybersecurity Audit',
    'payment_type' => 'product',
    'label' => 'Purchase Now',
    'class' => 'btn btn-primary'
])

<div class="payment-button-container">
    <button 
        id="payment-button-{{ uniqid() }}" 
        class="{{ $class }} payment-btn"
        data-amount="{{ $amount }}"
        data-description="{{ $description }}"
        data-payment-type="{{ $payment_type }}"
    >
        {{ $label }} - ${{ number_format($amount / 100, 2) }}
    </button>
</div>

@push('scripts')
<script src="https://js.stripe.com/v3/"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Add click handlers to all payment buttons
    document.querySelectorAll('.payment-btn').forEach(button => {
        button.addEventListener('click', async function() {
            button.disabled = true;
            const originalText = button.textContent;
            button.textContent = 'Processing...';
            
            try {
                const response = await fetch('/payment/checkout', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify({
                        amount: parseInt(button.dataset.amount),
                        description: button.dataset.description,
                        payment_type: button.dataset.paymentType || 'product'
                    })
                });
                
                const data = await response.json();
                
                if (data.success) {
                    if (data.checkout_url) {
                        // Redirect to Stripe checkout
                        window.location.href = data.checkout_url;
                    } else {
                        // Show temporary message
                        alert(data.message || 'Payment system is being set up. Thank you for your interest!');
                        button.disabled = false;
                        button.textContent = originalText;
                    }
                } else {
                    throw new Error(data.error || 'Payment setup failed');
                }
            } catch (error) {
                console.error('Payment error:', error);
                button.disabled = false;
                button.textContent = originalText;
                alert('Payment setup failed. Please try again later.');
            }
        });
    });
});
</script>
@endpush
