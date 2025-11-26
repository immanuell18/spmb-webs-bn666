<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Services\CoordinateService;
use App\Models\Jurusan;

class PendaftaranRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            // Data Pribadi
            'nama_lengkap' => 'required|string|max:255',
            'nik' => 'required|string|size:16|unique:pendaftar_data_siswa,nik',
            'nisn' => 'nullable|string|size:10',
            'tanggal_lahir' => 'required|date|before:today',
            'tempat_lahir' => 'required|string|max:255',
            'jenis_kelamin' => 'required|in:L,P',
            'agama' => 'required|in:Islam,Kristen,Katolik,Hindu,Buddha,Konghucu',
            'no_hp' => 'required|string|max:15|regex:/^[0-9+\-\s]+$/',
            
            // Alamat
            'provinsi' => 'required|string',
            'kabupaten' => 'required|string', 
            'kecamatan' => 'required|string',
            'kelurahan' => 'required|string',
            'alamat' => 'required|string|max:500',
            
            // Koordinat (opsional tapi harus valid jika diisi)
            'latitude' => 'nullable|numeric|between:-11,6',
            'longitude' => 'nullable|numeric|between:95,141',
            
            // Data Orang Tua
            'nama_ayah' => 'required|string|max:255',
            'nama_ibu' => 'required|string|max:255',
            'pekerjaan_ayah' => 'required|string|max:255',
            'pekerjaan_ibu' => 'required|string|max:255',
            'no_hp_ortu' => 'required|string|max:15|regex:/^[0-9+\-\s]+$/',
            'penghasilan_ortu' => 'required|in:< 1 juta,1-3 juta,3-5 juta,> 5 juta',
            
            // Data Pendidikan
            'nama_sekolah' => 'required|string|max:255',
            'npsn' => 'nullable|string|max:8',
            'tahun_lulus' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'nilai_rata' => 'required|numeric|min:0|max:100',
            'alamat_sekolah' => 'required|string|max:500',
            'jurusan_id' => 'required|exists:jurusan,id',
            'gelombang_id' => 'required|exists:gelombang,id'
        ];
    }

    public function messages()
    {
        return [
            'nama_lengkap.required' => 'Nama lengkap wajib diisi',
            'nik.required' => 'NIK wajib diisi',
            'nik.size' => 'NIK harus 16 digit',
            'nik.unique' => 'NIK sudah terdaftar',
            'latitude.between' => 'Latitude harus dalam rentang Indonesia (-11 sampai 6)',
            'longitude.between' => 'Longitude harus dalam rentang Indonesia (95 sampai 141)',
            'tahun_lulus.min' => 'Tahun lulus minimal 2020',
            'nilai_rata.min' => 'Nilai rata-rata minimal 0',
            'nilai_rata.max' => 'Nilai rata-rata maksimal 100',
            'jurusan_id.required' => 'Jurusan wajib dipilih',
            'gelombang_id.required' => 'Gelombang wajib dipilih'
        ];
    }

    public function withValidator($validator)
    {
        $validator->after(function ($validator) {
            // Validate coordinates if both provided
            if ($this->filled('latitude') && $this->filled('longitude')) {
                $coordinateService = new CoordinateService();
                $validation = $coordinateService->validateCoordinateInput(
                    $this->latitude, 
                    $this->longitude
                );
                
                if (!$validation['valid']) {
                    foreach ($validation['errors'] as $error) {
                        $validator->errors()->add('coordinates', $error);
                    }
                }
            }
            
            // Validate jurusan quota
            if ($this->filled('jurusan_id')) {
                $jurusan = Jurusan::withCount(['pendaftar' => function($query) {
                    $query->whereIn('status', ['SUBMIT', 'ADM_PASS', 'PAID']);
                }])->find($this->jurusan_id);
                
                if ($jurusan && $jurusan->pendaftar_count >= $jurusan->kuota) {
                    $validator->errors()->add('jurusan_id', 'Kuota jurusan ' . $jurusan->nama . ' sudah penuh! (' . $jurusan->pendaftar_count . '/' . $jurusan->kuota . ')');
                }
            }
        });
    }
}