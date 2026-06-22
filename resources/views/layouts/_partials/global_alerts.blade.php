@if (session('message'))
    <div class="row mb-2">
        <div class="col-lg-12">
            <div class="alert alert-success" role="alert">{{ session('message') }}</div>
        </div>
    </div>
@endif
@if ($errors->count() > 0)
    <div class="alert alert-danger">
        <ul class="list-unstyled">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

@if (session('error'))
    <div class="row mb-2">
        <div class="col-lg-12">
            <div class="alert alert-danger" role="alert">{{ session('error') }}</div>
        </div>
    </div>
@endif
@if (session('success'))
    <div class="row mb-2">
        <div class="col-lg-12">
            <div class="alert alert-success" role="alert">{{ session('success') }}</div>
        </div>
    </div>
@endif
@if (session('warning'))
    <div class="row mb-2">
        <div class="col-lg-12">
            <div class="alert alert-warning" role="alert">{{ session('warning') }}</div>
        </div>
    </div>
@endif

@if (session('info'))
    <div class="row mb-2">
        <div class="col-lg-12">
            <div class="alert alert-info" role="alert">{{ session('info') }}</div>
        </div>
    </div>
@endif
