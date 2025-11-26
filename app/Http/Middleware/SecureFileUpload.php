<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class SecureFileUpload
{
    public function handle(Request $request, Closure $next)
    {
        if ($request->hasFile('file') || $request->hasFile('bukti_bayar')) {
            $files = $request->allFiles();
            
            foreach ($files as $file) {
                if (is_array($file)) {
                    foreach ($file as $f) {
                        $this->validateFile($f);
                    }
                } else {
                    $this->validateFile($file);
                }
            }
        }
        
        return $next($request);
    }
    
    private function validateFile($file)
    {
        // Validasi ekstensi file
        $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf', 'doc', 'docx'];
        $extension = strtolower($file->getClientOriginalExtension());
        
        if (!in_array($extension, $allowedExtensions)) {
            abort(422, 'File type not allowed');
        }
        
        // Validasi ukuran file (max 5MB)
        if ($file->getSize() > 5 * 1024 * 1024) {
            abort(422, 'File size too large');
        }
        
        // Validasi MIME type
        $allowedMimes = [
            'image/jpeg', 'image/png', 'application/pdf',
            'application/msword', 'application/vnd.openxmlformats-officedocument.wordprocessingml.document'
        ];
        
        if (!in_array($file->getMimeType(), $allowedMimes)) {
            abort(422, 'Invalid file type');
        }
    }
}