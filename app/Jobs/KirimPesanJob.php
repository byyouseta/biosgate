<?php

namespace App\Jobs;

use App\Http\Controllers\BookingPendaftaranController;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class KirimPesanJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $noPasien, $tglPeriksa, $templateId;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($noPasien, $tglPeriksa, $templateId)
    {
        $this->noPasien = $noPasien;
        $this->tglPeriksa = $tglPeriksa;
        $this->templateId = $templateId;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        try {
            // Simpan log awal
            $log = \App\LogKirimPesan::create([
                'no_rm'   => $this->noPasien,
                'tgl_periksa' => $this->tglPeriksa,
                'template_id' => $this->templateId,
                'status'      => 'pending',
            ]);

            // Lakukan proses kirim pesan
            $hasil = BookingPendaftaranController::kirimPesan($this->noPasien, $this->tglPeriksa, $this->templateId);

            // Update status
            $log->update([
                'status'     => $hasil && $hasil->original['success'] == true ?  'berhasil' : 'gagal',
                'keterangan' => $hasil->original['message'] ?? 'Terkirim',
            ]);
        } catch (\Throwable $e) {
            // Update jika gagal
            \App\LogKirimPesan::create([
                'no_rm'   => $this->noPasien,
                'tgl_periksa' => $this->tglPeriksa,
                'template_id' => $this->templateId,
                'status'      => 'gagal',
                'keterangan'  => $e->getMessage(),
            ]);
        }
    }
}
