@extends('layouts.guest')

@section('title', 'Login')

@section('content')
<div class="bg-white rounded-lg shadow p-8">
    <h1 class="text-2xl font-bold text-center text-indigo-600 mb-1">TOKOPINTAR</h1>
    <p class="text-center text-sm text-gray-500 mb-6">Sistem Manajemen Toko UMKM</p>

    <x-flash />

    @if ($errors->any())
        <div class="mb-4 px-4 py-3 rounded bg-red-100 border border-red-300 text-red-800">
            @foreach ($errors->all() as $err)
                <div>{{ $err }}</div>
            @endforeach
        </div>
    @endif

    <form method="POST" action="{{ route('login.attempt') }}" class="space-y-4">
        @csrf
        <div>
            <label for="login" class="block text-sm font-medium text-gray-700 mb-1">Username atau Email</label>
            <input id="login" name="login" type="text" value="{{ old('login') }}" autofocus required
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div>
            <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Password</label>
            <input id="password" name="password" type="password" required
                class="w-full border rounded px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-500">
        </div>
        <div class="flex items-center">
            <input id="remember" name="remember" type="checkbox" value="1" class="mr-2">
            <label for="remember" class="text-sm text-gray-700">Ingat saya</label>
        </div>
        <button type="submit"
            class="w-full bg-indigo-600 text-white py-2 rounded hover:bg-indigo-700 font-medium">
            Login
        </button>
    </form>
</div>
@endsection
