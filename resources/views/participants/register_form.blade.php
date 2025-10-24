<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar: {{ $selectedEvent->name }}</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; background-color: #eef2f5; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .form-container {
            width: 100%;
            max-width: 500px;
            padding: 30px;
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
    </style>
</head>
<body>
    <div class="form-container">
        <a href="{{ route('participant.create') }}" style="display: block; margin-bottom: 20px; font-size: 14px; color: #007bff;">
            &larr; Kembali ke Daftar Event
        </a>

        <h1 style="color: #333; text-align: center; margin-bottom: 25px;">Pendaftaran Event: <br><strong style="color: #007bff;">{{ $selectedEvent->name }}</strong></h1>

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

            <div style="margin-bottom: 25px;">
                <label for="phone" class="label-style">Nomor HP (Opsional):</label>
                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" class="input-style">
            </div>

            <button type="submit" class="button-style">
                Daftar Sekarang
            </button>
        </form>
    </div>
</body>
</html>
