<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo_sympony_icon.png') }}">
    <title>Daftar: {{ $selectedEvent->name }}</title>
    <style>
        body {
            font-family: sans-serif;
            margin: 0;
            padding: 20px; /* Tambahkan padding di body agar tidak menempel ke tepi layar */
            background-color: #eef2f5;
            display: flex;
            justify-content: center;
            align-items: flex-start; /* Ubah ke flex-start agar konten tidak selalu di tengah secara vertikal */
            min-height: 100vh;
            box-sizing: border-box; /* Sangat penting: Pastikan padding tidak menambah lebar */
        }
        .form-container {
            width: 100%;
            max-width: 500px;
            /* Perbaikan Kritis: Sesuaikan padding horizontal untuk mobile */
            padding: 30px 20px;
            background-color: #fcfcfc;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        /* Tambahkan CSS untuk input dan tombol sesuai style sebelumnya */
        .input-style {
            width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; margin-top: 5px;
        }
        .label-style {
            display: block; margin-bottom: 5px; font-weight: bold; color: #555;
        }
        .button-style {
            width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; transition: background-color 0.3s;
        }
        .error-style {
            background-color: #fdd; color: #a00; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #f00;
        }

        /* --- Perbaikan Spesifik Mobile --- */
        @media (max-width: 600px) {
            body {
                padding: 10px; /* Kurangi padding body di layar sangat kecil */
                align-items: flex-start;
            }
            .form-container {
                padding: 20px 15px; /* Kurangi padding container agar tidak terpotong */
                margin-top: 0; /* Pastikan tidak ada margin atas yang tidak perlu */
            }
            .form-container h1 {
                font-size: 1.5em; /* Kecilkan judul */
            }
        }
    </style>
</head>
<body>
    <div class="form-container">
        <a href="{{ route('participant.create') }}" style="display: block; margin-bottom: 20px; font-size: 14px; color: #007bff;">
            &larr; Kembali ke Daftar Event
        </a>

        <h1 style="color: #333; text-align: center; margin-bottom: 25px;">Pendaftaran Event: <br><strong style="color: #007bff;">{{ $selectedEvent->name }}</strong></h1>

@if ($selectedEvent->max_capacity !== null)
            @php
                // Variabel ini dijamin ada karena sudah dikirim dari ParticipantController::create() yang diperbarui
                $remainingCapacity = $selectedEvent->max_capacity - $registeredCount;
                $bgColor = $remainingCapacity <= 10 ? '#fdd' : '#d4edda'; // Merah Muda untuk menipis, Hijau Muda untuk aman
                $textColor = $remainingCapacity <= 10 ? '#a00' : '#155724'; // Merah Tua / Hijau Tua
                $message = $remainingCapacity > 0
                            ? "Slot Tersisa: " . $remainingCapacity . " dari " . $selectedEvent->max_capacity
                            : "Maaf, Kuota Event Sudah Penuh!";
            @endphp

            <div style="margin-bottom: 20px; padding: 10px; text-align: center; border-radius: 6px; border: 1px solid {{ $textColor }}; background-color: {{ $bgColor }}; color: {{ $textColor }}; font-weight: bold;">
                {{ $message }}
            </div>
        @endif

        @if ($errors->any())
            <div class="error-style">
                <p>Terjadi kesalahan:</p>
                <ul style="margin-left: 20px;">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

<form method="POST" action="{{ route('participant.store') }}">
            @csrf

            {{-- Hidden field untuk event_id yang dipilih --}}
            <input type="hidden" name="event_id" value="{{ $selectedEvent->id }}">

            <div style="margin-bottom: 15px;">
                <label for="name" class="label-style">Nama Lengkap:</label>
                <input type="text" id="name" name="name" value="{{ old('name') }}" required class="input-style">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="email" class="label-style">Email:</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="input-style">
            </div>

            <div style="margin-bottom: 15px;">
                <label for="phone" class="label-style">Nomor HP (Opsional):</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="input-style">
            </div>

            @if (!empty($selectedEvent->custom_fields_config))
                <h4 style="margin-top: 25px; margin-bottom: 15px; font-weight: bold; color: #333;">Informasi Tambahan</h4>

                @foreach ($selectedEvent->custom_fields_config as $field)
                    <div style="margin-bottom: 15px;">
                        <label for="{{ $field['key'] }}" class="label-style">{{ $field['name'] }}:</label>
                        {{-- Menggunakan tipe input yang dikonfigurasi (text, number, email) --}}
                        <input type="{{ $field['type'] }}"
                               id="{{ $field['key'] }}"
                               name="custom_fields[{{ $field['key'] }}]"
                               value="{{ old('custom_fields.' . $field['key']) }}"
                               {{-- Asumsi semua custom field wajib diisi --}}
                               required
                               class="input-style">
                    </div>
                    @error('custom_fields.' . $field['key'])
                        <p style="color: red; font-size: 12px; margin-top: -10px;">{{ $message }}</p>
                    @enderror
                @endforeach
            @endif
            <button type="submit" style="margin-top: 25px;" class="button-style">
                Daftar Sekarang
            </button>
        </form>
    </div>
</body>
</html>
