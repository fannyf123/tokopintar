@extends('layouts.app')
@section('title', $supplier->exists ? 'Edit Supplier - TOKOPINTAR' : 'Supplier Baru - TOKOPINTAR')
@section('page_title', $supplier->exists ? 'Edit Supplier' : 'Supplier Baru')
@section('content')
<div class="card">
    <div class="card-body">
        @if ($errors->any())
            <div class="alert alert-danger small">@foreach ($errors->all() as $e)<div>{{ $e }}</div>@endforeach</div>
        @endif
        <form method="POST" action="{{ $supplier->exists ? route('supplier.update', $supplier) : route('supplier.store') }}" class="row g-3">
            @csrf
            @if ($supplier->exists) @method('PUT') @endif
            @foreach (['nama' => ['Nama', true, 6], 'kontak' => ['Nama Kontak', false, 6], 'no_hp' => ['No HP', false, 6], 'email' => ['Email', false, 6], 'alamat' => ['Alamat', false, 12]] as $field => $cfg)
                <div class="col-12 col-md-{{ $cfg[2] }}">
                    <label class="form-label fw-semibold">{{ $cfg[0] }}</label>
                    <input name="{{ $field }}" value="{{ old($field, $supplier->{$field}) }}" class="form-control" {{ $cfg[1] ? 'required' : '' }}>
                </div>
            @endforeach
            <div class="col-12 d-flex gap-2">
                <button class="btn btn-primary"><i class="fas fa-save me-1"></i> Simpan</button>
                <a href="{{ route('supplier.index') }}" class="btn btn-light">Batal</a>
            </div>
        </form>
    </div>
</div>
@endsection
