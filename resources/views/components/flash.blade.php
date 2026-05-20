@if (session('success'))
    <div class="mb-4 px-4 py-3 rounded bg-green-100 border border-green-300 text-green-800" role="alert">
        {{ session('success') }}
    </div>
@endif
@if (session('error'))
    <div class="mb-4 px-4 py-3 rounded bg-red-100 border border-red-300 text-red-800" role="alert">
        {{ session('error') }}
    </div>
@endif
@if (session('info'))
    <div class="mb-4 px-4 py-3 rounded bg-blue-100 border border-blue-300 text-blue-800" role="alert">
        {{ session('info') }}
    </div>
@endif
