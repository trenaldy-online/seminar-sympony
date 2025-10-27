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

            // MODE 2: Tampilkan Formulir Registrasi
            return view('participants.register_form', compact('selectedEvent'));
        }

        // MODE 1: Tampilkan Banner/Slider Event (sesuai permintaan terakhir)
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

    // 2. Validasi Dinamis (Custom Fields)
    if ($event->custom_fields_config && $request->has('custom_fields')) {
        $dynamicRules = [];

        foreach ($event->custom_fields_config as $field) {

            // Tentukan aturan validasi yang benar berdasarkan tipe form
            $validationRule = $field['type'];
            if ($validationRule === 'number') {
                $validationRule = 'numeric'; // Ganti ke aturan Laravel yang benar
            } elseif ($validationRule === 'text') {
                $validationRule = 'string'; // Ganti ke aturan Laravel yang benar
            }
            // 'email' sudah benar

            $dynamicRules['custom_fields.' . $field['key']] = [
                'required',
                $validationRule,
                'max:255'
            ];
        }

        // Lakukan validasi terpisah untuk custom fields
        $customValidator = Validator::make($request->all(), $dynamicRules);

        if ($customValidator->fails()) {
            return back()->withErrors($customValidator)->withInput();
        }

        $customFieldsData = $request->input('custom_fields');
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
            'qr_code_token' => $token,
            'custom_fields_data' => $customFieldsData, // SIMPAN DATA CUSTOM FIELDS
        ]);

        return redirect()->route('participant.ticket', ['token' => $token]);
    } catch (\Exception $e) {
        Log::error('Kesalahan Mass Assignment atau Database saat pendaftaran:', [
            'error' => $e->getMessage(),
            'input' => $request->all(),
        ]);
        return back()->withInput()->with('error', 'Pendaftaran gagal. Ada masalah internal.');
    }
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
}
