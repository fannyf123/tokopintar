<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class PelangganRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()?->hasRole('admin', 'kasir') ?? false;
    }

    public function rules(): array
    {
        $id = $this->route('pelanggan');

        return [
            'nama' => ['required', 'string', 'max:255'],
            'no_hp' => [
                'nullable', 'string', 'max:30',
                Rule::unique('pelanggans', 'no_hp')->ignore($id),
            ],
            'alamat' => ['nullable', 'string', 'max:255'],
            'tipe' => ['required', Rule::in(['umum', 'member'])],
        ];
    }

    public function messages(): array
    {
        return [
            'nama.required' => 'Nama pelanggan wajib diisi.',
            'no_hp.unique' => 'Nomor HP sudah dipakai.',
            'tipe.in' => 'Tipe harus umum atau member.',
        ];
    }
}
