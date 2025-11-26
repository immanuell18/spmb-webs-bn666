<?php

namespace App\Http\Middleware;

use Illuminate\Foundation\Http\Middleware\VerifyCsrfToken as Middleware;

class VerifyCsrfToken extends Middleware
{
    /**
     * The URIs that should be excluded from CSRF verification.
     *
     * @var array<int, string>
     */
    protected $except = [
        'siswa/*',
        'csrf-token',
        'refresh-token',
        'test-csrf'
    ];
    
    /**
     * Get the except array for debugging
     */
    public function getExcept()
    {
        return $this->except;
    }
}
