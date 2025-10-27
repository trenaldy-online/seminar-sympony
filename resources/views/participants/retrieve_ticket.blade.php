<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cari Tiket Saya</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; background-color: #f0f4f8; display: flex; justify-content: center; align-items: center; min-height: 100vh; }
        .form-container {
            width: 100%;
            max-width: 400px;
            padding: 30px;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
        }
        .input-style, .select-style {
            width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; margin-top: 5px;
        }
        .label-style { display: block; margin-bottom: 5px; font-weight: bold; color: #555; }
        .button-style {
            width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; transition: background-color 0.3s;
        }
        .button-style:hover { background-color: #0056b3; }
        .error-style { background-color: #fdd; color: #a00; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #f00; }
        .success-style { background-color: #d4edda; color: #155724; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #c3e6cb; }
    </style>
</head>
<body>
    <div class="form-container">
        <form method="POST" action="{{ route('participant.ticket.retrieve.process') }}">
            @csrf

            <div style="margin-bottom: 15px;">
                <label for="event_id" class="label-style">Pilih Event:</label>
                <select id="event_id" name="event_id" required class="input-style select-style">
                    <option value="">-- Pilih Event --</option>
                    @foreach ($activeEvents as $event)
                        <option value="{{ $event->id }}" {{ old('event_id') == $event->id ? 'selected' : '' }}>
                            {{ $event->name }}
                        </option>
                    @endforeach
                </select>
                @error('event_id')
                    <p style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 15px;">
                <label for="nik" class="label-style">NIK (Nomor Induk Kependudukan):</label>
                <input type="text" id="nik" name="nik" value="{{ old('nik') }}" required minlength="16" maxlength="16" pattern="\d{16}" title="NIK harus terdiri dari 16 digit angka." class="input-style">
                @error('nik')
                    <p style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <div style="margin-bottom: 25px;">
                <label for="email" class="label-style">Email Pendaftaran:</label>
                <input type="email" id="email" name="email" value="{{ old('email') }}" required class="input-style">
                @error('email')
                    <p style="color: red; font-size: 12px; margin-top: 5px;">{{ $message }}</p>
                @enderror
            </div>

            <button type="submit" class="button-style">
                Cari & Akses Tiket
            </button>
        </form>
    </div>
</body>
</html>
