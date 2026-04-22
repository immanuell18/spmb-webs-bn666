<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UploadBuktiBayarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'bukti_bayar' => 'required|file|mimes:jpg,jpeg,png,pdf|max:2048',
        ];
    }

    public function messages(): array
    {
        return [
            'bukti_bayar.required' => 'Bukti pembayaran harus diupload.',
            'bukti_bayar.mimes'    => 'Format bukti bayar harus JPG, PNG, atau PDF.',
            'bukti_bayar.max'      => 'Ukuran file maksimal 2MB.',
        ];
    }
}
