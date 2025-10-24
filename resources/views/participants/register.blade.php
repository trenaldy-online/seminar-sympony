<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar Event Seminar | {{ config('app.name', 'Laravel') }}</title>

    {{-- Gunakan Link ke app.css jika Anda ingin Tailwind CSS bekerja pada header/navigation --}}
    {{-- Jika Anda tidak menggunakan Vite (npm run dev), Anda mungkin perlu menghapus baris ini --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; background-color: #f0f4f8; }

        /* HEADER STYLE */
        .page-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 15px 30px;
            background-color: white;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
        }
        .header-logo { font-size: 24px; font-weight: bold; color: #007bff; text-decoration: none; }
        .header-nav a {
            padding: 8px 15px;
            margin-left: 10px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
        }
        .btn-daftar { border: 2px solid #007bff; color: #007bff; }
        .btn-login { background-color: #007bff; color: white; }

        /* SLIDER/CONTENT STYLE */
        .main-container { padding: 40px 20px; max-width: 1200px; margin: 0 auto; }
        .slider-wrapper {
            overflow: hidden;
            border-radius: 12px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            /* Memastikan lebar wrapper sesuai container */
            width: 100%;
        }
        .slides {
            display: flex;
            transition: transform 0.5s ease-in-out;
            /* Lebar ini akan dihitung ulang oleh JS */
            width: 100%;
        }
        .slide {
            /* Penting: min-width 100% dari parent (slides) */
            min-width: 100%;
            width: 100%; /* Ditambah ini untuk memastikan di awal */
            height: 500px;
            background-size: cover;
            background-position: center;
            display: flex;
            justify-content: center;
            align-items: center;
            position: relative;
        }
        .slide-overlay {
            position: absolute;
            inset: 0;
            background: rgba(0, 0, 0, 0.5); /* Gelapkan lagi agar teks lebih kontras */
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            padding: 40px;
        }
        .register-button-slide {
            margin-top: 25px;
            padding: 15px 40px;
            background-color: #ffc107;
            color: #1a1a1a; /* Warna teks lebih gelap untuk kontras */
            border-radius: 8px;
            font-size: 1.1em;
            font-weight: bold;
            text-decoration: none;
            transition: background-color 0.3s, transform 0.1s;
        }
        .register-button-slide:hover { background-color: #e0a800; transform: translateY(-2px); }

        .slide h2 { font-size: 2.5em; margin: 10px 0; }
        .slide p { font-size: 1.1em; opacity: 0.9; }

        .no-image { background-color: #1e88e5 !important; } /* Warna biru cerah jika tidak ada gambar */

        @media (max-width: 800px) {
            .slide { height: 350px; }
            .slide h2 { font-size: 1.8em; }
            .register-button-slide { padding: 10px 25px; font-size: 1em; }
        }
    </style>
</head>
<body>

    {{-- HEADER --}}
    <div class="page-header">
        <a href="{{ url('/') }}" class="header-logo">{{ config('app.name', 'Seminar App') }}</a>
        <div class="header-nav">
            @auth
                <a href="{{ url('/dashboard') }}" class="btn-login">Dashboard</a>
            @else
                <a href="{{ route('login') }}" class="btn-login">Login</a>
                {{-- Register dihilangkan dari header jika Anda ingin hanya admin yang register --}}
            @endauth
        </div>
    </div>

    <div class="main-container">
        @if ($activeEvents->isEmpty())
            {{-- KASUS 0: TIDAK ADA EVENT AKTIF --}}
            <div style="text-align: center; padding: 50px 20px; background-color: white; border-radius: 12px; box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);">
                <h2 style="color: #d9534f;">Pendaftaran Ditutup Sementara</h2>
                <p style="color: #777; margin-top: 15px;">Saat ini, tidak ada Event yang dibuka pendaftarannya. Silakan cek kembali nanti atau hubungi Admin.</p>
            </div>
        @else
            {{-- KASUS 1 & 2: MULTI ATAU SINGLE EVENT --}}
            <div class="slider-wrapper">
                {{-- Lebar slides akan dihitung oleh JS agar sesuai dengan jumlah event --}}
                <div class="slides" id="slides" style="transform: translateX(0%);">
                    @foreach ($activeEvents as $event)
                        @php
                            // PASTIKAN $event->banner_image menyimpan path yang benar (e.g., storage/banners/file.jpg)
                            $bgStyle = $event->banner_image
                                ? 'background-image: url(' . asset($event->banner_image) . ');'
                                : 'background-color: hsl(' . ($event->id * 40 % 360) . ', 70%, 40%);';
                        @endphp

                        <div class="slide {{ $event->banner_image ? '' : 'no-image' }}"
                             style="{{ $bgStyle }}">
                            <div class="slide-overlay">
                                <p style="font-size: 0.9em; font-weight: 500;">{{ \Carbon\Carbon::parse($event->date)->format('l, d F Y') }}</p>
                                <h2>{{ $event->name }}</h2>
                                <p>{{ $event->description ?? 'Klik tombol di bawah untuk informasi dan pendaftaran.' }}</p>

                                {{-- TOMBOL MENGARAH KE FORM DENGAN EVENT ID --}}
                                <a href="{{ route('participant.create', ['event_id' => $event->id]) }}" class="register-button-slide">
                                    DAFTAR SEKARANG
                                </a>

                                @if ($activeEvents->count() > 1)
                                <p style="margin-top: 25px; font-weight: bold; font-size: 0.8em; opacity: 0.7;">Geser otomatis setiap 5 detik</p>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>

    {{-- Script Slider untuk Multi Event (Hanya jika ada lebih dari 1) --}}
    @if ($activeEvents->count() > 1)
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const slidesContainer = document.getElementById('slides');
            const slides = slidesContainer.children;
            const eventCount = slides.length;
            let currentIndex = 0;
            let slideInterval;

            // 1. Tentukan total lebar container slides (penting untuk transisi)
            slidesContainer.style.width = (eventCount * 100) + '%';

            // 2. Fungsi Transisi
            function nextSlide() {
                // Ambil lebar slide saat ini (penting untuk responsif)
                const slideWidth = slides[0].clientWidth;

                currentIndex = (currentIndex + 1) % eventCount;

                // Gunakan transform untuk menggeser slides
                slidesContainer.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
            }

            // 3. Fungsi untuk memulai slider
            function startSlider() {
                // Hentikan interval yang mungkin sudah ada
                clearInterval(slideInterval);
                slideInterval = setInterval(nextSlide, 5000); // 5 detik
            }

            startSlider();

            // 4. Interaksi Mouse (hentikan saat mouse di atas slider)
            slidesContainer.parentNode.addEventListener('mouseenter', () => clearInterval(slideInterval));
            slidesContainer.parentNode.addEventListener('mouseleave', startSlider);

            // 5. Penanganan Resize Window
            window.addEventListener('resize', () => {
                // Hitung ulang posisi saat resize
                const newSlideWidth = slides[0].clientWidth;
                slidesContainer.style.transform = `translateX(-${currentIndex * newSlideWidth}px)`;
            });

            // KASUS SINGLE EVENT (Mengatasi masalah initial load)
            if (eventCount === 1) {
                slidesContainer.style.width = '100%';
                slidesContainer.style.transform = `translateX(0)`;
            }
        });
    </script>
    @endif
</body>
</html>
