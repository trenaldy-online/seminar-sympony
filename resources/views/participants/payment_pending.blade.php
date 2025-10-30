{{-- resources/views/participants/payment_pending.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Pembayaran | {{ config('app.name', 'Seminar App') }}</title>
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo_sympony_icon.png') }}">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        /* Gaya dasar dari register.blade.php */
        body { font-family: sans-serif; margin: 0; padding: 0; background-color: #f0f4f8; }
        .page-header { display: flex; justify-content: space-between; align-items: center; padding: 15px 30px; background-color: white; box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05); }
        .header-logo { font-size: 24px; font-weight: bold; color: #007bff; text-decoration: none; }
    </style>
</head>
<body>

    <div class="page-header">
        <a href="{{ url('/') }}" class="header-logo">
            <img src="{{ asset('images/logo_sympony.PNG') }}"
                 alt="Sympony Logo"
                 style="height: 30px; width: auto;">
        </a>
    </div>

    <div class="max-w-xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="mt-2 text-2xl font-bold text-gray-900">Pembayaran Tertunda</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Terima kasih <b>{{ $participant->name }}</b> telah mendaftar di acara <b>{{ $participant->event->name }}</b>. Saat ini kami menunggu pembayaran Anda.
                </p>
                <div class="mt-6 p-4 bg-yellow-50 border-l-4 border-yellow-400 text-yellow-700">
                    <p class="font-bold">Status: Menunggu Pembayaran</p>
                </div>
            </div>

            <div class="mt-8">
        <h3 class="text-lg font-semibold border-b pb-2 mb-4">Detail Pembayaran</h3>
        <div class="space-y-4 text-gray-700">

            <p>Harap transfer dana sebesar:</p>
            <div class="bg-gray-100 p-3 rounded-lg text-2xl font-extrabold text-center text-indigo-600">
                Rp {{ number_format($participant->event->price + $participant->unique_code, 0, ',', '.') }}
            </div>

            <p class="text-sm font-semibold text-red-600">
                Pastikan Anda mentransfer sesuai nominal tersebut (termasuk kode unik Rp {{ number_format($participant->unique_code, 0) }}) agar pembayaran dapat divalidasi dengan cepat.
            </p>

            <div class="border-t pt-4">
                <p class="font-semibold mb-2">Transfer ke Rekening Tujuan:</p>

                <div class="space-y-3">

                    <div class="flex">
                        <span class="w-1/3 min-w-[120px] font-semibold text-gray-600">Bank:</span>
                        <span class="w-2/3 text-gray-900 font-bold">{{ $participant->event->bank_name }}</span>
                    </div>

                    <div class="flex items-center">
                        <span class="w-1/3 min-w-[120px] font-semibold text-gray-600">No. Rekening:</span>
                        <div class="flex items-center w-2/3 space-x-2">
                            {{-- ID ini digunakan oleh JavaScript --}}
                            <strong id="account-number" class="text-gray-900 font-bold flex-grow">
                                {{ $participant->event->account_number }}
                            </strong>

                            {{-- Tombol Copy --}}
                            <button type="button" id="copy-btn"
                                class="flex items-center text-xs px-2 py-1 bg-green-500 hover:bg-green-600 text-white rounded-md transition duration-150">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 5H6a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2v-1M8 5a2 2 0 002 2h2a2 2 0 002-2M8 5a2 2 0 012-2h2a2 2 0 012 2m0 0h2a2 2 0 012 2v1M16 11H4"></path></svg>
                                Salin
                            </button>
                        </div>
                    </div>

                    <div class="flex">
                        <span class="w-1/3 min-w-[120px] font-semibold text-gray-600">Atas Nama:</span>
                        <span class="w-2/3 text-gray-900 font-bold">{{ $participant->event->account_holder }}</span>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="border-t pt-4">
        </div>

    <div class="mt-6 p-4 bg-blue-50 border-l-4 border-blue-400 text-blue-700 rounded-lg">
        <p class="font-bold mb-2 flex items-center">
            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
            Langkah Selanjutnya (Wajib):
        </p>
        <p class="text-sm">
            Mohon segera lakukan transfer dan **kirim bukti pembayaran** Anda ke kontak WhatsApp berikut:
        </p>

        <div class="mt-3 text-center">
            <a href="https://api.whatsapp.com/send/?phone=6285172266911" target="_blank"
               class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-green-500 hover:bg-green-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path d="M20 10c0-5.523-4.477-10-10-10S0 4.477 0 10c0 4.84 3.44 8.87 8 9.8V15h-3V9h3V7c0-3.31 1.69-5 4-5 1.48 0 2.75.14 3.13.2L17 4V6h-2c-1.657 0-2 .877-2 2v2h4l-1 4h-3v5.8c4.56-0.93 8-4.96 8-9.8z" fill="none"></path><path fill-rule="evenodd" d="M18 10c0 4.418-3.582 8-8 8s-8-3.582-8-8 3.582-8 8-8 8 3.582 8 8zm-4.707 3.293a.999.999 0 01-1.414 0l-2-2a.999.999 0 011.414-1.414l1.293 1.293 2.293-2.293a.999.999 0 111.414 1.414l-3 3z" clip-rule="evenodd" fill="#FFF"></path><path d="M12 10.424l-1.293-1.293a.999.999 0 00-1.414 1.414l2 2a.999.999 0 001.414 0l3-3a.999.999 0 10-1.414-1.414l-2.293 2.293z" fill="#FFF"></path><path d="M14.707 8.707a.999.999 0 00-1.414 0l-2.293 2.293-1.293-1.293a.999.999 0 10-1.414 1.414l2 2a.999.999 0 001.414 0l3-3a.999.999 0 000-1.414z" fill="#FFF"></path></svg>
                0851-7226-6911 (Klik untuk Chat WhatsApp)
            </a>
        </div>
    </div>

            <div class="mt-8 text-center">
                <p class="text-sm text-gray-500">Setelah pembayaran dilakukan, Admin akan memvalidasi data Anda. Anda dapat mencoba mengakses tiket Anda (<a href="{{ route('participant.ticket', ['token' => $participant->qr_code_token]) }}" class="text-indigo-600 hover:underline">klik di sini</a>) secara berkala.</p>
            </div>
        </div>
    </div>
</body>
</html>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const copyBtn = document.getElementById('copy-btn');
    const accountNumberElement = document.getElementById('account-number');

    if (copyBtn && accountNumberElement) {
        // Ambil hanya teks nomor rekening (tanpa spasi ekstra jika ada)
        const accountNumber = accountNumberElement.textContent.trim();

        copyBtn.addEventListener('click', async function() {
            try {
                // Gunakan Clipboard API modern
                await navigator.clipboard.writeText(accountNumber);

                // Beri feedback visual
                const originalText = copyBtn.innerHTML;
                copyBtn.innerHTML = '✅ Berhasil Disalin!';
                copyBtn.classList.remove('bg-green-500', 'hover:bg-green-600');
                copyBtn.classList.add('bg-blue-500');

                // Kembalikan ke tampilan semula setelah 2 detik
                setTimeout(() => {
                    copyBtn.innerHTML = originalText;
                    copyBtn.classList.remove('bg-blue-500');
                    copyBtn.classList.add('bg-green-500', 'hover:bg-green-600');
                }, 2000);

            } catch (err) {
                console.error('Gagal menyalin:', err);
                alert('Gagal menyalin nomor rekening. Silakan salin manual: ' + accountNumber);
            }
        });
    }
});
</script>
