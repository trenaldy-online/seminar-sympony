<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ParticipantController extends Controller
{
    // 1. Tampilkan halaman daftar event / formulir pendaftaran
    public function create(Request $request)
    {
        // Ambil semua Event yang statusnya aktif
        $activeEvents = Event::where('is_active', true)->get();

        // Cek apakah ada Event ID di URL (mode tampilkan formulir)
        $selectedEventId = $request->query('event_id');

        if ($selectedEventId) {
            // Cek apakah event yang dipilih ada dan aktif
            $selectedEvent = $activeEvents->where('id', $selectedEventId)->first();

            if (!$selectedEvent) {
                // Jika event tidak ditemukan atau tidak aktif, redirect kembali ke home
                return redirect()->route('participant.create')->with('error', 'Event tidak ditemukan atau pendaftaran sudah ditutup.');
            }

            // MODE 2: Tampilkan Formulir Registrasi
            return view('participants.register_form', compact('selectedEvent'));
        }

        // MODE 1: Tampilkan Banner/Slider Event (sesuai permintaan terakhir)
        return view('participants.register', compact('activeEvents'));
    }

    // 2. Proses dan simpan data pendaftaran
    public function store(Request $request)
    {
        // Validasi event_id
        $request->validate([
            'event_id' => 'required|exists:events,id', // Event harus ada di tabel events
            'name' => 'required|string|max:255',
            // BARU: Tambahkan validasi unique email per event
            'email' => 'required|email|unique:participants,email,NULL,id,event_id,' . $request->event_id,
            'phone' => 'nullable|string|max:20',
        ]);

        // Buat token unik
        do {
            $token = Str::random(10); // Contoh: 10 karakter acak
        } while (Participant::where('qr_code_token', $token)->exists());

        // Simpan data peserta ke database
        $participant = Participant::create([
            'event_id' => $request->event_id,
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'qr_code_token' => $token, // Simpan token
        ]);

        // Redirect ke halaman tiket dengan token
        return redirect()->route('participant.ticket', ['token' => $token]);
    }

    // 3. Tampilkan halaman tiket dengan QR Code (Akses: /seminar/ticket/{token})
    public function showTicket($token)
    {
        $participant = Participant::where('qr_code_token', $token)
                                ->with('event')
                                ->firstOrFail();

        // Data yang di-encode ke QR Code adalah URL check-in panitia (POST action)
        // Panitia akan menscan ini, dan scanner akan mengirim token sebagai POST request

        // WARNING: Rute ini menunjuk ke POST route, pastikan scanner QR Anda mengirim POST request,
        // atau kita akan menggunakan GET route untuk kemudahan testing.

        // Untuk kemudahan testing awal (menggunakan token saja):
        $qrDataUrl = $token;

        // Jika Anda ingin meng-encode URL lengkap check-in (seperti yang kita butuhkan nanti di checkin controller):
        // $qrDataUrl = route('checkin.process', ['qr_token' => $token]);
        // NOTE: Karena ini POST route, kita akan menggunakan token saja dulu.

        return view('participants.ticket', compact('participant', 'qrDataUrl'));
    }
}
