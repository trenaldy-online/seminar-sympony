<!-- resources/views/participants/ticket.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tiket Seminar - {{ $participant->name }}</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #f0f4f8;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .ticket-container {
            width: 100%;
            max-width: 450px;
            background: linear-gradient(135deg, #ffffff, #f9f9f9);
            border-radius: 15px;
            box-shadow: 0 15px 30px rgba(0, 0, 0, 0.1);
            overflow: hidden;
            border: 1px solid #e0e0e0;
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 30px 20px;
            text-align: center;
            border-bottom: 5px solid #0056b3;
        }
        .header h1 {
            font-size: 24px;
            margin-bottom: 5px;
            font-weight: 700;
        }
        .header p {
            font-size: 14px;
            opacity: 0.9;
        }
        .content {
            padding: 30px;
            text-align: center;
        }
        .info-box {
            background-color: #eaf5ff;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 25px;
            border: 1px dashed #007bff;
        }
        .info-box p {
            margin: 5px 0;
            font-size: 15px;
            color: #333;
            font-weight: 600;
        }
        .qr-section {
            padding: 20px 0;
            margin-bottom: 25px;
            border-top: 1px dashed #ccc;
            border-bottom: 1px dashed #ccc;
        }
        .qr-section svg {
            display: block;
            margin: 0 auto;
            border: 6px solid white; /* Border putih di sekeliling QR */
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.15);
            border-radius: 5px;
        }
        .footer {
            background-color: #f0f4f8;
            padding: 15px 20px;
            text-align: center;
            font-size: 12px;
            color: #666;
        }
        .footer p {
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="header">
            <h1>E-TICKET RESMI</h1>
            <p>SEMINAR TEKNOLOGI INOVASI 2024</p>
        </div>

        <div class="content">
            <h2 style="font-size: 20px; color: #007bff; margin-bottom: 20px;">Selamat Datang, {{ $participant->name }}!</h2>

            <div class="info-box">
                <p>Nama: <strong>{{ $participant->name }}</strong></p>
                <p>Email: <strong>{{ $participant->email }}</strong></p>
                <p>No. HP: <strong>{{ $participant->phone ?? '-' }}</strong></p>
            </div>

            <div class="qr-section">
                <p style="font-size: 16px; font-weight: 600; color: #333; margin-bottom: 15px;">Kode Akses Check-in Anda:</p>

                {{-- Menghasilkan QR Code --}}
                {{-- Data yang di-encode ke QR Code adalah $qrDataUrl (TOKEN) --}}
                {!! QrCode::size(180)->generate($qrDataUrl) !!}

                <p style="font-size: 14px; margin-top: 15px; color: #888;">Tunjukkan kode ini kepada Panitia</p>
            </div>

            <div style="font-size: 14px; color: #555;">
                <p style="font-weight: 700;">TOKEN TIKET:</p>
                <code style="background-color: #eee; padding: 5px 10px; border-radius: 3px; font-weight: bold; color: #007bff;">{{ $participant->qr_code_token }}</code>
            </div>
        </div>

        <div class="footer">
            <p>Terima kasih telah mendaftar. Kami tunggu kehadiran Anda!</p>
        </div>
    </div>
</body>
</html>
