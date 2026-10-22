<?php

namespace App\Jobs;

use App\AccountSatuSehat;
use App\ChargeItemSatuSehat;
use App\Http\Controllers\SatuSehatController;
use Carbon\Carbon;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class SendChargeItemJob implements ShouldQueue
{
    // use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    // /**
    //  * Create a new job instance.
    //  *
    //  * @return void
    //  */
    // public function __construct()
    // {
    //     //
    // }

    // /**
    //  * Execute the job.
    //  *
    //  * @return void
    //  */
    // public function handle()
    // {
    //     //
    // }

    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $tries = 3;

    public $timeout = 120;

    protected $noRawat;
    protected $jenisRawat;

    public function __construct($noRawat, $jenisRawat)
    {
        $this->noRawat = $noRawat;
        $this->jenisRawat = $jenisRawat;

        $this->onQueue('satusehat');
    }

    // public function handle()
    // {
    //     $account = AccountSatuSehat::where(
    //         'no_rawat',
    //         $this->noRawat
    //     )
    //         ->where('jenis_rawat', $this->jenisRawat)
    //         ->where('status', 'sent')
    //         ->whereNotNull('account_id')
    //         ->first();

    //     if (!$account) {
    //         throw new RuntimeException(
    //             'Account SATUSEHAT belum tersedia untuk kunjungan '
    //                 . $this->noRawat
    //         );
    //     }

    //     $ambilItem = ChargeItemSatuSehat::where(
    //         'no_rawat',
    //         $this->noRawat
    //     )
    //         ->where('status', 'pending')
    //         ->whereNull('charge_item_id')
    //         ->orderBy('id')
    //         ->get();

    //     foreach ($ambilItem as $item) {
    //         // Jangan kirim ulang item yang sudah sukses.
    //         if ($item->status === 'sent' && $item->charge_item_id) {
    //             continue;
    //         }

    //         if (
    //             empty($item->patient_id) ||
    //             empty($item->encounter_id) ||
    //             empty($item->kptl_code) ||
    //             empty($item->performer_id)
    //         ) {
    //             $item->status = 'failed';
    //             $item->pesan_error =
    //                 'Patient, Encounter, kode KPTL, atau Performer belum lengkap.';
    //             $item->save();

    //             return;
    //         }

    //         $payload = [
    //             'resourceType' => 'ChargeItem',
    //             'status' => 'billable',

    //             'code' => [
    //                 'coding' => [
    //                     [
    //                         'system' => $item->kptl_system,
    //                         'code' => $item->kptl_code,
    //                         // 'display' => $item->kptl_display,
    //                     ],
    //                 ],
    //             ],

    //             'subject' => [
    //                 'reference' => 'Patient/' . $item->patient_id,
    //             ],

    //             'context' => [
    //                 'reference' => 'Encounter/' . $item->encounter_id,
    //             ],

    //             'account' => [
    //                 [
    //                     'reference' => 'Account/' . $account->account_id,
    //                 ],
    //             ],

    //             'performer' => [
    //                 [
    //                     'actor' => [
    //                         'reference' => 'Practitioner/' . $item->performer_id,
    //                     ],
    //                 ],
    //             ],

    //             'quantity' => [
    //                 'value' => (float) $item->jumlah,
    //             ],

    //             'extension' => [
    //                 [
    //                     'url' =>
    //                     'https://fhir.kemkes.go.id/r4/StructureDefinition/unitPrice',
    //                     'valueMoney' => [
    //                         'value' => (float) $item->harga_satuan,
    //                         'currency' => 'IDR',
    //                     ],
    //                 ],
    //                 [
    //                     'url' =>
    //                     'https://fhir.kemkes.go.id/r4/StructureDefinition/totalPrice',
    //                     'valueMoney' => [
    //                         'value' => (float) $item->total_harga,
    //                         'currency' => 'IDR',
    //                     ],
    //                 ],
    //             ],
    //         ];

    //         // Kirim waktu sesuai kolom yang tersedia.
    //         if ($item->occurrence_start && $item->occurrence_end) {
    //             $payload['occurrencePeriod'] = [
    //                 'start' => $item->occurrence_start,
    //                 'end' => $item->occurrence_end,
    //             ];
    //         } elseif ($item->occurrence_start) {
    //             $payload['occurrenceDateTime'] =
    //                 $item->occurrence_start;
    //         } else {
    //             $item->status = 'failed';
    //             $item->pesan_error =
    //                 'Waktu pelayanan ChargeItem belum tersedia.';
    //             $item->save();

    //             return;
    //         }

    //         $item->status = 'pending';
    //         $item->pesan_error = null;
    //         $item->save();

    //         try {
    //             $access_token = SatuSehatController::getTokenSehat();
    //             $baseUrl = cache()->get('base_url');

    //             if (empty($baseUrl)) {
    //                 throw new RuntimeException(
    //                     'Base URL SATUSEHAT tidak tersedia.'
    //                 );
    //             }

    //             $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
    //             $response = $client->request('POST', 'fhir-r4/v1/ChargeItem', [
    //                 'headers' => [
    //                     'Authorization' => "Bearer {$access_token}"
    //                 ],
    //                 'json' => $payload
    //             ]);

    //             $body = (string) $response->getBody();
    //             $result = json_decode($body, true);

    //             $item->response = $result ?? [
    //                 'raw_response' => $body,
    //             ];

    //             if (
    //                 !is_array($result) ||
    //                 ($result['resourceType'] ?? null) !== 'ChargeItem' ||
    //                 empty($result['id'])
    //             ) {
    //                 throw new RuntimeException(
    //                     'Respons POST tidak berisi ID ChargeItem yang valid.'
    //                 );
    //             }

    //             $item->charge_item_id = $result['id'];
    //             $item->status = 'sent';
    //             $item->pesan_error = null;
    //             $item->sent_at = now();
    //             $item->save();
    //         } catch (BadResponseException $e) {
    //             $httpResponse = $e->getResponse();
    //             $body = $httpResponse
    //                 ? (string) $httpResponse->getBody()
    //                 : '';

    //             $result = json_decode($body, true);

    //             $item->response = $result ?? [
    //                 'raw_response' => $body,
    //             ];
    //             $item->pesan_error = mb_substr(
    //                 $body ?: $e->getMessage(),
    //                 0,
    //                 60000
    //             );

    //             $httpStatus = $httpResponse?->getStatusCode() ?? 0;

    //             if ($httpStatus === 429 || $httpStatus >= 500) {
    //                 $item->status = 'pending';
    //                 $item->save();

    //                 // Biarkan Laravel mencoba kembali.
    //                 throw $e;
    //             }

    //             // Error 4xx umumnya perlu diperbaiki sebelum retry.
    //             $item->status = 'failed';
    //             $item->save();
    //         }
    //     }
    // }

    public function handle()
    {
        // 1. Ambil Account SATUSEHAT untuk kunjungan ini.
        $account = AccountSatuSehat::where('no_rawat', $this->noRawat)
            ->where('jenis_rawat', $this->jenisRawat)
            ->whereNotNull('account_id')
            ->first();

        if (!$account) {
            throw new RuntimeException(
                'Account SATUSEHAT belum tersedia untuk kunjungan '
                    . $this->noRawat
                    . ' (' . $this->jenisRawat . ').'
            );
        }

        // 2. Ambil hanya item milik kunjungan dan jenis rawat ini.
        $ambilItem = ChargeItemSatuSehat::where('no_rawat', $this->noRawat)
            ->where('jenis_rawat', $this->jenisRawat)
            ->whereIn('status', ['pending', 'failed'])
            ->whereNull('charge_item_id')
            ->orderBy('id')
            ->get();

        if ($ambilItem->isEmpty()) {
            return;
        }

        // 3. Siapkan koneksi SATUSEHAT.
        // $baseUrl = cache()->get('base_url');

        // if (empty($baseUrl)) {
        //     throw new RuntimeException(
        //         'Base URL SATUSEHAT tidak tersedia.'
        //     );
        // }

        $access_token = SatuSehatController::getTokenSehat();

        if (empty($access_token)) {
            throw new RuntimeException(
                'Access token SATUSEHAT tidak tersedia.'
            );
        }

        foreach ($ambilItem as $item) {
            // Validasi data wajib sebelum mengirim.
            if (
                empty($item->patient_id) ||
                empty($item->encounter_id) ||
                empty($item->kptl_system) ||
                empty($item->kptl_code) ||
                empty($item->performer_id)
            ) {
                $item->status = 'failed';
                $item->pesan_error =
                    'Patient, Encounter, sistem KPTL, kode KPTL, '
                    . 'atau Performer belum lengkap.';
                $item->save();

                // Item lain tetap diproses.
                continue;
            }

            // Konversi waktu pelayanan dari WIB ke UTC.
            $start = Carbon::parse(
                $item->occurrence_start,
                'Asia/Jakarta'
            )->utc()->format('Y-m-d\TH:i:s\Z');

            $end = Carbon::parse(
                $item->occurrence_end,
                'Asia/Jakarta'
            )->utc()->format('Y-m-d\TH:i:s\Z');

            // if ($item->occurrence_start && $item->occurrence_end) {
            //     $start = Carbon::parse(
            //         $item->occurrence_start,
            //         'Asia/Jakarta'
            //     )->utc()->format('Y-m-d\TH:i:s\Z');

            //     $end = Carbon::parse(
            //         $item->occurrence_end,
            //         'Asia/Jakarta'
            //     )->utc()->format('Y-m-d\TH:i:s\Z');

            //     $payload['occurrencePeriod'] = [
            //         'start' => $start,
            //         'end' => $end,
            //     ];
            // } elseif ($item->occurrence_start) {
            //     $start = Carbon::parse(
            //         $item->occurrence_start,
            //         'Asia/Jakarta'
            //     )->utc()->format('Y-m-d\TH:i:s\Z');

            //     $end = Carbon::parse(
            //         $item->occurrence_end,
            //         'Asia/Jakarta'
            //     )->utc()->format('Y-m-d\TH:i:s\Z');

            //     $payload['occurrencePeriod'] = [
            //         'start' => $start,
            //         'end' => $end,
            //     ];
            // } else {
            //     $item->status = 'failed';
            //     $item->pesan_error = 'Waktu pelayanan ChargeItem belum tersedia.';
            //     $item->save();

            //     continue;
            // }

            $payload = [
                'resourceType' => 'ChargeItem',
                'status' => 'billable',

                'code' => [
                    'coding' => [
                        [
                            'system' => $item->kptl_system,
                            'code' => $item->kptl_code,
                            // 'display' => $item->kptl_display,
                        ],
                    ],
                ],

                'subject' => [
                    'reference' => 'Patient/' . $item->patient_id,
                ],

                'context' => [
                    'reference' => 'Encounter/' . $item->encounter_id,
                ],

                "occurrencePeriod" => [
                    "start" => $start,
                    "end" => $end,
                ],

                'account' => [
                    [
                        'reference' => 'Account/' . $account->account_id,
                    ],
                ],

                'performer' => [
                    [
                        'actor' => [
                            'reference' => 'Practitioner/'
                                . $item->performer_id,
                        ],
                    ],
                ],

                'quantity' => [
                    'value' => (float) $item->jumlah,
                ],

                'extension' => [
                    [
                        'url' =>
                        'https://fhir.kemkes.go.id/r4/StructureDefinition/unitPrice',
                        'valueMoney' => [
                            'value' => (float) $item->harga_satuan,
                            'currency' => 'IDR',
                        ],
                    ],
                    [
                        'url' =>
                        'https://fhir.kemkes.go.id/r4/StructureDefinition/totalPrice',
                        'valueMoney' => [
                            'value' => (float) $item->total_harga,
                            'currency' => 'IDR',
                        ],
                    ],
                ],
            ];

            $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);

            try {
                $response = $client->request('POST', 'fhir-r4/v1/ChargeItem', [
                    'headers' => [
                        'Authorization' => "Bearer {$access_token}"
                    ],
                    'json' => $payload
                ]);

                $body = (string) $response->getBody();
                $result = json_decode($body, true);

                $item->response = $result ?: [
                    'raw_response' => $body,
                ];

                if (
                    !is_array($result) ||
                    !isset($result['resourceType']) ||
                    $result['resourceType'] !== 'ChargeItem' ||
                    empty($result['id'])
                ) {
                    // Jangan otomatis POST ulang ketika hasil server
                    // tidak dapat dipastikan.
                    $item->status = 'failed';
                    $item->pesan_error =
                        'Respons SATUSEHAT tidak berisi ID ChargeItem '
                        . 'yang valid. Periksa respons sebelum mengirim ulang.';
                    $item->save();

                    continue;
                }

                $item->charge_item_id = $result['id'];
                $item->status = 'sent';
                $item->pesan_error = null;
                $item->sent_at = now();
                $item->save();
            } catch (BadResponseException $e) {
                $httpResponse = $e->getResponse();
                $body = $httpResponse
                    ? (string) $httpResponse->getBody()
                    : '';

                $result = json_decode($body, true);

                $item->response = $result ?: [
                    'raw_response' => $body,
                ];

                $item->pesan_error = mb_substr(
                    $body ?: $e->getMessage(),
                    0,
                    60000
                );

                $httpStatus = $httpResponse
                    ? $httpResponse->getStatusCode()
                    : 0;

                if (
                    $httpStatus === 429 ||
                    $httpStatus >= 500 ||
                    $httpStatus === 0
                ) {
                    // Simpan sebelum retry.
                    $item->status = 'pending';
                    $item->save();

                    throw $e;
                }

                // Error HTTP 4xx lainnya perlu diperiksa/diperbaiki.
                $item->status = 'failed';
                $item->save();

                continue;
            } catch (RequestException $e) {
                // Kegagalan jaringan: hasil POST mungkin belum diketahui.
                $item->pesan_error = mb_substr(
                    $e->getMessage(),
                    0,
                    60000
                );
                $item->status = 'pending';
                $item->save();

                // Biarkan Laravel melakukan retry.
                throw $e;
            }
        }
    }

    public function failed(Throwable $exception)
    {
        try {
            ChargeItemSatuSehat::where('no_rawat', $this->noRawat)
                ->where('jenis_rawat', $this->jenisRawat)
                ->where('status', 'pending')
                ->whereNull('charge_item_id')
                ->update([
                    'status' => 'failed',
                    'pesan_error' => mb_substr(
                        $exception->getMessage()
                            ? $exception->getMessage()
                            : 'Job gagal setelah seluruh percobaan ulang.',
                        0,
                        60000
                    ),
                ]);

            Log::error('SendChargeItemJob gagal setelah percobaan ulang.', [
                'no_rawat' => $this->noRawat,
                'jenis_rawat' => $this->jenisRawat,
                'error' => $exception->getMessage(),
            ]);
        } catch (Throwable $e) {
            Log::error('Gagal memperbarui status ChargeItem setelah Job gagal.', [
                'no_rawat' => $this->noRawat,
                'jenis_rawat' => $this->jenisRawat,
                'job_error' => $exception->getMessage(),
                'database_error' => $e->getMessage(),
            ]);
        }
    }
}
