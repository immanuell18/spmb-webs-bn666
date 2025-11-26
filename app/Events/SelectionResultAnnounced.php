<?php

namespace App\Events;

use App\Models\Pendaftar;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class SelectionResultAnnounced
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pendaftar;

    public function __construct(Pendaftar $pendaftar)
    {
        $this->pendaftar = $pendaftar;
    }
}