<!-- resources/views/participants/ticket.blade.php -->
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo_sympony_icon.png') }}">
    <title>Tiket - {{ $participant->name }}</title>
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
        /* Override untuk wrapper: agar svg tidak punya border sendiri dan mengisi kotak 180x180 */
        #qr-svg-wrapper svg {
            width: 100%;
            height: 100%;
            border: 0;
            display: block;
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
        .download-btn {
            display: inline-block;
            padding: 10px 25px;
            background-color: #28a745; /* Warna hijau untuk download */
            color: white;
            border-radius: 5px;
            text-decoration: none;
            font-weight: 600;
            font-size: 14px;
            margin-top: 30px;
            transition: background-color 0.3s;
        }
        .download-btn:hover {
            background-color: #1e7e34;
        }
    </style>
</head>
<body>
    <div class="ticket-container">
        <div class="header">
            <h1>E-TICKET RESMI</h1>
            <p>
                {{ $participant->event && $participant->event->name ? strtoupper($participant->event->name) : 'EVENT TIDAK DITEMUKAN' }}
            </p>
        </div>

        <div class="content">
            <h2 style="font-size: 20px; color: #007bff; margin-bottom: 20px;">Selamat Datang, {{ $participant->name }}!</h2>

            <div class="info-box">
                <p>Nama: <strong>{{ $participant->name }}</strong></p>
                <p>Email: <strong>{{ $participant->email }}</strong></p>
                <p>No. HP: <strong>{{ $participant->phone ?? '-' }}</strong></p>
            </div>

            <div class="qr-section" id="qr-section">
                <p style="font-size: 16px; font-weight: 600; color: #333; margin-bottom: 15px;">Kode Akses Check-in Anda:</p>

                {{-- Menghasilkan QR Code sebagai SVG (render inline). Kita akan menyediakan tombol untuk download PNG via JS di klien agar tidak perlu Imagick di server --}}
                @php
                    // Tingkatkan resolusi QR menjadi 1080px untuk kualitas maksimal
                    $qrCodeSvg = QrCode::size(1080)->format('svg')->generate($qrDataUrl);
                    $eventName = $participant->event->name ?? 'event-tanpa-nama';
                    $participantName = $participant->name ?? 'peserta-tanpa-nama';
                    $pngFilename = Str::slug($eventName . '-' . $participantName) . '.png';
                @endphp

                {{-- Tampilkan SVG inline (wrapper menambahkan padding putih di kiri/kanan 15px) --}}
                <div id="qr-svg-wrapper" style="display:flex; justify-content:center; align-items:center; margin: 0 auto; width: 210px; height: 192px; border: 6px solid white; box-shadow: 0 0 10px rgba(0, 0, 0, 0.15); border-radius: 5px; padding: 0 15px; background:#ffffff; box-sizing: border-box; overflow: hidden;">
                    <div style="width:180px; height:180px; display:flex; align-items:center; justify-content:center;">
                        {!! $qrCodeSvg !!}
                    </div>
                </div>

                <p style="font-size: 14px; margin-top: 15px; color: #888;">Tunjukkan kode ini kepada Panitia</p>

                {{-- Tombol Download: PNG via JS (client-side) dan fallback SVG --}}
                <a href="#" id="download-png-btn" data-filename="{{ $pngFilename }}" class="download-btn">
                    DOWNLOAD QR TIKET (PNG)
                </a>
            </div>

            <div style="font-size: 14px; color: #555;">

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

<script>
// Konversi SVG yang dirender inline ke PNG di sisi klien untuk menghindari dependensi Imagick
document.addEventListener('DOMContentLoaded', function() {
    const btn = document.getElementById('download-png-btn');
    if (!btn) return;

    btn.addEventListener('click', function(e) {
        e.preventDefault();
        const svg = document.querySelector('#qr-svg-wrapper svg');
        if (!svg) {
            alert('QR SVG tidak ditemukan.');
            return;
        }

        // Serialisasi SVG
        const serializer = new XMLSerializer();
        let svgString = serializer.serializeToString(svg);

        // Pastikan namespace ada
        if (!svgString.match(/^<svg[^>]+xmlns="http:/)) {
            svgString = svgString.replace(/^<svg/, '<svg xmlns="http://www.w3.org/2000/svg"');
        }

        // Buat blob dan gambar
        const svgBlob = new Blob([svgString], { type: 'image/svg+xml;charset=utf-8' });
        const url = URL.createObjectURL(svgBlob);
        const img = new Image();

        img.onload = function() {
            const qrSize = Math.max(img.width, img.height) || 1080;
            const padding = 15 * (qrSize / 180); // Scale padding relative to QR size
            const totalSize = qrSize + (padding * 2);

            const canvas = document.createElement('canvas');
            canvas.width = totalSize;
            canvas.height = totalSize;
            const ctx = canvas.getContext('2d');

            // Putih background untuk PNG (termasuk padding)
            ctx.fillStyle = '#ffffff';
            ctx.fillRect(0, 0, totalSize, totalSize);

            // Gambar QR di tengah dengan padding
            ctx.drawImage(img, padding, padding, qrSize, qrSize);

            URL.revokeObjectURL(url);
            canvas.toBlob(function(blob) {
                const a = document.createElement('a');
                const filename = btn.getAttribute('data-filename') || 'qrcode.png';
                a.download = filename;
                a.href = URL.createObjectURL(blob);
                document.body.appendChild(a);
                a.click();
                a.remove();
                URL.revokeObjectURL(a.href);
            }, 'image/png');
        };

        img.onerror = function() {
            URL.revokeObjectURL(url);
            alert('Gagal mengonversi SVG menjadi PNG. Silakan coba lagi atau hubungi admin.');
        };

        img.src = url;
    });
});
</script>
