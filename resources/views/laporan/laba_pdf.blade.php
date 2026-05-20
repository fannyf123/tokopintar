<!DOCTYPE html>
<html><head><meta charset="UTF-8">
<title>Laporan Laba</title>
<style>
body { font-family: sans-serif; font-size: 11px; }
h1 { font-size: 16px; margin: 0; }
.muted { color: #666; }
table { width: 100%; border-collapse: collapse; margin-top: 12px; }
th, td { border: 1px solid #ccc; padding: 4px 6px; }
th { background: #f3f4f6; text-align: left; }
.right { text-align: right; }
.totals td { font-weight: bold; background: #fafafa; }
</style></head>
<body>
<h1>{{ config('app.name') }} — Laporan Laba</h1>
<div class="muted">Periode: {{ $start->format('Y-m-d') }} s/d {{ $end->format('Y-m-d') }} · {{ $g }}</div>
<table>
    <thead>
        <tr>
            <th>Periode</th>
            <th class="right">Omzet</th>
            <th class="right">HPP</th>
            <th class="right">Laba Kotor</th>
            <th class="right">Biaya</th>
            <th class="right">Laba Bersih</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data['rows'] as $r)
            <tr>
                <td>{{ $r['bucket'] }}</td>
                <td class="right">{{ number_format($r['omzet'], 0, ',', '.') }}</td>
                <td class="right">{{ number_format($r['hpp'], 0, ',', '.') }}</td>
                <td class="right">{{ number_format($r['laba_kotor'], 0, ',', '.') }}</td>
                <td class="right">{{ number_format($r['biaya'], 0, ',', '.') }}</td>
                <td class="right">{{ number_format($r['laba_bersih'], 0, ',', '.') }}</td>
            </tr>
        @endforeach
        <tr class="totals">
            <td>TOTAL</td>
            <td class="right">{{ number_format($data['totals']['omzet'], 0, ',', '.') }}</td>
            <td class="right">{{ number_format($data['totals']['hpp'], 0, ',', '.') }}</td>
            <td class="right">{{ number_format($data['totals']['laba_kotor'], 0, ',', '.') }}</td>
            <td class="right">{{ number_format($data['totals']['biaya'], 0, ',', '.') }}</td>
            <td class="right">{{ number_format($data['totals']['laba_bersih'], 0, ',', '.') }}</td>
        </tr>
    </tbody>
</table>
</body></html>
