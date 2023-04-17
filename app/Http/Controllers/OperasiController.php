<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

class OperasiController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        session()->put('ibu', 'Operasi');
        session()->put('anak', 'Booking Operasi');
        session()->forget('cucu');

        if (isset($request->tanggalMulai)) {
            $tanggalMulai = $request->tanggalMulai;
            $tanggalSelesai = $request->tanggalSelesai;
        } else {
            $tanggalMulai = Carbon::now()->format('Y-m-d');
            $tanggalSelesai = Carbon::now()->format('Y-m-d');
        }

        $data = DB::connection('mysqlkhanza')->table('booking_jadwal_operasi')
            ->join('paket_operasi', 'paket_operasi.kode_paket', '=', 'booking_jadwal_operasi.kode_paket')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'booking_jadwal_operasi.no_rawat')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'pasien.nm_pasien',
                'paket_operasi.kode_paket',
                'paket_operasi.nm_perawatan',
                'paket_operasi.kelas',
                'paket_operasi.operator1 as biaya',
                'booking_jadwal_operasi.id_booking',
                'booking_jadwal_operasi.tanggal',
                'booking_jadwal_operasi.status'
            )
            ->get();

        // dd($data);

        return view('operasi.booking', compact('data'));
    }

    public function booking($id)
    {
        $id = Crypt::decrypt($id);


        $data = DB::connection('mysqlkhanza')->table('booking_jadwal_operasi')
            ->join('paket_operasi', 'paket_operasi.kode_paket', '=', 'booking_jadwal_operasi.kode_paket')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'booking_jadwal_operasi.no_rawat')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.alamat',
                'paket_operasi.kode_paket',
                'paket_operasi.nm_perawatan',
                'paket_operasi.kelas',
                'paket_operasi.operator1 as biaya',
                'booking_jadwal_operasi.id_booking',
                'booking_jadwal_operasi.tanggal',
                'booking_jadwal_operasi.status'
            )
            ->where('booking_jadwal_operasi.id_booking', '=', $id)
            ->first();

        // dd($id, $data);

        $dokter = DB::connection('mysqlkhanza')->table('dokter')
            ->select('dokter.kd_dokter', 'dokter.nm_dokter')
            ->get();

        $ruang = DB::connection('mysqlkhanza')->table('ruang_ok')
            ->select('ruang_ok.kd_ruang_ok', 'ruang_ok.nm_ruang_ok')
            ->get();

        // dd($dokter);

        return view('operasi.add_booking', compact('data', 'dokter', 'ruang'));
    }

    public function simpan(Request $request)
    {
        $this->validate($request, [
            'no_rawat' => 'required',
            'kode_paket' => 'required',
            'tanggal' => 'required',
            'jam_mulai' => 'required',
            'jam_selesai' => 'required',
            'status' => 'required',
            'operator' => 'required',
            'ruang_ok' => 'required',
            'id_booking' => 'required',
        ]);

        // dd($request);



        if ($request->status != 'Batal') {
            $updateBooking = DB::connection('mysqlkhanza')->table('booking_jadwal_operasi')
                ->where('id_booking', $request->id_booking)
                ->update(['status' => 'Booking']);

            $simpan = DB::connection('mysqlkhanza')->table('booking_operasi')
                ->insert([
                    'no_rawat' => $request->no_rawat,
                    'kode_paket' => $request->kode_paket,
                    'tanggal' => $request->tanggal,
                    'jam_mulai' => $request->jam_mulai,
                    'jam_selesai' => $request->jam_selesai,
                    'status' => $request->status,
                    'kd_dokter' => $request->operator,
                    'kd_ruang_ok' => $request->ruang_ok,
                ]);

            Session::flash('success', 'Data berhasil disimpan!');
        } else {
            $updateBooking = DB::connection('mysqlkhanza')->table('booking_jadwal_operasi')
                ->where('id_booking', $request->id_booking)
                ->update(['status' => 'Batal']);
        }



        return redirect('/operasi/booking');
    }

    public function jadwal(Request $request)
    {
        session()->put('ibu', 'Operasi');
        session()->put('anak', 'Jadwal Operasi');
        session()->forget('cucu');

        if (empty($request->tanggalSelesai)) {
            $tanggalMulai = Carbon::now()->yesterday()->format('Y-m-d');
            $tanggalSelesai = Carbon::now()->format('Y-m-d');
        } else {
            $tanggalMulai = $request->tanggalMulai;
            $tanggalSelesai = $request->tanggalSelesai;
        }

        // dd($now);
        $data = DB::connection('mysqlkhanza')->table('booking_operasi')
            ->join('paket_operasi', 'paket_operasi.kode_paket', '=', 'booking_operasi.kode_paket')
            ->join('reg_periksa', 'reg_periksa.no_rawat', '=', 'booking_operasi.no_rawat')
            ->join('dokter', 'dokter.kd_dokter', '=', 'booking_operasi.kd_dokter')
            ->join('ruang_ok', 'ruang_ok.kd_ruang_ok', '=', 'booking_operasi.kd_ruang_ok')
            ->leftJoin('pasien', 'pasien.no_rkm_medis', '=', 'reg_periksa.no_rkm_medis')
            ->select(
                'reg_periksa.no_rkm_medis',
                'reg_periksa.no_rawat',
                'pasien.nm_pasien',
                'pasien.jk',
                'pasien.tgl_lahir',
                'pasien.alamat',
                'paket_operasi.kode_paket',
                'paket_operasi.nm_perawatan',
                'paket_operasi.kelas',
                'paket_operasi.operator1 as biaya',
                'dokter.nm_dokter',
                'ruang_ok.kd_ruang_ok',
                'ruang_ok.nm_ruang_ok',
                'booking_operasi.jam_mulai',
                'booking_operasi.jam_selesai',
                'booking_operasi.tanggal',
                'booking_operasi.status'
            )
            ->where('booking_operasi.tanggal', '>=', $tanggalMulai)
            ->where('booking_operasi.tanggal', '<=', $tanggalSelesai)
            ->get();

        // dd($data);

        return view('operasi.jadwal', compact('data'));
    }
}
