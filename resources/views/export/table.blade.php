<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>{{ $title }}</title>
<style>
    body { font-family: 'DejaVu Sans', Arial, sans-serif; font-size: 11px; color:#000; }
    h2 { font-size: 16px; margin: 0 0 4px; }
    .meta { font-size: 10px; color:#555; margin-bottom: 10px; }
    table { width: 100%; border-collapse: collapse; }
    th, td { border: 1px solid #999; padding: 5px 7px; text-align: left; }
    th { background: #eef2ff; font-weight: bold; }
    tr:nth-child(even) td { background: #f8fafc; }
</style>
</head>
<body>
    <h2>{{ config('app.name') }} — {{ $title }}</h2>
    <div class="meta">Diekspor: {{ now()->format('d/m/Y H:i') }} · Total {{ count($rows) }} baris</div>
    <table>
        <thead>
            <tr>
                @foreach ($header as $h)<th>{{ $h }}</th>@endforeach
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $row)
                <tr>
                    @foreach ($row as $cell)<td>{{ $cell }}</td>@endforeach
                </tr>
            @empty
                <tr><td colspan="{{ max(count($header), 1) }}">Tidak ada data.</td></tr>
            @endforelse
        </tbody>
    </table>
</body>
</html>
