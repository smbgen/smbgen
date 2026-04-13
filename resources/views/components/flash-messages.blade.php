@if (session('success'))
    <div class="alert alert-success">
        <i class="fas fa-check-circle"></i>
        <span>{{ session('success') }}</span>
    </div>
@endif

@if (session('error'))
    <div class="alert alert-error">
        <i class="fas fa-triangle-exclamation"></i>
        <span>{{ session('error') }}</span>
    </div>
@endif

@if (session('warning'))
    <div class="alert alert-warning">
        <i class="fas fa-circle-exclamation"></i>
        <span>{{ session('warning') }}</span>
    </div>
@endif

@if (session('info'))
    <div class="alert alert-info">
        <i class="fas fa-circle-info"></i>
        <span>{{ session('info') }}</span>
    </div>
@endif
