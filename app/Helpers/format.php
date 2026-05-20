<?php

use Carbon\Carbon;

if (! function_exists('format_rupiah')) {
    function format_rupiah(int|null $amount, bool $withPrefix = true): string
    {
        $amount = (int) ($amount ?? 0);
        $sign = $amount < 0 ? '-' : '';
        $abs = abs($amount);
        $formatted = number_format($abs, 0, ',', '.');
        return ($withPrefix ? 'Rp ' : '') . $sign . $formatted;
    }
}

if (! function_exists('format_tanggal_id')) {
    function format_tanggal_id($date, bool $withTime = false): string
    {
        if (empty($date)) {
            return '-';
        }
        $c = $date instanceof Carbon ? $date : Carbon::parse($date);
        $bulan = [
            1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni',
            'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember',
        ];
        $out = $c->day . ' ' . $bulan[(int) $c->month] . ' ' . $c->year;
        if ($withTime) {
            $out .= ' ' . $c->format('H:i');
        }
        return $out;
    }
}
