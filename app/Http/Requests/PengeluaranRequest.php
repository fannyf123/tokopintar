<?php

namespace App\Http\Requests;

use App\Models\Pengeluaran;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PengeluaranRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'kategori' => ['required', Rule::in(Pengeluaran::KATEGORI_LIST)],
            'tanggal' => ['required', 'date'],
            'jumlah' => ['required', 'integer', 'min:0'],
            'catatan' => ['nullable', 'string', 'max:255'],
            'bukti' => ['nullable', 'image', 'max:2048'],
        ];
    }

    public function messages(): array
    {
        return [
            'kategori.in' => 'Kategori tidak valid.',
            'jumlah.min' => 'Jumlah tidak boleh negatif.',
        ];
    }
}
