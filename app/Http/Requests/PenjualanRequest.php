<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PenjualanRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'pelanggan_id' => ['nullable', 'exists:pelanggans,id'],
            'metode_bayar' => ['required', Rule::in(['cash', 'transfer', 'qris', 'kartu'])],
            'diskon' => ['nullable', 'integer', 'min:0'],
            'pajak' => ['nullable', 'integer', 'min:0'],
            'dibayar' => ['required', 'integer', 'min:0'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_id' => ['required', 'exists:barangs,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.diskon_item' => ['nullable', 'integer', 'min:0'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Keranjang kosong.',
            'items.min' => 'Keranjang kosong.',
            'items.*.qty.min' => 'Qty harus minimal 1.',
            'dibayar.required' => 'Jumlah dibayar wajib diisi.',
        ];
    }
}
