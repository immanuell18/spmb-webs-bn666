<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePersyaratanRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool { return auth()->check() && auth()->user()->role === 'admin'; }

    public function rules(): array
    {
        return [
            'nama'      => 'required|string|max:255',
            'deskripsi' => 'nullable|string|max:500',
            'jenis'     => 'required|in:dokumen,foto,sertifikat',
            'wajib'     => 'sometimes|boolean',
            'urutan'    => 'required|integer|min:1',
        ];
    }

    public function messages(): array
    {
        return [
            'jenis.in' => 'Jenis persyaratan harus dokumen, foto, atau sertifikat.',
        ];
    }
}
