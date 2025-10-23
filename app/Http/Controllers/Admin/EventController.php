<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage; // Tambahkan ini jika Anda mengimplementasikan upload gambar

class EventController extends Controller
{
    /**
     * Tampilkan daftar semua Event.
     */
    public function index()
    {
        // Muat events beserta jumlah pesertanya
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
            // Tambahkan 'banner_image' jika Anda mengimplementasikannya
            // 'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // 2. Logika Upload Gambar Banner (jika ada)
        $bannerPath = null;
        if ($request->hasFile('banner_image')) {
            $bannerPath = $request->file('banner_image')->store('public/banners');
            $bannerPath = str_replace('public/', 'storage/', $bannerPath);
        }

        // 3. Buat slug dari nama event (bersihkan string untuk URL)
        $slug = Str::slug($request->name);

        // 4. Simpan Event ke database
        Event::create([
            'name' => $request->name,
            'slug' => $slug,
            'date' => $request->date,
            'description' => $request->description,
            'banner_image' => $bannerPath, // Jika ada
            'is_active' => $request->has('is_active'),
        ]);

        // 5. Redirect kembali ke halaman daftar Event dengan pesan sukses
        return redirect()->route('admin.events.index')->with('success', 'Event baru berhasil ditambahkan!');
    }

    // --- BARU: METHOD EDIT (Menampilkan form edit) ---
    /**
     * Tampilkan formulir untuk mengedit Event tertentu.
     */
    public function edit(Event $event)
    {
        return view('admin.events.edit', compact('event'));
    }

    // --- BARU: METHOD UPDATE (Memproses perubahan) ---
    /**
     * Update Event yang ada di database.
     */
    public function update(Request $request, Event $event)
    {
        // 1. Validasi data
        $request->validate([
            'name' => 'required|string|max:255',
            'date' => 'required|date',
            'description' => 'nullable|string',
            // Tambahkan validasi gambar jika Anda mengimplementasikannya
            // 'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        // 2. Logika Update Gambar Banner (jika ada)
        $bannerPath = $event->banner_image; // Pertahankan path lama

        if ($request->hasFile('banner_image')) {
            // Hapus gambar lama jika ada
            if ($event->banner_image) {
                Storage::delete(str_replace('storage/', 'public/', $event->banner_image));
            }
            // Simpan gambar baru
            $bannerPath = $request->file('banner_image')->store('public/banners');
            $bannerPath = str_replace('public/', 'storage/', $bannerPath);
        } else if ($request->input('remove_image')) {
            // Logika Hapus Gambar
            if ($event->banner_image) {
                Storage::delete(str_replace('storage/', 'public/', $event->banner_image));
                $bannerPath = null;
            }
        }

        // 3. Update data Event
        $event->update([
            'name' => $request->name,
            'slug' => Str::slug($request->name),
            'date' => $request->date,
            'description' => $request->description,
            'banner_image' => $bannerPath, // Update path gambar
            'is_active' => $request->has('is_active'),
        ]);

        return redirect()->route('admin.events.index')->with('success', 'Event "' . $event->name . '" berhasil diperbarui!');
    }

    // --- BARU: METHOD DESTROY (Menghapus event) ---
    /**
     * Hapus Event dari database.
     */
    public function destroy(Event $event)
    {
        $eventName = $event->name;

        // Hapus file gambar terkait (jika ada)
        if ($event->banner_image) {
             Storage::delete(str_replace('storage/', 'public/', $event->banner_image));
        }

        // Hapus Event
        $event->delete();

        return redirect()->route('admin.events.index')->with('success', 'Event "' . $eventName . '" berhasil dihapus!');
    }
}
