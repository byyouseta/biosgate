<?php

namespace App\Jobs;

use App\Services\ChargeItemService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SyncChargeItemJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    protected $no_rawat;

    public function __construct($no_rawat)
    {
        $this->no_rawat = $no_rawat;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle(ChargeItemService $service)
    {
        $result = $service->sync($this->no_rawat);

        \Log::info('Sync ChargeItem selesai', [
            'no_rawat' => $this->no_rawat,
            'result' => $result,
        ]);
    }
}
