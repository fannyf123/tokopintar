<?php

namespace App\Http\Requests;

use App\Models\StockMovement;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class MutasiStokRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin', 'gudang') ?? false;
    }

    public function rules(): array
    {
        return [
            'barang_id' => ['required', 'exists:barangs,id'],
            'batch_id' => ['nullable', 'exists:product_batches,id'],
            'jenis' => ['required', Rule::in(StockMovement::JENIS_MUTASI)],
            'qty' => ['required', 'integer', 'min:1'],
            'alasan' => ['required', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'barang_id.required' => 'Barang wajib dipilih.',
            'jenis.in' => 'Jenis mutasi tidak valid.',
            'qty.min' => 'Qty harus minimal 1.',
            'alasan.required' => 'Alasan wajib diisi untuk audit trail.',
        ];
    }
}
