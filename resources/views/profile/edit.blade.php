@extends('layouts.app')
@section('title', 'Profil - TOKOPINTAR')
@section('page_title', 'Profil Saya')
@section('content')
<div class="row g-3">
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-user-circle me-2"></i>Informasi Akun</h6>
                @if ($errors->updateProfile->any())
                    <div class="alert alert-danger small">@foreach ($errors->updateProfile->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                @endif
                <form method="POST" action="{{ route('profile.update') }}" class="row g-3">
                    @csrf @method('PUT')
                    <div class="col-12">
                        <label class="form-label fw-semibold">Nama</label>
                        <input name="name" type="text" required value="{{ old('name', auth()->user()->name) }}" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Username</label>
                        <input name="username" type="text" required value="{{ old('username', auth()->user()->username) }}" class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Email</label>
                        <input name="email" type="email" required value="{{ old('email', auth()->user()->email) }}" class="form-control">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h6 class="fw-bold mb-3"><i class="fas fa-key me-2"></i>Ganti Password</h6>
                @if ($errors->changePassword->any())
                    <div class="alert alert-danger small">@foreach ($errors->changePassword->all() as $e)<div>{{ $e }}</div>@endforeach</div>
                @endif
                <form method="POST" action="{{ route('profile.password') }}" class="row g-3">
                    @csrf @method('PUT')
                    <div class="col-12">
                        <label class="form-label fw-semibold">Password Lama</label>
                        <input name="current_password" type="password" required class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Password Baru</label>
                        <input name="password" type="password" required class="form-control">
                    </div>
                    <div class="col-12">
                        <label class="form-label fw-semibold">Konfirmasi Password</label>
                        <input name="password_confirmation" type="password" required class="form-control">
                    </div>
                    <div class="col-12">
                        <button type="submit" class="btn btn-primary"><i class="fas fa-save me-1"></i> Ganti Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
