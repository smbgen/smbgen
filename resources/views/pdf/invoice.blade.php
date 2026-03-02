<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #111; }
        .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; }
        .title { font-size: 20px; font-weight: bold; }
        .muted { color: #555; }
        table { width: 100%; border-collapse: collapse; margin-top: 12px; }
        th, td { padding: 8px; border-bottom: 1px solid #ddd; text-align: left; }
        th { background: #f5f5f5; }
        .right { text-align: right; }
        .total { font-weight: bold; }
    </style>
    <title>Invoice #{{ $invoice->id }}</title>
</head>
<body>
    <div class="header">
        <div>
            <div class="title">Invoice #{{ $invoice->id }}</div>
            <div class="muted">Date: {{ $invoice->created_at ? $invoice->created_at->format('Y-m-d') : 'N/A' }}</div>
            @if($invoice->due_date)
                <div class="muted">Due: {{ $invoice->due_date ? $invoice->due_date->format('Y-m-d') : 'N/A' }}</div>
            @endif
        </div>
        <div class="right">
            <div><strong>{{ config('business.company_name') ?? 'Company' }}</strong></div>
            <div class="muted">{{ config('business.contact.email') ?? 'N/A' }}</div>
            <div class="muted">{{ config('business.contact.website') ?? 'N/A' }}</div>
        </div>
    </div>

    <div>
        <strong>Billed To:</strong><br>
        {{ optional($invoice->user)->name ?? 'Unknown' }}<br>
        {{ optional($invoice->user)->email ?? 'N/A' }}
    </div>

    @if($invoice->memo)
        <p style="margin-top: 12px;"><strong>Memo:</strong> {{ $invoice->memo }}</p>
    @endif

    <table>
        <thead>
            <tr>
                <th>Description</th>
                <th class="right">Qty</th>
                <th class="right">Unit</th>
                <th class="right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach($invoice->items as $item)
                <tr>
                    <td>{{ $item->description }}</td>
                    <td class="right">{{ $item->quantity }}</td>
                    <td class="right">${{ number_format($item->unit_amount / 100, 2) }}</td>
                    <td class="right">${{ number_format($item->total_amount / 100, 2) }}</td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="3" class="right total">Total</td>
                <td class="right total">${{ number_format($invoice->total_amount / 100, 2) }}</td>
            </tr>
        </tfoot>
    </table>

    <p class="muted" style="margin-top: 16px;">Thank you for your business.</p>
</body>
</html>


