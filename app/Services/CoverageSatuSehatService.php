<?php

namespace App\Services;

use App\CoverageSatuSehat as AppCoverageSatuSehat;
use App\Models\CoverageSatuSehat;
use App\Http\Controllers\SatuSehatController;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Facades\Log;
use RuntimeException;
use Throwable;

class CoverageSatuSehatService
{
    public function cariAtauAmbil(
        string $noRkmMedis,
        string $patientId,
        string $kdPj,
        ?string $nomorPeserta,
        string $identifierSystem
    ): ?AppCoverageSatuSehat {

        // 1. Validasi parameter
        if (
            trim($patientId) === '' ||
            trim($kdPj) === '' ||
            trim($identifierSystem) === '' ||
            trim((string) $nomorPeserta) === ''
        ) {
            throw new RuntimeException(
                'Patient ID, kode penjamin, sistem identifier, dan nomor peserta wajib diisi.'
            );
        }

        // 2. Cek database lokal terlebih dahulu
        $coverage = AppCoverageSatuSehat::query()
            ->where('no_rkm_medis', $noRkmMedis)
            ->where('patient_id', $patientId)
            ->where('kd_pj', $kdPj)
            ->where('nomor_peserta', $nomorPeserta)
            ->where('status', 'found')
            ->whereNotNull('coverage_id')
            ->first();

        if ($coverage) {
            return $coverage;
        }

        // 3. Siapkan record lokal
        $coverage = AppCoverageSatuSehat::firstOrNew([
            'no_rkm_medis' => $noRkmMedis,
            'kd_pj' => $kdPj,
            'nomor_peserta' => $nomorPeserta,
        ]);

        $coverage->patient_id = $patientId;
        $coverage->status = 'pending';
        $coverage->pesan_error = null;
        $coverage->save();

        try {
            // 4. Gunakan token SATUSEHAT yang sudah ada
            $access_token = SatuSehatController::getTokenSehat();

            $baseUrl = cache()->get('base_url');

            if (empty($baseUrl)) {
                throw new RuntimeException(
                    'Base URL SATUSEHAT belum tersedia di cache.'
                );
            }

            $client = new Client([
                'base_uri' => rtrim($baseUrl, '/') . '/',
                'timeout' => 30,
                'connect_timeout' => 10,
            ]);

            // 5. GET Coverage berdasarkan Patient dan identifier
            $response = $client->request(
                'GET',
                'fhir-r4/v1/Coverage',
                [
                    'headers' => [
                        'Authorization' => "Bearer {$access_token}",
                        'Accept' => 'application/fhir+json',
                    ],
                    'query' => [
                        'beneficiary' => 'Patient/' . $patientId,
                        'identifier' => $identifierSystem . '|' . $nomorPeserta,
                    ],
                ]
            );

            $body = (string) $response->getBody();
            $result = json_decode($body, true);

            $coverage->response = $result ?? [
                'raw_response' => $body,
            ];

            $coverage->last_checked_at = now();

            // 6. Pastikan respons berupa Bundle
            if (
                !is_array($result) ||
                ($result['resourceType'] ?? null) !== 'Bundle'
            ) {
                throw new RuntimeException(
                    'Respons pencarian Coverage bukan FHIR Bundle.'
                );
            }

            $entries = $result['entry'] ?? [];

            // Cocokkan kembali pasien dan identifier
            // untuk menghindari mengambil Coverage yang keliru.
            $resource = null;

            foreach ($entries as $entry) {
                $item = $entry['resource'] ?? [];

                if (($item['resourceType'] ?? null) !== 'Coverage') {
                    continue;
                }

                if (empty($item['id'])) {
                    continue;
                }

                $beneficiary = $item['beneficiary']['reference'] ?? null;

                $identifierCocok = collect(
                    $item['identifier'] ?? []
                )->contains(function ($identifier) use (
                    $identifierSystem,
                    $nomorPeserta
                ) {
                    return ($identifier['system'] ?? null)
                        === $identifierSystem
                        && ($identifier['value'] ?? null)
                        === $nomorPeserta;
                });

                if (
                    $beneficiary === 'Patient/' . $patientId &&
                    $identifierCocok
                ) {
                    $resource = $item;
                    break;
                }
            }

            // 7. Coverage tidak ditemukan
            if (!$resource) {
                $coverage->coverage_id = null;
                $coverage->coverage_status = null;
                $coverage->status = 'not_found';
                $coverage->pesan_error = null;
                $coverage->save();

                return null;
            }

            // 8. Simpan ID Coverage SATUSEHAT
            $coverage->coverage_id = $resource['id'];
            $coverage->coverage_status = $resource['status'] ?? null;
            $coverage->status = 'found';
            $coverage->pesan_error = null;
            $coverage->response = $result;
            $coverage->last_checked_at = now();
            $coverage->save();

            return $coverage;
        } catch (BadResponseException $e) {
            // Respons HTTP 4xx/5xx
            $response = $e->getResponse();
            $body = $response
                ? (string) $response->getBody()
                : null;

            $coverage->status = 'failed';
            $coverage->pesan_error = $body ? mb_substr($body, 0, 60000) : $e->getMessage();
            $coverage->response = $body ? (json_decode($body, true) ?? ['raw_response' => $body]) : null;
            $coverage->last_checked_at = now();
            $coverage->save();

            Log::error('HTTP error pencarian Coverage SATUSEHAT', [
                'no_rkm_medis' => $noRkmMedis,
                'patient_id' => $patientId,
                'kd_pj' => $kdPj,
                'http_status' => $response?->getStatusCode(),
                'error' => $e->getMessage(),
            ]);

            throw $e;
        } catch (Throwable $e) {
            dd($e);
            // Error token, koneksi, parsing, atau error lainnya
            $coverage->status = 'failed';
            $coverage->pesan_error = mb_substr(
                $e->getMessage(),
                0,
                60000
            );
            $coverage->last_checked_at = now();
            $coverage->save();

            Log::error('Pencarian Coverage SATUSEHAT gagal', [
                'no_rkm_medis' => $noRkmMedis,
                'patient_id' => $patientId,
                'kd_pj' => $kdPj,
                'error' => $e->getMessage(),
            ]);

            throw $e;
        }
    }
}
