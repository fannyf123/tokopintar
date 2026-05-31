<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class KategoriRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        $id = $this->route('kategori');

        return [
            'nama' => ['required', 'string', 'max:100', Rule::unique('kategoris', 'nama')->ignore($id)],
            'deskripsi' => ['nullable', 'string', 'max:255'],
            'ikon' => ['nullable', 'string', 'max:50'],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama kategori wajib diisi.',
            'nama.unique' => 'Nama kategori sudah dipakai.',
        ];
    }
}
