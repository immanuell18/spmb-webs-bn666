<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreGelombangRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool { return auth()->check() && auth()->user()->role === 'admin'; }

    public function rules(): array
    {
        return [
            'nama'        => 'required|string|max:100',
            'tahun'       => 'required|integer|min:2020|max:' . (now()->year + 2),
            'tgl_mulai'   => 'required|date',
            'tgl_selesai' => 'required|date|after:tgl_mulai',
            'biaya_daftar' => 'required|numeric|min:0',
            'status'      => 'required|in:aktif,nonaktif',
        ];
    }

    public function messages(): array
    {
        return [
            'tgl_selesai.after' => 'Tanggal selesai harus setelah tanggal mulai.',
            'biaya_daftar.min'  => 'Biaya daftar tidak boleh negatif.',
        ];
    }
}
