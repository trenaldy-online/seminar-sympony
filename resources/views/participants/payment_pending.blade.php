{{-- resources/views/participants/payment_pending.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menunggu Pembayaran | {{ config('app.name', 'Seminar App') }}</title>
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
        <a href="{{ url('/') }}" class="header-logo">{{ config('app.name', 'Seminar App') }}</a>
    </div>

    <div class="max-w-xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-8">
            <div class="text-center">
                <svg class="mx-auto h-12 w-12 text-yellow-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                <h2 class="mt-2 text-2xl font-bold text-gray-900">Pembayaran Tertunda</h2>
                <p class="mt-1 text-sm text-gray-500">
                    Terima kasih telah mendaftar, **{{ $participant->name }}**! Pendaftaran Anda untuk Event **"{{ $participant->event->name }}"** telah kami terima.
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
                        Pastikan Anda mentransfer sesuai nominal tersebut (termasuk kode unik **Rp {{ number_format($participant->unique_code, 0) }}**) agar pembayaran dapat divalidasi dengan cepat.
                    </p>

                    <div class="border-t pt-4">
                        <p class="font-semibold">Transfer ke Rekening Tujuan:</p>
                        <div class="mt-1 space-y-1">
                            <p>Bank: <strong class="text-gray-900">{{ $participant->event->bank_name }}</strong></p>
                            <p>Nomor Rekening: <strong class="text-gray-900">{{ $participant->event->account_number }}</strong></p>
                            <p>Atas Nama: <strong class="text-gray-900">{{ $participant->event->account_holder }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="mt-8 text-center">
                <p class="text-sm text-gray-500">Setelah pembayaran dilakukan, Admin akan memvalidasi data Anda. Anda dapat mencoba mengakses tiket Anda (<a href="{{ route('participant.ticket', ['token' => $participant->qr_code_token]) }}" class="text-indigo-600 hover:underline">klik di sini</a>) secara berkala.</p>
            </div>
        </div>
    </div>
</body>
</html>
