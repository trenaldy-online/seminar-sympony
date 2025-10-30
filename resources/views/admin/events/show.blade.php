{{-- resources/views/admin/events/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Event: ' . $event->name) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Tombol Kembali --}}
                    <a href="{{ route('admin.events.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 mb-6 inline-block">
                        &larr; Kembali ke Daftar Event
                    </a>

                    <h3 class="text-2xl font-bold mb-4">{{ $event->name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Tanggal: {{ \Carbon\Carbon::parse($event->date)->isoFormat('dddd, D MMMM Y') }} |
                        Status: {{ $event->is_active ? 'Aktif' : 'Non-Aktif' }}
                    </p>

                    {{-- Statistik Peserta --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 text-center">
                        <div class="p-4 bg-blue-100 dark:bg-blue-900/50 rounded-lg shadow">
                            <p class="text-sm text-blue-800 dark:text-blue-200">Total Peserta</p>
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $totalParticipants }}</p>
                        </div>
                        <div class="p-4 bg-green-100 dark:bg-green-900/50 rounded-lg shadow">
                            <p class="text-sm text-green-800 dark:text-green-200">Sudah Check-in</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $checkedInCount }}</p>
                        </div>
                        <div class="p-4 bg-red-100 dark:bg-red-900/50 rounded-lg shadow">
                            <p class="text-sm text-red-800 dark:text-red-200">Belum Check-in</p>
                            <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $notCheckedInCount }}</p>
                        </div>
                    </div>
                    {{-- Tombol Download Excel --}}
                    <div class="mb-8 flex justify-end">
                        <a href="{{ route('admin.events.export', $event) }}"
                        class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                            Download Data Peserta (XLSX)
                        </a>
                    </div>

                    <h4 class="text-xl font-semibold mt-10 mb-4 border-b pb-2">Daftar Peserta (Total: {{ $totalParticipants }})</h4>

                    {{-- FORM PENCARIAN BARU --}}
                        <form method="GET" action="{{ route('admin.events.show', $event) }}" class="flex items-center space-x-2">
                            <input type="text" name="q" placeholder="Cari Nama, Email, NIK, atau Kode Bayar..."
                                   value="{{ $searchQuery ?? '' }}"
                                   class="border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm text-sm p-2 w-72">
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-500 transition duration-150 text-sm">
                                Cari
                            </button>
                            @if ($searchQuery)
                                <a href="{{ route('admin.events.show', $event) }}" class="text-sm text-red-600 hover:text-red-800">
                                    Reset
                                </a>
                            @endif
                        </form>
                    {{-- AKHIR FORM PENCARIAN --}}

                    {{-- Tabel Peserta --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-3 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">No.</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Peserta</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status Check-in</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status Pembayaran</th>
                                    <th class="px-4 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Validasi Pembayaran</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Token QR</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($event->participants as $participant)
                                    @php
                                    // Tambahkan nomor urut
                                    $loopIndex = $loop->iteration;
                                @endphp
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-400">
                                        {{ $loopIndex }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $participant->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $participant->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        @if ($participant->is_checked_in)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                                Sudah Check-in
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                                Belum Check-in
                                            </span>
                                        @endif
                                    </td>
                                    <td class="border px-4 py-2 text-center">
                                        @if ($participant->event->is_paid)
                                            @if ($participant->is_paid)
                                                <span class="text-sm font-semibold text-green-600">LUNAS</span>
                                            @else
                                                <span class="text-sm font-semibold text-red-600">
                                                    BELUM BAYAR (Kode: {{ $participant->unique_code }})
                                                </span>
                                            @endif
                                        @else
                                            <span class="text-sm text-gray-500">Gratis (Lunas)</span>
                                        @endif
                                    </td>
                                    {{-- Kolom Aksi --}}
                                    <td class="border px-4 py-2 text-center">
                                        @if ($participant->event->is_paid && !$participant->is_paid)
                                            {{-- Tombol Validasi Pembayaran --}}
                                            <form method="POST" action="{{ route('admin.participants.validate_payment', $participant) }}" style="display:inline;" onsubmit="return confirm('Yakin ingin memvalidasi pembayaran {{ $participant->name }}?');">
                                                @csrf
                                                @method('PATCH')
                                                <button type="submit" class="text-xs bg-green-500 hover:bg-green-700 text-white font-bold py-1 px-2 rounded">
                                                    Validasi Bayar
                                                </button>
                                            </form>
                                        @endif
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-mono text-gray-500 dark:text-gray-400">
                                        {{ $participant->qr_code_token }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                            {{-- Tempat placeholder QR SVG (sembunyikan) --}}
                                            <div data-qr-token="{{ $participant->qr_code_token }}"
                                                data-name="{{ Str::slug($participant->name) }}"
                                                class="qr-code-placeholder hidden">
                                            </div>

                                            {{-- Tombol Download PNG via JavaScript --}}
                                            <button type="button"
                                                data-token="{{ $participant->qr_code_token }}"
                                                class="download-png-admin-btn inline-flex items-center px-3 py-1 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 transition ease-in-out duration-150">
                                                <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                                PNG
                                            </button>
                                        </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        Belum ada peserta terdaftar untuk event ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
    // Ambil token CSRF dari meta tag
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Fungsi utama yang sama dengan yang ada di halaman tiket
    async function downloadQrAsPng(token, participantName) {
        // 1. Dapatkan string SVG dari server (menggunakan AJAX)
        const fetchUrl = `{{ url('/dashboard/participants/qr/download') }}/${token}`;

        try {
            const response = await fetch(fetchUrl, {
                method: 'GET',
                headers: {
                    'X-CSRF-TOKEN': csrfToken,
                    'Accept': 'image/svg+xml' // Minta format SVG
                }
            });

            if (!response.ok) {
                throw new Error('Gagal mengambil data QR Code dari server.');
            }

            const svgString = await response.text();

            // 2. Konversi SVG String ke PNG menggunakan Canvas di sisi klien

            const svgBlob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
            const url = URL.createObjectURL(svgBlob);
            const img = new Image();

            img.onload = function() {
                // Ukuran target PNG (sesuaikan dengan kebutuhan cetak)
                const qrSize = 1080;
                // Hitung padding (misal 10% dari ukuran QR)
                const padding = qrSize * 0.10;
                const totalSize = qrSize + (padding * 2);

                const canvas = document.createElement('canvas');
                canvas.width = totalSize;
                canvas.height = totalSize;
                const ctx = canvas.getContext('2d');

                // Latar belakang putih
                ctx.fillStyle = '#ffffff';
                ctx.fillRect(0, 0, totalSize, totalSize);

                // Gambar QR di tengah
                ctx.drawImage(img, padding, padding, qrSize, qrSize);

                URL.revokeObjectURL(url);
                canvas.toBlob(function(blob) {
                    const a = document.createElement('a');
                    const fileName = `${participantName}_${token}_QR.png`;
                    a.download = fileName;
                    a.href = URL.createObjectURL(blob);
                    document.body.appendChild(a);
                    a.click();
                    a.remove();
                    URL.revokeObjectURL(a.href);
                }, 'image/png');
            };

            img.onerror = function() {
                URL.revokeObjectURL(url);
                alert('Gagal memuat atau mengonversi SVG. Pastikan QR Code valid.');
            };

            img.src = url;

        } catch (error) {
            console.error(error);
            alert('Aksi gagal: ' + error.message);
        }
    }

    // Event Listener untuk semua tombol download
    document.addEventListener('DOMContentLoaded', function() {
        document.querySelectorAll('.download-png-admin-btn').forEach(button => {
            button.addEventListener('click', function() {
                const token = this.getAttribute('data-token');
                // Asumsi nama peserta bisa diambil dari baris tabel (Ini membutuhkan penyesuaian jika kolom nama peserta tidak tersedia di tombol)
                // Kita akan gunakan data-name dari placeholder yang sudah ditambahkan
                const nameSlug = this.closest('tr').querySelector('.qr-code-placeholder').getAttribute('data-name');

                // Ganti teks tombol menjadi 'Downloading...'
                const originalText = button.innerHTML;
                button.innerHTML = '<span class="animate-spin mr-1">🔄</span> PNG';
                button.disabled = true;

                downloadQrAsPng(token, nameSlug)
                    .finally(() => {
                        // Kembalikan teks dan status tombol setelah selesai
                        button.innerHTML = originalText;
                        button.disabled = false;
                    });
            });
        });
    });
</script>

</x-app-layout>
