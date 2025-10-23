<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Participant;

class CheckinController extends Controller
{
    // 1. Tampilkan halaman check-in
    public function index()
    {
        // Ambil semua data peserta, diurutkan berdasarkan status check-in (yang belum di atas)
        $participants = Participant::orderBy('is_checked_in', 'asc')->get();

        // Menghitung statistik (opsional)
        $totalParticipants = $participants->count();
        $checkedInCount = $participants->where('is_checked_in', true)->count();
        $notCheckedInCount = $totalParticipants - $checkedInCount;

        return view('checkin.index', compact(
            'participants',
            'totalParticipants',
            'checkedInCount',
            'notCheckedInCount'
        ));
    }

    // 2. Proses Check-in
    public function processCheckin(Request $request)
    {
        // Validasi input token dari scanner
        $request->validate([
            'qr_token' => 'required|string|max:255',
        ]);

        $token = $request->input('qr_token');

        // Cari peserta
        $participant = Participant::where('qr_code_token', $token)->first();

        if (!$participant) {
            // Jika token tidak ditemukan
            return back()->with('error', 'Token peserta TIDAK VALID.');
        }

        if ($participant->is_checked_in) {
            // Jika sudah check-in
            return back()->with('warning', 'Peserta ' . $participant->name . ' sudah check-in sebelumnya.');
        }

        // Lakukan Check-in
        $participant->is_checked_in = true;
        $participant->save();

        // Berikan notifikasi sukses
        return back()->with('success', 'Check-in BERHASIL untuk: ' . $participant->name);
    }
}

