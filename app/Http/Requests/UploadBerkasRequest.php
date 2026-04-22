<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadBerkasRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $mimes = 'mimes:pdf,jpg,jpeg,png';
        
        // Cek jika jenis_berkas adalah 'foto', jangan izinkan 'pdf'
        if ($this->input('jenis_berkas') === 'foto') {
            $mimes = 'mimes:jpg,jpeg,png';
        }

        return [
            'jenis_berkas' => 'required|in:ijazah,rapor,kip,kks,akta,kk,foto',
            'file'         => "required|file|{$mimes}|max:2048",
        ];
    }

    public function messages(): array
    {
        $mimesMsg = 'Format file harus PDF, JPG, atau PNG.';
        if ($this->input('jenis_berkas') === 'foto') {
            $mimesMsg = 'Khusus Pas Foto, format file harus gambar (JPG/PNG), tidak boleh PDF.';
        }

        return [
            'jenis_berkas.required' => 'Jenis berkas harus dipilih.',
            'jenis_berkas.in'       => 'Jenis berkas tidak valid.',
            'file.required'         => 'File berkas harus diupload.',
            'file.mimes'            => $mimesMsg,
            'file.max'              => 'Ukuran file maksimal 2MB.',
        ];
    }
}
