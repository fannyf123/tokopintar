<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class BarangRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $id = $this->route('barang');

        return [
            'kode' => ['nullable', 'string', 'max:50', Rule::unique('barangs', 'kode')->ignore($id)],
            'barcode' => ['nullable', 'string', 'max:100', Rule::unique('barangs', 'barcode')->ignore($id)],
            'nama' => ['required', 'string', 'max:255'],
            'kategori_id' => ['required', 'exists:kategoris,id'],
            'supplier_id' => ['nullable', 'exists:suppliers,id'],
            'satuan' => ['required', 'string', 'max:20'],
            'harga_beli' => ['required', 'integer', 'min:0'],
            'harga_jual' => ['required', 'integer', 'min:0', 'gte:harga_beli'],
            'stok_min' => ['required', 'integer', 'min:0'],
            'stok_max' => ['required', 'integer', 'min:0', 'gte:stok_min'],
            'aktif' => ['nullable', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama barang wajib diisi.',
            'kategori_id.required' => 'Kategori wajib dipilih.',
            'harga_jual.gte' => 'Harga jual tidak boleh kurang dari harga beli.',
            'stok_max.gte' => 'Stok maksimal tidak boleh kurang dari stok minimal.',
            'kode.unique' => 'Kode sudah dipakai.',
            'barcode.unique' => 'Barcode sudah dipakai.',
        ];
    }
}
