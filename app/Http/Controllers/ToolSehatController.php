<?php

namespace App\Http\Controllers;

use GuzzleHttp\Exception\ClientException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ToolSehatController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function pegawaiSync(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Tool Satu Sehat');
        session()->put('cucu', 'Sinkron Pegawai');
        set_time_limit(0);

        $data = DB::connection('mysqlkhanza')->table('pegawai')->get();

        return view('satu_sehat.pegawai_sync', compact('data'));
    }

    public function pegawaiSyncEdit($id)
    {
        $data = DB::connection('mysqlkhanza')->table('pegawai')->where('id', Crypt::decrypt($id))->first();

        return response()->json($data);
    }

    public function pegawaiSyncUpdate(Request $request, $id)
    {
        $pegawai = DB::connection('mysqlkhanza')->table('pegawai')->where('id', $id)->first();

        DB::connection('mysqlkhanza')->table('pegawai')->where('id', $id)->update([
            'nama' => $request->nama,
            'nik' => $request->nik,
            'no_ktp' => $request->no_ktp,
        ]);

        return response()->json(['success' => true]);
    }

    public function pegawaiSyncProses(Request $request)
    {
        $pegawai = DB::connection('mysqlkhanza')->table('pegawai')
            ->where('stts_aktif', 'AKTIF')
            ->where(function ($q) {
                $q->whereNotNull('no_ktp')
                    ->where('no_ktp', '!=', '')
                    ->where('no_ktp', '!=', '-');
            })
            ->get();


        foreach ($pegawai as $p) {
            $access_token = SatuSehatController::getTokenSehat();
            // dd($access_token);
            try {
                $client = new \GuzzleHttp\Client(['base_uri' => cache()->get('base_url')]);
                $response = $client->request('GET', 'fhir-r4/v1/Practitioner?identifier=https://fhir.kemkes.go.id/id/nik|' . $p->no_ktp, [
                    'headers' => [
                        'Authorization' => "Bearer {$access_token}"
                    ]
                ]);
            } catch (ClientException $e) {
                if ($e->hasResponse()) {
                    $response = $e->getResponse();
                    $test = json_decode((string) $response->getBody());

                    dd($test);
                } else {
                    dd('Error tidak ada response');
                }
            }

            $datanakes = json_decode($response->getBody());

            dd($p, $datanakes);
        }
    }

    public function mapingRadio(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Tool Satu Sehat');
        session()->put('cucu', 'Maping Radiologi');

        $data = DB::connection('mysqlkhanza')->table('jns_perawatan_radiologi')
            ->leftJoin('fhir_rad', 'fhir_rad.kd_jenis_prw', '=', 'jns_perawatan_radiologi.kd_jenis_prw')
            ->leftJoin('fhir_master_loinc_rad', 'fhir_rad.kd_loinc', '=', 'fhir_master_loinc_rad.kd_loinc')
            ->select(
                'fhir_rad.*',
                'fhir_master_loinc_rad.*',
                'jns_perawatan_radiologi.*'
            )
            ->get();

        $master = DB::connection('mysqlkhanza')->table('fhir_master_loinc_rad')->get();

        return view('satu_sehat.map_radiologi', compact('data', 'master'));
    }

    public function mapingRadioEdit($id)
    {
        $data = DB::connection('mysqlkhanza')->table('jns_perawatan_radiologi')
            ->leftJoin('fhir_rad', 'fhir_rad.kd_jenis_prw', '=', 'jns_perawatan_radiologi.kd_jenis_prw')
            ->select(
                'fhir_rad.*',
                'jns_perawatan_radiologi.kd_jenis_prw',
                'jns_perawatan_radiologi.nm_perawatan',
                'jns_perawatan_radiologi.kelas',
                'jns_perawatan_radiologi.status'
            )
            ->where('jns_perawatan_radiologi.kd_jenis_prw', Crypt::decrypt($id))->first();

        return response()->json($data);
    }

    public function mapingRadioUpdate(Request $request)
    {
        $data = DB::connection('mysqlkhanza')
            ->table('fhir_rad')
            ->where('kd_jenis_prw', $request->kd_jenis_prw)
            ->first();

        if ($data) {
            DB::connection('mysqlkhanza')
                ->table('fhir_rad')
                ->where('kd_jenis_prw', $request->kd_jenis_prw)
                ->update(['kd_loinc' => $request->kd_loinc]);
        } else {
            DB::connection('mysqlkhanza')
                ->table('fhir_rad')
                ->insert([
                    'kd_jenis_prw' => $request->kd_jenis_prw,
                    'kd_loinc' => $request->kd_loinc,
                ]);
        }

        if ($data) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function mapingLab(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Tool Satu Sehat');
        session()->put('cucu', 'Maping Laboratorium');

        $data = DB::connection('mysqlkhanza')->table('jns_perawatan_lab')
            ->join('penjab', 'jns_perawatan_lab.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('fhir_lab', 'fhir_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->leftJoin('fhir_master_loinc', 'fhir_lab.kd_loinc', '=', 'fhir_master_loinc.kd_loinc')
            ->leftJoin('fhir_master_specimen', 'fhir_lab.kd_snomed', '=', 'fhir_master_specimen.kd_snomed')
            ->select(
                'penjab.png_jawab',
                'penjab.nama_perusahaan',
                'fhir_lab.*',
                'fhir_master_loinc.*',
                'fhir_master_specimen.kd_snomed',
                'fhir_master_specimen.display',
                'fhir_master_specimen.coding_system',
                'jns_perawatan_lab.*'
            )
            ->get();

        $dataSpecimen = DB::connection('mysqlkhanza')->table('fhir_master_specimen')->get();
        $dataLoinc = DB::connection('mysqlkhanza')->table('fhir_master_loinc')
            ->where('code', '!=', '')
            ->get();

        // dd($dataLoinc->take(10));

        return view('satu_sehat.map_lab', compact('data', 'dataSpecimen', 'dataLoinc'));
    }

    public function mapingTemplateLab(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Tool Satu Sehat');
        session()->put('cucu', 'Maping Template Lab');

        $data = DB::connection('mysqlkhanza')->table('jns_perawatan_lab')
            ->join('template_laboratorium', 'template_laboratorium.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->join('penjab', 'jns_perawatan_lab.kd_pj', '=', 'penjab.kd_pj')
            ->leftJoin('fhir_lab_template', 'fhir_lab_template.kd_template', '=', 'template_laboratorium.id_template')
            ->leftJoin('fhir_master_loinc', 'fhir_lab_template.kd_loinc', '=', 'fhir_master_loinc.kd_loinc')
            ->leftJoin('fhir_master_specimen', 'fhir_lab_template.kd_snomed', '=', 'fhir_master_specimen.kd_snomed')
            ->select(
                'penjab.png_jawab',
                'penjab.nama_perusahaan',
                'fhir_lab_template.*',
                'fhir_master_loinc.*',
                'fhir_master_specimen.kd_snomed',
                'fhir_master_specimen.display',
                'fhir_master_specimen.coding_system',
                'jns_perawatan_lab.kelas',
                'jns_perawatan_lab.kd_pj',
                'jns_perawatan_lab.status',
                'jns_perawatan_lab.kategori',
                'template_laboratorium.*'
            )
            ->get();

        // dd($data->take(10));

        $dataSpecimen = DB::connection('mysqlkhanza')->table('fhir_master_specimen')->get();
        $dataLoinc = DB::connection('mysqlkhanza')->table('fhir_master_loinc')
            ->where('code', '!=', '')
            ->get();

        return view('satu_sehat.map_templatelab', compact('data', 'dataSpecimen', 'dataLoinc'));
    }

    public function mapingLabEdit($id)
    {
        $data = DB::connection('mysqlkhanza')->table('jns_perawatan_lab')
            ->leftJoin('fhir_lab', 'fhir_lab.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->select(
                'fhir_lab.*',
                'jns_perawatan_lab.*'
            )
            ->where('jns_perawatan_lab.kd_jenis_prw', Crypt::decrypt($id))->first();

        return response()->json($data);
    }

    public function mapingTemplateLabEdit($id)
    {
        $id = Crypt::decrypt($id);
        $data = DB::connection('mysqlkhanza')->table('template_laboratorium')
            ->join('jns_perawatan_lab', 'template_laboratorium.kd_jenis_prw', '=', 'jns_perawatan_lab.kd_jenis_prw')
            ->leftJoin('fhir_lab_template', 'fhir_lab_template.kd_template', '=', 'template_laboratorium.id_template')
            ->where('template_laboratorium.id_template', $id)->first();

        return response()->json($data);
    }

    public function mapingLabUpdate(Request $request)
    {
        $data = DB::connection('mysqlkhanza')
            ->table('fhir_lab')
            ->where('kd_jenis_prw', $request->kd_jenis_prw)
            ->first();

        if ($data) {
            DB::connection('mysqlkhanza')
                ->table('fhir_lab')
                ->where('kd_jenis_prw', $request->kd_jenis_prw)
                ->update([
                    'kd_loinc' => $request->kd_loinc,
                    'kd_snomed' => $request->kd_snomed
                ]);
        } else {
            DB::connection('mysqlkhanza')
                ->table('fhir_lab')
                ->insert([
                    'kd_jenis_prw' => $request->kd_jenis_prw,
                    'kd_loinc' => $request->kd_loinc,
                    'kd_snomed' => $request->kd_snomed,
                ]);
        }

        if ($data) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function mapingTemplateLabUpdate(Request $request)
    {
        $data = DB::connection('mysqlkhanza')
            ->table('fhir_lab_template')
            ->where('kd_template', $request->id_template)
            ->first();

        if ($data) {
            DB::connection('mysqlkhanza')
                ->table('fhir_lab_template')
                ->where('kd_template', $request->id_template)
                ->update([
                    'kd_loinc' => $request->kd_loinc,
                    'kd_snomed' => $request->kd_snomed
                ]);
        } else {
            DB::connection('mysqlkhanza')
                ->table('fhir_lab_template')
                ->insert([
                    'kd_template' => $request->id_template,
                    'kd_loinc' => $request->kd_loinc,
                    'kd_snomed' => $request->kd_snomed,
                ]);
        }

        if ($data) {
            return response()->json(['success' => true]);
        } else {
            return response()->json(['success' => false]);
        }
    }

    public function mapingObat(Request $request)
    {
        session()->put('ibu', 'Satu Sehat');
        session()->put('anak', 'Tool Satu Sehat');
        session()->put('cucu', 'Maping Obat');

        $data = DB::connection('mysqlkhanza')->table('databarang')
            ->leftJoin('fhir_farmasi', 'fhir_farmasi.kode_brng', '=', 'databarang.kode_brng')
            ->join('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->join('industrifarmasi', 'databarang.kode_industri', '=', 'industrifarmasi.kode_industri')
            ->join('kategori_barang', 'databarang.kode_kategori', '=', 'kategori_barang.kode')
            ->join('golongan_barang', 'databarang.kode_golongan', '=', 'golongan_barang.kode')
            ->leftJoin('fhir_master_medicationform', 'fhir_farmasi.kode_medication', '=', 'fhir_master_medicationform.kd_medication')
            ->leftJoin('fhir_master_ucum', 'fhir_farmasi.kode_ucum', '=', 'fhir_master_ucum.kd_ucum')
            ->leftJoin('fhir_master_ingredient', 'fhir_farmasi.kode_ingredient', '=', 'fhir_master_ingredient.kd_ingredient')
            ->leftJoin('fhir_master_route', 'fhir_farmasi.kode_route', '=', 'fhir_master_route.kd_route')
            ->select(
                'fhir_farmasi.*',
                'databarang.*',
                'kodesatuan.satuan',
                'industrifarmasi.nama_industri',
                'kategori_barang.nama as nama_kategori',
                'golongan_barang.nama as nama_golongan',
                'fhir_master_medicationform.display as medication_display',
                'fhir_master_ucum.name as ucum_name',
                'fhir_master_ingredient.display as ingredient_display',
                'fhir_master_route.display as route_display'
            )
            ->where('golongan_barang.nama', 'like', '%Obat%')
            ->get();

        $dataKfa = DB::connection('mysqlkhanza')->table('fhir_master_kfa')->get();
        // Coba ambil dari API KFA
        // $dataMedicationForm = DB::connection('mysqlkhanza')->table('fhir_master_medicationform')->get();
        // $dataUcum = DB::connection('mysqlkhanza')->table('fhir_master_ucum')->get();
        // $dataRoute = DB::connection('mysqlkhanza')->table('fhir_master_route')->get();
        $dataIngredient = DB::connection('mysqlkhanza')->table('fhir_master_ingredient')->get();


        return view('satu_sehat.map_obat', compact('data', 'dataKfa', 'dataIngredient'));
    }

    public function mapingObatEdit($id)
    {
        $data = DB::connection('mysqlkhanza')->table('databarang')
            ->leftJoin('fhir_farmasi', 'fhir_farmasi.kode_brng', '=', 'databarang.kode_brng')
            ->join('kodesatuan', 'databarang.kode_sat', '=', 'kodesatuan.kode_sat')
            ->join('industrifarmasi', 'databarang.kode_industri', '=', 'industrifarmasi.kode_industri')
            ->join('kategori_barang', 'databarang.kode_kategori', '=', 'kategori_barang.kode')
            ->join('golongan_barang', 'databarang.kode_golongan', '=', 'golongan_barang.kode')
            ->select(
                'fhir_farmasi.*',
                'databarang.*',
                'kodesatuan.satuan',
                'industrifarmasi.nama_industri',
                'kategori_barang.nama as nama_kategori',
                'golongan_barang.nama as nama_golongan'
            )
            ->where('databarang.kode_brng', Crypt::decrypt($id))->first();

        return response()->json($data);
    }

    public function mapingObatUpdate(Request $request)
    {
        $this->validate($request, [
            'kode_brng' => 'required',
            'kd_kfa' => 'required',
            'kd_ingredient' => 'required',
        ]);
        $data = DB::connection('mysqlkhanza')
            ->table('fhir_farmasi')
            ->where('kode_brng', $request->kode_brng)
            ->first();

        $idRS = env('IDRS');
        //Send data
        $access_token = SatuSehatController::getTokenSehat();
        $client = new \GuzzleHttp\Client(['base_uri' => env('URL_APIKFA')]);
        try {
            $response = $client->request('GET', "/kfa-v2/products?identifier=kfa&code=" . $request->kd_kfa, [
                'headers' => [
                    'Authorization' => "Bearer {$access_token}"
                ]
            ]);
        } catch (ClientException $e) {
            if ($e->hasResponse()) {
                $response = $e->getResponse();
                $test = json_decode($response->getBody());
                dd($test);
            }

            $message = "Gagal melakukan pencarian " . $request->kd_kfa;
        }

        $dataResponse = json_decode($response->getBody());

        // dd($dataResponse);

        if ($dataResponse && $dataResponse->search_code) {
            $dataKfa = (object)$dataResponse->result;

            $medicationForm = $dataKfa->dosage_form ?? null;
            $ucum = $dataKfa->ucum ?? null;
            $route = $dataKfa->rute_pemberian ?? null;

            $cekForm = DB::connection('mysqlkhanza')->table('fhir_master_medicationform')->where('kd_medication', $medicationForm->code)->first();
            $cekUcum = DB::connection('mysqlkhanza')->table('fhir_master_ucum')->where('kd_ucum', $ucum->cs_code)->first();
            $cekRoute = DB::connection('mysqlkhanza')->table('fhir_master_route')->where('kd_route', $route->code)->first();

            if (!$cekForm && $medicationForm) {
                DB::connection('mysqlkhanza')->table('fhir_master_medicationform')->insert([
                    'kd_medication' => $medicationForm->code,
                    'display' => $medicationForm->name,
                    'coding_system' => 'http://terminology.kemkes.go.id/CodeSystem/medication-form'
                ]);
            }
            if (!$cekUcum && $ucum) {
                DB::connection('mysqlkhanza')->table('fhir_master_ucum')->insert([
                    'kd_ucum' => $ucum->cs_code,
                    'name' => $ucum->name,
                    'coding_system' => 'http://unitsofmeasure.org',
                    'sinonim' => $ucum->sinonim ?? '-'
                ]);
            }
            if (!$cekRoute && $route) {
                DB::connection('mysqlkhanza')->table('fhir_master_route')->insert([
                    'kd_route' => $route->code,
                    'display' => $route->name,
                    'system' => 'http://www.whocc.no/atc',
                    'keterangan' => $route->keterangan ?? '-'
                ]);
            }
        } else {
            $dataKfa = null;

            return redirect()->back()->with('error', 'Data KFA tidak ditemukan');
            // return response()->json(['status' => 'failed', 'error' => 'Data KFA tidak ditemukan'], 404);
        }

        try {
            if ($data) {
                $simpanMapping = DB::connection('mysqlkhanza')
                    ->table('fhir_farmasi')
                    ->where('kode_brng', $request->kode_brng)
                    ->update([
                        'id_ihs' => $request->kd_kfa,
                        'kode_medication' => $medicationForm->code,
                        'kode_ucum' => $ucum->cs_code,
                        'kode_ingredient' => $request->kd_ingredient,
                        'kode_route' => $route->code
                    ]);
            } else {
                $simpanMapping = DB::connection('mysqlkhanza')
                    ->table('fhir_farmasi')
                    ->insert([
                        'kode_brng' => $request->kode_brng,
                        'id_ihs' => $request->kd_kfa,
                        'kode_medication' => $medicationForm->code,
                        'kode_ucum' => $ucum->cs_code,
                        'kode_ingredient' => $request->kd_ingredient,
                        'kode_route' => $route->code
                    ]);
            }
        } catch (\Exception $e) {

            dd($e->getMessage()); // 🔥 lihat error asli

        }

        // dd($simpanMapping);

        if ($data) {
            // update
            if ($simpanMapping === 0) {
                return back()->with('sukses', 'Data tidak berubah');
            } elseif ($simpanMapping === 1) {
                return back()->with('sukses', 'Mapping berhasil diperbarui');
            }
        } else {
            // insert
            if (!$simpanMapping) {
                return back()->with('error', 'Gagal insert');
            }
        }
    }
}
