<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BerkasUploadRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'jenis_berkas' => 'required|in:ijazah,transkrip_nilai,kartu_keluarga,akta_lahir,pas_foto,bukti_pembayaran',
            'file' => [
                'required',
                'file',
                'max:2048', // 2MB max
                function ($attribute, $value, $fail) {
                    $allowedMimes = ['application/pdf', 'image/jpeg', 'image/jpg', 'image/png'];
                    $fileMime = $value->getMimeType();
                    
                    if (!in_array($fileMime, $allowedMimes)) {
                        $fail('File harus berformat PDF, JPG, JPEG, atau PNG');
                    }
                    
                    // Additional validation for specific file types
                    $jenisBerkas = request('jenis_berkas');
                    
                    if (in_array($jenisBerkas, ['ijazah', 'transkrip_nilai', 'kartu_keluarga', 'akta_lahir'])) {
                        if ($fileMime !== 'application/pdf') {
                            $fail('Dokumen resmi harus berformat PDF');
                        }
                    }
                    
                    if ($jenisBerkas === 'pas_foto') {
                        if (!in_array($fileMime, ['image/jpeg', 'image/jpg', 'image/png'])) {
                            $fail('Pas foto harus berformat JPG, JPEG, atau PNG');
                        }
                        
                        // Check image dimensions for pas foto
                        if (in_array($fileMime, ['image/jpeg', 'image/jpg', 'image/png'])) {
                            $imageSize = getimagesize($value->getPathname());
                            if ($imageSize) {
                                $width = $imageSize[0];
                                $height = $imageSize[1];
                                $ratio = $width / $height;
                                
                                // Pas foto should be roughly 3:4 ratio (portrait)
                                if ($ratio > 1 || $ratio < 0.6) {
                                    $fail('Pas foto harus berformat portrait (3:4)');
                                }
                            }
                        }
                    }
                }
            ]
        ];
    }

    public function messages()
    {
        return [
            'jenis_berkas.required' => 'Jenis berkas wajib dipilih',
            'jenis_berkas.in' => 'Jenis berkas tidak valid',
            'file.required' => 'File wajib dipilih',
            'file.file' => 'File tidak valid',
            'file.max' => 'Ukuran file maksimal 2MB'
        ];
    }
}