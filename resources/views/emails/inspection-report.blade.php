<div style="font-family: Arial, Helvetica, sans-serif;">
    <h2 style="color: #1e40af; margin-top: 0;">Inspection Report: {{ $report->summary_title }}</h2>

    <p><strong>Client:</strong> {{ $report->client_name }}</p>
    <p><strong>Phone:</strong> {{ $report->client_phone ?? 'N/A' }}</p>
    <p><strong>Email:</strong> {{ $report->client_email ?? 'N/A' }}</p>
    <p><strong>Address:</strong> {{ $report->client_address ?? 'N/A' }}</p>

    <p><strong>Date of Consult:</strong> {{ optional($report->consult_date)->toDayDateTimeString() ?? 'N/A' }}</p>

    <h3 style="color: #1e40af; margin-top: 20px;">Summary</h3>
    <p>{{ $report->summary_title }}</p>

    <h3 style="color: #1e40af; margin-top: 20px;">Explanation</h3>
    <p>{{ $report->body_explanation ?? 'No explanation provided.' }}</p>

    <h3 style="color: #1e40af; margin-top: 20px;">Suggested Actions</h3>
    <p>{{ $report->body_suggested_actions ?? 'No suggested actions.' }}</p>

    <p>View this report in your client portal or reply to this email if you have questions.</p>
</div>