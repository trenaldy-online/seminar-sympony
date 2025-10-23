<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event; // Import Model Event
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon; // Opsional, untuk manajemen tanggal/waktu

class EventController extends Controller
{
    /**
     * Tampilkan daftar semua Event.
     */
    public function index()
    {
        // Muat events beserta jumlah pesertanya (menggunakan withCount)
        $events = Event::withCount('participants')->orderBy('date', 'desc')->get();

        // Arahkan ke view daftar event
        return view('admin.events.index', compact('events'));
    }

    /**
     * Tampilkan formulir untuk membuat Event baru.
     */
    public function create()
    {
        return view('admin.events.create');
    }

    /**
     * Menyimpan Event baru ke database.
     */
    public function store(Request $request)
    {
        // 1. Validasi data masukan dari formulir
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
        ]);

        // 2. Buat slug dari nama event (bersihkan string untuk URL)
        $slug = Str::slug($request->name);

        // 3. Simpan Event ke database
        Event::create([
            'name' => $request->name,
            'slug' => $slug,
            'date' => $request->date,
            'description' => $request->description,
            // Cek apakah checkbox 'is_active' dicentang
            'is_active' => $request->has('is_active'),
        ]);

        // 4. Redirect kembali ke halaman daftar Event dengan pesan sukses
        return redirect()->route('admin.events.index')->with('success', 'Event baru berhasil ditambahkan!');
    }
}
