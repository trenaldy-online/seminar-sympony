<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\CheckinController;
use App\Http\Controllers\ParticipantController;
use App\Http\Controllers\Admin\EventController;

// Route Halaman Utama
// Route::get('/', function () {
//    return view('welcome');
// });

// --- ROUTE PESERTA SEMINAR (PUBLIK) ---
// 1. Tampilkan Formulir Pendaftaran Peserta (Contoh: /daftar)
Route::get('/', [ParticipantController::class, 'create'])->name('participant.create');

// 2. Proses dan Simpan Data Pendaftaran Peserta
Route::post('/', [ParticipantController::class, 'store'])->name('participant.store');

// 3. Tampilkan Halaman Tiket/QR Code
Route::get('/seminar/ticket/{token}', [ParticipantController::class, 'showTicket'])->name('participant.ticket');

// 4. Tampilkan Formulir Pencarian Tiket
Route::get('/ticket/retrieve', [ParticipantController::class, 'showRetrieveForm'])->name('participant.ticket.retrieve.form');

// 5. Proses Pencarian Tiket
Route::post('/ticket/retrieve', [ParticipantController::class, 'processTicketRetrieval'])->name('participant.ticket.retrieve.process');

// Route Dashboard (Membutuhkan Login)
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Route yang Membutuhkan Autentikasi Admin/Panitia
Route::middleware('auth')->group(function () {
    // Profil Admin/Panitia
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');

    // Fitur Check-in Panitia
    // Tampilkan formulir/halaman check-in
    Route::get('/dashboard/checkin', [CheckinController::class, 'index'])->name('checkin.index');
    // Proses check-in berdasarkan token QR (POST)
    Route::post('/dashboard/checkin', [CheckinController::class, 'processCheckin'])->name('checkin.process');

    // Rute untuk Validasi Pembayaran Admin
    Route::patch('/participants/{participant}/validate-payment', [EventController::class, 'validatePayment'])
    ->name('admin.participants.validate_payment');

    // --- BARU: ROUTE MANAJEMEN EVENT OLEH ADMIN ---
    // Menggunakan resource untuk generate semua rute CRUD: index, create, store, show, edit, update, destroy
    Route::resource('dashboard/events', EventController::class)
        ->names('admin.events');

    // Rute Tambahan untuk Export Excel
    Route::get('dashboard/events/{event}/export', [EventController::class, 'exportParticipants'])
         ->name('admin.events.export');
});

require __DIR__.'/auth.php';
