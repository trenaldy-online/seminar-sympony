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
            return view('participants.register_form', compact('selectedEvent', 'registeredCount'));
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
                // EMAIL HANYA DIVALIDASI FORMAT, TIDAK ADA ATURAN UNIQUE
                'email' => 'required|email',
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
                $rules = ['required']; // Semua custom field wajib (Aturan default, ubah jika ada yang opsional)

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
                    // DIUBAH: Hapus aturan 'unique:participants,nik'
                    $rules = array_merge($rules, ['digits:16']);
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

            // ISI DATA CUSTOM FIELDS SETELAH VALIDASI BERHASIL
            $customFieldsData = $request->input('custom_fields');
        }

        // Selain itu, jika NIK ada, kita ekstrak dan simpan sebagai kolom statis juga
        $nikData = $request->input('custom_fields.nik', '');

        // 3. Tentukan Status Pembayaran dan Kode Unik
        $isPaidEvent = $event->is_paid;
        $participantIsPaid = !$isPaidEvent; // Jika Gratis, TRUE. Jika Berbayar, FALSE.
        $uniqueCode = null;

        if ($isPaidEvent) {
            // Logika Kode Unik (dihitung jika berbayar)
            $existingCodes = Participant::where('event_id', $event->id)
                                        ->whereNotNull('unique_code')
                                        ->pluck('unique_code')
                                        ->toArray();

            $availableCodes = array_diff(range(1, 999), $existingCodes);

            if (!empty($availableCodes)) {
                $uniqueCode = array_rand(array_flip($availableCodes));
            } else {
                $uniqueCode = 1;
            }
        }

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
                'is_paid' => $participantIsPaid,
                'unique_code' => $uniqueCode,
                // Pastikan hanya kolom yang ada di migrasi dan fillable di Model yang ada di sini
            ]);

            // PERBAIKAN REDIRECTION
            if ($event->is_paid) {
                // Event Berbayar: Arahkan ke Halaman Pembayaran
                return redirect()->route('participant.payment.pending', ['token' => $token]);
            } else {
                // Event Gratis: Langsung ke Tiket
                return redirect()->route('participant.ticket', ['token' => $token]);
            }

        } catch (\Exception $e) {
            Log::error('Kesalahan Database saat pendaftaran:', [
                'error' => $e->getMessage(),
                'input' => $request->all(),
            ]);
            // TAMPILKAN PESAN ERROR DARI EXCEPTION AGAR MUDAH DIDEBUG
            return back()->withInput()->with('error', 'Pendaftaran gagal. Error: ' . $e->getMessage());
        }
    }

    // --- METHOD LAIN TIDAK BERUBAH ---
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

    public function showTicket($token)
    {
        $participant = Participant::where('qr_code_token', $token)
                                 ->with('event')
                                 ->firstOrFail();

        // Cek jika event berbayar dan belum lunas, alihkan ke halaman pending
        if ($participant->event->is_paid && !$participant->is_paid) {
             return redirect()->route('participant.payment.pending', ['token' => $token]);
        }

        $qrDataUrl = $token;

        return view('participants.ticket', compact('participant', 'qrDataUrl'));
    }

    public function showRetrieveForm()
    {
        $activeEvents = Event::where('is_active', true)->get();
        return view('participants.retrieve_ticket', compact('activeEvents'));
    }

    // 5. Proses pencarian tiket berdasarkan NIK dan email
    public function processTicketRetrieval(Request $request)
    {
        $request->validate([
            'nik' => 'required|string|min:16|max:16',
            'email' => 'required|email',
            'event_id' => 'required|exists:events,id',
        ]);

        // Cari peserta berdasarkan NIK, Email, dan Event ID
        $participant = Participant::where('nik', $request->nik)
                                  ->where('email', $request->email)
                                  ->where('event_id', $request->event_id)
                                  ->first();

        if (!$participant) {
            return back()->withInput()->with('error', 'Tiket tidak ditemukan. Kombinasi NIK dan Email tidak cocok untuk Event yang dipilih.');
        }

        // <<< PERBAIKAN: Cek Status Pembayaran dan Arahkan ke Halaman yang Tepat >>>

        // Load Event relationship untuk mengecek is_paid event
        // Kita menggunakan with('event') karena di query awal kita tidak meloadnya.
        $participant->load('event');

        // Jika event berbayar dan peserta belum lunas (is_paid = false)
        if ($participant->event->is_paid && !$participant->is_paid) {
            // Arahkan kembali ke halaman pending payment
            return redirect()->route('participant.payment.pending', ['token' => $participant->qr_code_token])
                             ->with('info', 'Tiket Anda ditemukan, namun Anda harus menyelesaikan pembayaran.');
        }

        // Jika sudah lunas atau event gratis, arahkan ke halaman tiket
        return redirect()->route('participant.ticket', ['token' => $participant->qr_code_token])
                         ->with('success', 'Tiket Anda berhasil ditemukan!');
        // <<< AKHIR PERBAIKAN >>>
    }
}
