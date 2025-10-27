<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

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
            'banner_image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',

            // Validasi Custom Fields (Wajib)
            'custom_fields' => 'nullable|array',
            'custom_fields.*.name' => 'required|string|max:100|distinct', // Nama field harus unik
            'custom_fields.*.type' => 'required|in:text,number,email',     // Tipe field harus valid
        ]);

        // 2. Logika Upload Gambar Banner (jika ada)
        $bannerPath = null;
        if ($request->hasFile('banner_image')) {
            $filePath = $request->file('banner_image')->store('banners', 'public');
            $bannerPath = 'storage/' . $filePath;
        }

        // 4. Siapkan data Custom Fields untuk disimpan sebagai JSON
        $customFieldsConfig = [];
        if ($request->has('custom_fields')) {
            // Kita hanya mengambil 'name' dan 'type' untuk array final JSON
            $customFieldsConfig = collect($request->input('custom_fields'))->map(function($field) {
                // Kita juga membuat 'key' yang bersih untuk nama kolom/input form nanti
                $key = Str::slug($field['name'], '_');
                return [
                    'key' => $key,
                    'name' => $field['name'],
                    'type' => $field['type']
                ];
            })->unique('key')->values()->toArray(); // Pastikan key unik dan reset index
        }

        // 3. Buat slug dari nama event (bersihkan string untuk URL)
        $slug = Str::slug($request->name);

        // 4. Simpan Event ke database
        Event::create([
            'name' => $request->name,
            'slug' => $slug,
            'date' => $request->date,
            'description' => $request->description,
            'banner_image' => $bannerPath,
            'custom_fields_config' => $customFieldsConfig,
            'is_active' => $request->has('is_active'),
        ]);

        // 5. Redirect kembali ke halaman daftar Event dengan pesan sukses
        return redirect()->route('admin.events.index')->with('success', 'Event baru berhasil ditambahkan!');
    }

    /**
     * Tampilkan Event spesifik beserta daftar Peserta.
     */
    public function show(Event $event)
    {
        // Memuat Event dan Peserta yang diurutkan berdasarkan status check-in (yang belum check-in di atas)
        $event->load([
            'participants' => function ($query) {
                // Urutkan berdasarkan status check-in (false/0 dulu, lalu true/1)
                $query->orderBy('is_checked_in', 'asc')
                      ->orderBy('name', 'asc'); // Urutan kedua berdasarkan nama
            }
        ]);

        // Hitung statistik
        $totalParticipants = $event->participants->count();
        $checkedInCount = $event->participants->where('is_checked_in', true)->count();
        $notCheckedInCount = $totalParticipants - $checkedInCount;

        return view('admin.events.show', compact(
            'event',
            'totalParticipants',
            'checkedInCount',
            'notCheckedInCount'
        ));
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
                // Kita hanya perlu path setelah 'storage/'
            $oldFilePath = str_replace('storage/', '', $event->banner_image);
            Storage::disk('public')->delete($oldFilePath); // Hapus dari disk 'public'
        }
            // Simpan gambar baru ke DISK 'public'
            $filePath = $request->file('banner_image')->store('banners', 'public');
            // Format path untuk database
            $bannerPath = 'storage/' . $filePath;

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
