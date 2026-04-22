<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreJurusanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool { return auth()->check() && auth()->user()->role === 'admin'; }

    public function rules(): array
    {
        $jurusanId = $this->route('id'); // untuk update
        return [
            'kode'      => 'required|string|max:10|unique:jurusan,kode,' . $jurusanId,
            'nama'      => 'required|string|max:100',
            'deskripsi' => 'nullable|string|max:500',
            'kuota'     => 'required|integer|min:1|max:1000',
        ];
    }

    public function messages(): array
    {
        return [
            'kode.unique'   => 'Kode jurusan sudah digunakan.',
            'kuota.min'     => 'Kuota minimal 1 siswa.',
        ];
    }
}
