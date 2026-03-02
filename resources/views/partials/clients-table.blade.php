@if($clients->count())
    <div class="table-responsive">
        <table class="table table-sm table-hover text-white">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Created</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                @foreach ($clients as $client)
                    <tr>
                        <td data-label="Name">{{ $client->name ?? 'Unknown' }}</td>
                        <td data-label="Email">{{ $client->email ?? 'N/A' }}</td>
                        <td data-label="Created">{{ $client->created_at ? $client->created_at->format('M d, Y') : 'N/A' }}</td>
                        <td data-label="Edit">
                            <a href="{{ route('clients.edit', $client) }}" class="btn btn-sm btn-outline-light">Edit</a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="text-muted">No clients found.</p>
@endif
