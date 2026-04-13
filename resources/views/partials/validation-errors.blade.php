@if ($errors->any())
    <div class="alert alert-danger">
        <strong class="d-block mb-1">There were some problems with your input:</strong>
        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif