<?php

namespace App\Http\Controllers;

use App\Models\AnggotaModel;
use App\Models\TutorialModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use DateTime;

class GateController extends Controller
{
    public function tutorial(Request $request, $id)
    {
        $tutorial = TutorialModel::where('id', $id)->first();
        if (!$tutorial) {
            abort(404, 'Tutorial tidak ditemukan.');
        }

        $dataUrl = base64_decode($tutorial->path_base64);

        $auth = $request->auth;
        $anggota = AnggotaModel::where('user_id', $auth->id)->first();
        if ($tutorial->akses != 'Publik' && !$anggota) {
            abort(403, 'Akses ditolak! Kamu belum pernah terdaftar sebagai anggota.');
        }

        $akses = strtolower($tutorial->akses);
        switch ($akses) {
            case 'Anggota':
                // Cek tanggal expired keanggotaan
                $expiredDate = new DateTime($anggota->tanggal_expired);
                $today = new DateTime();

                if ($expiredDate <= $today) {
                    abort(403, 'Akses ditolak! Keanggotaan kamu sudah expired pada tanggal ' . date('d F Y', strtotime($anggota->tanggal_expired)) . '.');
                }
                break;

            case 'Khusus':
                // Cek tanggal expired keanggotaan
                $expiredDate = new DateTime($anggota->tanggal_expired);
                $today = new DateTime();

                if ($expiredDate <= $today) {
                    abort(403, 'Akses ditolak! Keanggotaan kamu sudah expired pada tanggal ' . date('d F Y', strtotime($anggota->tanggal_expired)) . '.');
                }

                $dataPaket = explode(',', $anggota->paket);
                if (!in_array('Tutorial', $dataPaket)) {
                    abort(403, 'Akses ditolak! Kamu tidak memiliki akses ke tutorial khusus.');
                }
                break;
        }

        if (filter_var($dataUrl, FILTER_VALIDATE_URL)) {
            return redirect($dataUrl);
        } else {
            if (!Storage::disk('private')->exists($dataUrl)) {
                abort(404, 'File tidak ditemukan.');
            }

            // Kirim file ke browser (tanpa public URL)
            return response()->file(Storage::disk('private')->path($dataUrl));
        }
    }
}
