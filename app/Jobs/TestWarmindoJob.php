<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class TestWarmindoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public string $message;

    public function __construct(string $message = 'Job POS Warmindo Berhasil Diproses Horizon!')
    {
        $this->message = $message;
    }

    public function handle(): void
    {
        Log::info('[HORIZON TEST] ' . $this->message);
    }
}
