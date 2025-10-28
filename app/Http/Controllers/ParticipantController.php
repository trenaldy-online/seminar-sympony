<?php

namespace App\Http\Controllers;

use App\Models\Participant;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

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

            // HITUNG JUMLAH PESERTA
            $registeredCount = $selectedEvent->participants()->count();

            // Cek jika kuota tidak null (berarti ada batasan) dan sudah penuh
            if ($selectedEvent->max_capacity !== null && $registeredCount >= $selectedEvent->max_capacity) {
                return redirect()->route('participant.create')
                                 ->with('error', 'Mohon maaf, kuota untuk Event "' . $selectedEvent->name . '" sudah penuh.');
            }

            // MODE 2: Tampilkan Formulir Registrasi
            // PASTIKAN registeredCount DIKIRIMKAN KE VIEW DI BARIS INI
            return view('participants.register_form', compact('selectedEvent', 'registeredCount')); // <<< PERBAIKAN PENTING DI SINI
        }

        // MODE 1: Tampilkan Banner/Slider Event
        // Muat data kuota ke collection untuk ditampilkan di slider
        $activeEvents = $activeEvents->map(function ($event) {
            $registeredCount = $event->participants()->count();
            $event->registeredCount = $registeredCount;
            $event->isFull = $event->max_capacity !== null && $registeredCount >= $event->max_capacity;
            return $event;
        });

        return view('participants.register', compact('activeEvents'));
    }

    // 2. Proses dan simpan data pendaftaran
    public function store(Request $request)
    {
            // 1. Validasi Statis (Form Bawaan)
            $staticValidation = [
                'event_id' => 'required|exists:events,id',
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:participants,email,NULL,id,event_id,' . $request->event_id,
                'phone' => 'nullable|string|max:20',
                'custom_fields' => 'nullable|array',
            ];

            $request->validate($staticValidation);

            // Ambil Event untuk mendapatkan konfigurasi custom field
            $event = Event::findOrFail($request->event_id);
            $customFieldsData = [];

            $registeredCount = $event->participants()->count();
            if ($event->max_capacity !== null && $registeredCount >= $event->max_capacity) {
                return redirect()->route('participant.create', ['event_id' => $event->id])
                                 ->with('error', 'Pendaftaran gagal. Kuota Event sudah penuh.');
            }

        // 2. Validasi Dinamis (Custom Fields)
        if ($event->custom_fields_config && $request->has('custom_fields')) {
            $dynamicRules = [];

            foreach ($event->custom_fields_config as $field) {
                $key = $field['key']; // Ambil key (misal: 'nik')
                $validationRule = $field['type'];
                $rules = ['required']; // Semua custom field wajib

                // --- MAPPING TIPE DATA FORM KE ATURAN VALIDASI LARAVEL ---
                if ($validationRule === 'number') {
                    $rules[] = 'numeric';
                } elseif ($validationRule === 'text') {
                    $rules[] = 'string';
                } else {
                    $rules[] = $validationRule; // (misal: 'email')
                }

                // --- PERBAIKAN: ATURAN KHUSUS UNTUK NIK ---
                if (str_contains($key, 'nik')) { // Cek jika key mengandung 'nik'
                    $rules = array_merge($rules, ['digits:16', 'unique:participants,nik']); // Tambahkan validasi 16 digit dan unique
                } else {
                    $rules[] = 'max:255'; // Aturan max 255 untuk field lain
                }

                $dynamicRules['custom_fields.' . $key] = $rules;
            }

            // Lakukan validasi terpisah untuk custom fields
            $customValidator = Validator::make($request->all(), $dynamicRules);

            if ($customValidator->fails()) {
                return back()->withErrors($customValidator)->withInput();
            }
        }

        // Selain itu, jika NIK ada, kita ekstrak dan simpan sebagai kolom statis juga
        $nikData = $request->input('custom_fields.nik', null);

        // 3. Tentukan Status Pembayaran dan Kode Unik
        $isPaidEvent = $event->is_paid;
        $participantIsPaid = !$isPaidEvent; // Jika Gratis, langsung TRUE (Lunas). Jika Berbayar, FALSE.
        $uniqueCode = null;

        if ($isPaidEvent) {
            // Jika berbayar, hitung kode unik (001 - 999)
            // Ambil kode unik tertinggi saat ini untuk event ini
            $lastParticipant = Participant::where('event_id', $event->id)
                                          ->orderByDesc('id')
                                          ->first();

            // Kode unik akan menjadi (ID Peserta saat ini + 1) MODULO 1000.
            // Untuk lebih aman, kita ambil angka acak dari 1 sampai 999 yang belum digunakan.
            $existingCodes = Participant::where('event_id', $event->id)
                                        ->whereNotNull('unique_code')
                                        ->pluck('unique_code')
                                        ->toArray();

            $availableCodes = array_diff(range(1, 999), $existingCodes);

            if (!empty($availableCodes)) {
                $uniqueCode = array_rand(array_flip($availableCodes));
            } else {
                // Jika semua kode 1-999 sudah habis, ulangi dari 1 (risiko kecil sekali)
                $uniqueCode = 1;
            }

        }
        // Jika event gratis, unique_code tetap null dan is_paid tetap true.

        // 3. Buat token unik
        do {
            $token = Str::random(10);
        } while (Participant::where('qr_code_token', $token)->exists());

        // 4. Simpan data peserta ke database
        try {
            $participant = Participant::create([
                'event_id' => $request->event_id,
                'name' => $request->name,
                'email' => $request->email,
                'phone' => $request->phone,
                'nik' => $nikData,
                'qr_code_token' => $token,
                'custom_fields_data' => $customFieldsData,
                'is_paid' => $participantIsPaid, // <<< BARU
                'unique_code' => $uniqueCode,     // <<< BARU
            ]);

            if ($participantIsPaid) {
                // Event Gratis: Langsung ke Tiket
                return redirect()->route('participant.ticket', ['token' => $token]);
            } else {
                // Event Berbayar: Arahkan ke Halaman Pembayaran
                return redirect()->route('participant.payment.pending', ['token' => $token]);
            }

            return redirect()->route('participant.ticket', ['token' => $token]);
        } catch (\Exception $e) {
            Log::error('Kesalahan Mass Assignment atau Database saat pendaftaran:', [
                'error' => $e->getMessage(),
                'input' => $request->all(),
            ]);
            return back()->withInput()->with('error', 'Pendaftaran gagal. Ada masalah internal.');
        }
    }

    // --- BARU: METHOD UNTUK HALAMAN MENUNGGU PEMBAYARAN ---
    public function showPaymentPending($token)
    {
        $participant = Participant::where('qr_code_token', $token)
                                 ->with('event')
                                 ->firstOrFail();

        // Jika event ternyata gratis, atau sudah lunas, langsung arahkan ke tiket
        if (!$participant->event->is_paid || $participant->is_paid) {
            return redirect()->route('participant.ticket', ['token' => $token]);
        }

        return view('participants.payment_pending', compact('participant'));
    }

    // 3. Tampilkan halaman tiket dengan QR Code (Akses: /seminar/ticket/{token})
    public function showTicket($token)
    {
        $participant = Participant::where('qr_code_token', $token)
                                 ->with('event')
                                 ->firstOrFail();

        $qrDataUrl = $token;

        return view('participants.ticket', compact('participant', 'qrDataUrl'));
    }

// 4. Tampilkan formulir untuk mencari tiket
    public function showRetrieveForm()
    {
        // Ambil daftar event aktif untuk dropdown
        $activeEvents = Event::where('is_active', true)->get();

        return view('participants.retrieve_ticket', compact('activeEvents'));
    }

    // 5. Proses pencarian tiket berdasarkan NIK dan email
    public function processTicketRetrieval(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|min:16|max:16', // Validasi format NIK 16 digit
            'email' => 'required|email',
            'event_id' => 'required|exists:events,id', // Tetap gunakan Event ID untuk scoping
        ]);

        // Cari peserta berdasarkan NIK, Email, dan Event ID
        $participant = Participant::where('nik', $request->nik)
                                  ->where('email', $request->email)
                                  ->where('event_id', $request->event_id)
                                  ->first();

        if (!$participant) {
            return back()->withInput()->with('error', 'Tiket tidak ditemukan. Kombinasi NIK dan Email tidak cocok untuk Event yang dipilih.');
        }

        // Jika ditemukan, redirect ke halaman tiket menggunakan token
        return redirect()->route('participant.ticket', ['token' => $participant->qr_code_token])
                         ->with('success', 'Tiket Anda berhasil ditemukan!');
    }
}
