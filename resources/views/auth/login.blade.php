@extends('layouts.guest')
@section('title', 'Login')
@section('content')
<div class="card login-card mt-5">
    <div class="card-body p-4 p-md-5">
        <div class="text-center mb-4">
            <i class="fas fa-store" style="font-size:2.5rem;color:#4361ee;"></i>
            <h3 class="fw-bold mt-3 mb-1">TOKOPINTAR</h3>
            <p class="text-muted small mb-0">Sistem Manajemen Toko UMKM</p>
        </div>
        @if ($errors->any())
            <div class="alert alert-danger small">{{ $errors->first() }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger small">{{ session('error') }}</div>
        @endif
        <form method="POST" action="{{ route('login.attempt') }}">
            @csrf
            <div class="mb-3">
                <label for="login" class="form-label small fw-semibold">Username atau Email</label>
                <input id="login" name="login" type="text" value="{{ old('login') }}" required autofocus
                    class="form-control @error('login') is-invalid @enderror">
            </div>
            <div class="mb-3">
                <label for="password" class="form-label small fw-semibold">Password</label>
                <div class="input-group">
                    <input id="password" name="password" type="password" required
                        class="form-control @error('password') is-invalid @enderror">
                    <button type="button" class="btn btn-outline-secondary" id="togglePass" tabindex="-1" aria-label="Tampilkan password">
                        <i class="fas fa-eye" id="togglePassIcon"></i>
                    </button>
                </div>
            </div>
            <div class="form-check mb-3">
                <input id="remember" name="remember" type="checkbox" value="1" class="form-check-input">
                <label for="remember" class="form-check-label small">Ingat saya</label>
            </div>
            <button type="submit" class="btn btn-primary w-100 py-2 fw-semibold">
                <i class="fas fa-sign-in-alt me-1"></i> Login
            </button>
        </form>
    </div>
</div>
<script>
document.getElementById('togglePass')?.addEventListener('click', function() {
    const inp = document.getElementById('password');
    const ico = document.getElementById('togglePassIcon');
    if (inp.type === 'password') { inp.type = 'text'; ico.className = 'fas fa-eye-slash'; }
    else { inp.type = 'password'; ico.className = 'fas fa-eye'; }
});
</script>
@endsection
