@foreach ($leads as $lead)
<tr>
    <td data-label="Name">
        <span title="{{ $lead->name ?? 'N/A' }}">
            {{ Str::limit($lead->name ?? 'N/A', 15) }}
        </span>
    </td>
    <td data-label="Email">
        <span title="{{ $lead->email ?? 'N/A' }}">
            {{ Str::limit($lead->email ?? 'N/A', 15) }}
        </span>
    </td>
    <td data-label="Message">
        <span title="{{ $lead->message ?? 'N/A' }}">
            {{ Str::limit($lead->message ?? 'N/A', 15) }}
        </span>
    </td>
    <td data-label="Submitted">
        <span title="{{ $lead->created_at ? $lead->created_at->diffForHumans() : 'N/A' }}">
            {{ Str::limit($lead->created_at ? $lead->created_at->diffForHumans() : 'N/A', 15) }}
        </span>
    </td>
    <td data-label="Created At">
        <span title="{{ $lead->created_at ?? 'N/A' }}">
            {{ Str::limit($lead->created_at ?? 'N/A', 15) }}
        </span>
    </td>
    <td data-label="IP">
        <span title="{{ $lead->ip_address ?? 'N/A' }}">
            {{ Str::limit($lead->ip_address ?? 'N/A', 15) }}
        </span>
    </td>
    <td data-label="Browser">
        <span title="{{ $lead->user_agent ?? 'N/A' }}">
            {{ Str::limit($lead->user_agent ?? 'N/A', 10) }}
        </span>
    </td>
    <td data-label="Referrer">
        <span title="{{ $lead->referer ?? 'N/A' }}">
            {{ Str::limit($lead->referer ?? 'N/A', 10) }}
        </span>
    </td>
    <td data-label="Convert">
        <form action="{{ route('leads.convert', $lead) }}" method="POST" class="d-inline">
            @csrf
            <button type="submit" class="btn btn-sm btn-success" title="Convert to Client">
                <i class="bi bi-check-circle-fill"></i>
            </button>
        </form>
    </td>
    <td data-label="Delete">
        <form action="{{ route('leads.destroy', $lead) }}" method="POST" class="d-inline">
            @csrf
            @method('DELETE')
            <button type="submit" class="btn btn-sm btn-outline-danger" title="Delete Lead">
                <i class="bi bi-trash3"></i>
            </button>
        </form>
    </td>
</tr>
@endforeach
