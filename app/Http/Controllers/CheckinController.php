<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log; // Diperlukan untuk logging error

class CheckinController extends Controller
{
    /**
     * Menampilkan halaman scanner/checkin.
     */
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

    /**
     * Memproses QR token dan melakukan check-in. (Handles both AJAX and Form Submit)
     */
    public function processCheckin(Request $request)
    {
        // 1. Validasi Token
        // Ganti max length ke 20 untuk keamanan/standar, 255 terlalu panjang
        $request->validate([
            'qr_token' => 'required|string|max:20',
        ]);

        $token = $request->input('qr_token');

        // 2. Cari Peserta dan Muat Relasi Event
        $participant = Participant::where('qr_code_token', $token)
                                  ->with('event')
                                  ->first();

        // Data dasar untuk response JSON/Redirect
        $responseData = [
            'success' => false,
            'status' => 'error',
            'message' => 'Token QR tidak valid atau Peserta tidak ditemukan.',
            'participant_name' => 'N/A',
            'event_name' => 'N/A',
        ];

        if (!$participant) {
            // Jika token tidak valid
            if ($request->expectsJson()) {
                return response()->json($responseData, 404);
            }
            return back()->with('error', $responseData['message']);
        }

        // Update responseData
        $responseData['participant_name'] = $participant->name;
        $responseData['event_name'] = $participant->event->name ?? 'N/A';
        $responseData['message'] = "Check-in Berhasil! Selamat datang.";

        // 3. Periksa Status Check-in
        if ($participant->is_checked_in) {
            $responseData['status'] = 'already_checked_in';
            // Sesuaikan pesan untuk feedback yang lebih baik
            $responseData['message'] = "GAGAL! " . $participant->name . " sudah CHECK-IN pada " . $participant->updated_at->format('H:i:s, d M Y') . " untuk Event " . $responseData['event_name'];

            if ($request->expectsJson()) {
                return response()->json($responseData, 409); // 409 Conflict
            }
            // Gunakan 'warning' untuk redirect agar pesan berbeda
            return back()->with('warning', $responseData['message']);
        }

        // 4. Lakukan Check-in
        try {
            $participant->is_checked_in = true;
            $participant->save();

            $responseData['success'] = true;
            $responseData['status'] = 'success';

            if ($request->expectsJson()) {
                // Berhasil untuk Scanner (mengembalikan 200 OK)
                return response()->json($responseData, 200);
            }
            // Berhasil untuk Input Manual (menggunakan redirect)
            return back()->with('success', "Check-in BERHASIL untuk: " . $participant->name . " Event: " . $responseData['event_name']);

        } catch (\Exception $e) {
            Log::error('Gagal menyimpan status check-in: ' . $e->getMessage());
            $responseData['message'] = 'Terjadi kesalahan sistem saat menyimpan status check-in.';

            if ($request->expectsJson()) {
                return response()->json($responseData, 500);
            }
            return back()->with('error', $responseData['message']);
        }
    }
}
