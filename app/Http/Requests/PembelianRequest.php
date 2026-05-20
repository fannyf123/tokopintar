<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PembelianRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin', 'gudang') ?? false;
    }

    public function rules(): array
    {
        return [
            'tanggal' => ['required', 'date'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'metode_bayar' => ['required', Rule::in(['cash', 'transfer', 'tempo'])],
            'dibayar' => ['required', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:255'],
            'items' => ['required', 'array', 'min:1'],
            'items.*.barang_id' => ['required', 'exists:barangs,id'],
            'items.*.qty' => ['required', 'integer', 'min:1'],
            'items.*.harga_beli' => ['required', 'integer', 'min:0'],
            'items.*.no_batch' => ['nullable', 'string', 'max:50'],
            'items.*.tanggal_kadaluarsa' => ['nullable', 'date', 'after_or_equal:tanggal'],
        ];
    }

    public function messages(): array
    {
        return [
            'items.required' => 'Minimal harus ada satu barang.',
            'items.min' => 'Minimal harus ada satu barang.',
            'items.*.barang_id.required' => 'Barang wajib dipilih.',
            'items.*.qty.min' => 'Qty harus minimal 1.',
        ];
    }
}
