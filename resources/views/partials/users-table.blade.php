@if($users->count())
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
                @foreach ($users as $user)
                    <tr>
                        <td>{{ $user->name ?? 'Unknown' }}</td>
                        <td>{{ $user->email ?? 'N/A' }}</td>
                        <td>{{ $user->created_at ? $user->created_at->format('M d, Y') : 'N/A' }}</td>
                        <td><a href="{{ route('users.edit', $user) }}" class="btn btn-sm btn-outline-light">Edit</a></td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
@else
    <p class="text-muted">No users found.</p>
@endif
