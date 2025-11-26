<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Wilayah;
use App\Models\Province;
use App\Models\Regency;
use App\Models\District;
use Illuminate\Support\Facades\DB;

class WilayahApiController extends Controller
{
    public function getProvinsi()
    {
        $provinsi = Province::select('id', 'name')
                          ->orderBy('name')
                          ->get();
        
        return response()->json($provinsi);
    }

    public function getKabupaten($provinsiId)
    {
        try {
            $kabupaten = Regency::select('id', 'name')
                               ->where('province_id', $provinsiId)
                               ->orderBy('name')
                               ->get();
            
            return response()->json($kabupaten);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function getKecamatan($provinsiId, $kabupatenId)
    {
        try {
            $kecamatan = District::select('id', 'name')
                               ->where('regency_id', $kabupatenId)
                               ->orderBy('name')
                               ->get();
            
            return response()->json($kecamatan);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function getKelurahan($provinsiId, $kabupatenId, $kecamatanId)
    {
        try {
            $kelurahan = \App\Models\Village::select('id', 'name')
                               ->where('district_id', $kecamatanId)
                               ->orderBy('name')
                               ->get();
            
            return response()->json($kelurahan);
        } catch (\Exception $e) {
            return response()->json([]);
        }
    }

    public function searchWilayah(Request $request)
    {
        $search = $request->get('q', '');
        
        $wilayah = Wilayah::where(function($query) use ($search) {
                            $query->where('kelurahan', 'like', "%{$search}%")
                                  ->orWhere('kecamatan', 'like', "%{$search}%")
                                  ->orWhere('kabupaten', 'like', "%{$search}%")
                                  ->orWhere('kodepos', 'like', "%{$search}%");
                          })
                          ->select('id', 'kelurahan', 'kecamatan', 'kabupaten', 'provinsi', 'kodepos')
                          ->limit(20)
                          ->get();
        
        return response()->json($wilayah);
    }
}