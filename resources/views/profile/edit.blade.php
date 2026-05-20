@extends('layouts.app')

@section('title', 'Profil')

@section('content')
<h1 class="text-2xl font-bold text-gray-800 mb-4">Profil Saya</h1>

<div class="grid md:grid-cols-2 gap-6">
    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Informasi Akun</h2>
        @if ($errors->updateProfile->any())
            <div class="mb-4 px-4 py-3 rounded bg-red-100 border border-red-300 text-red-800">
                @foreach ($errors->updateProfile->all() as $err)
                    <div>{{ $err }}</div>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('profile.update') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="name" class="block text-sm font-medium text-gray-700 mb-1">Nama</label>
                <input id="name" name="name" type="text" required
                    value="{{ old('name', auth()->user()->name) }}"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label for="username" class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input id="username" name="username" type="text" required
                    value="{{ old('username', auth()->user()->username) }}"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label for="email" class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input id="email" name="email" type="email" required
                    value="{{ old('email', auth()->user()->email) }}"
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit"
                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 font-medium">
                Simpan Perubahan
            </button>
        </form>
    </div>

    <div class="bg-white rounded-lg shadow p-6">
        <h2 class="text-lg font-semibold text-gray-800 mb-4">Ganti Password</h2>
        @if ($errors->changePassword->any())
            <div class="mb-4 px-4 py-3 rounded bg-red-100 border border-red-300 text-red-800">
                @foreach ($errors->changePassword->all() as $err)
                    <div>{{ $err }}</div>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('profile.password') }}" class="space-y-4">
            @csrf
            @method('PUT')
            <div>
                <label for="current_password" class="block text-sm font-medium text-gray-700 mb-1">Password Lama</label>
                <input id="current_password" name="current_password" type="password" required
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label for="new_password" class="block text-sm font-medium text-gray-700 mb-1">Password Baru</label>
                <input id="new_password" name="password" type="password" required
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input id="password_confirmation" name="password_confirmation" type="password" required
                    class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
            </div>
            <button type="submit"
                class="bg-indigo-600 text-white px-4 py-2 rounded hover:bg-indigo-700 font-medium">
                Ganti Password
            </button>
        </form>
    </div>
</div>
@endsection
