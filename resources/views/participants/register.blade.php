<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran Peserta Seminar</title>
    <style>
        body { font-family: sans-serif; margin: 0; padding: 0; background-color: #eef2f5; }
        .main-container {
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
            max-width: 1200px;
            margin: 0 auto;
        }
        .content-wrapper {
            display: flex;
            width: 100%;
            max-width: 1000px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
            background-color: white;
        }
        .slider-container {
            flex: 1;
            padding: 0;
            background-color: #fff;
            position: relative;
            min-height: 500px;
            max-width: 50%; /* Default two-column */
        }
        .form-container {
            flex: 1;
            padding: 30px;
            background-color: #fcfcfc;
            max-width: 50%; /* Default two-column */
        }
        .single-event-mode .slider-container { display: none !important; }
        .single-event-mode .form-container { max-width: 600px; margin: 0 auto; flex: none; width: 100%; }

        /* Slider Styles */
        .slider {
            width: 100%;
            height: 100%;
            overflow: hidden;
            position: absolute;
        }
        .slides {
            display: flex;
            transition: transform 0.5s ease-in-out;
            height: 100%;
        }
        .slide {
            min-width: 100%;
            height: 100%;
            box-sizing: border-box;
            padding: 40px;
            color: white;
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            text-align: center;
            background-size: cover;
            background-position: center;
        }
        .slide h2 { font-size: 2em; margin-bottom: 10px; }
        .slide p { font-size: 1.1em; opacity: 0.8; }

        /* Responsive Design */
        @media (max-width: 800px) {
            .content-wrapper {
                flex-direction: column;
                max-width: 100%;
            }
            .slider-container, .form-container {
                max-width: 100% !important;
                min-height: 300px;
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <div class="main-container">
        <div class="content-wrapper" id="content-wrapper">

            <!-- Kiri: Slider Event -->
            <div class="slider-container" id="slider-container">
                @if ($activeEvents->isNotEmpty())
                <div class="slider" id="slider">
                    <div class="slides" id="slides">
                        @foreach ($activeEvents as $event)
                            <!-- Placeholder Banner Acara -->
                            <div class="slide"
                                 style="background-color: hsl({{ $event->id * 40 % 360 }}, 70%, 40%);">
                                 <p style="font-size: 0.9em; margin-bottom: 5px;">{{ \Carbon\Carbon::parse($event->date)->format('d F Y') }}</p>
                                <h2 style="font-weight: bold;">{{ $event->name }}</h2>
                                @if($event->description)
                                    <p>{{ $event->description }}</p>
                                @else
                                    <p>Segera daftarkan diri Anda untuk acara spektakuler ini!</p>
                                @endif
                                <p style="margin-top: 20px; font-weight: bold; font-size: 0.8em; opacity: 0.7;">Geser otomatis setiap 5 detik</p>
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Kanan: Formulir Pendaftaran -->
            <div class="form-container" id="form-container">
                <h1 style="color: #333; text-align: center; margin-bottom: 25px;">Formulir Pendaftaran</h1>

                @if ($errors->any())
                    <div style="background-color: #fdd; color: #a00; padding: 10px; margin-bottom: 15px; border-radius: 4px; border: 1px solid #f00;">
                        <p>Terjadi kesalahan:</p>
                        <ul style="margin-left: 20px;">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                @forelse ($activeEvents as $event)
                    @if ($loop->first)
                        <!-- Jika ada event, tampilkan form -->
                        <form method="POST" action="{{ route('participant.store') }}">
                            @csrf

                            <!-- Dropdown Pilihan Event -->
                            <div style="margin-bottom: 15px;">
                                <label for="event_id" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">Pilih Acara:</label>
                                <select id="event_id" name="event_id" required
                                        style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                                    @foreach ($activeEvents as $eventOption)
                                        <option value="{{ $eventOption->id }}"
                                                {{ old('event_id') == $eventOption->id ? 'selected' : '' }}>
                                            {{ $eventOption->name }} ({{ \Carbon\Carbon::parse($eventOption->date)->format('d M Y') }})
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <!-- Input Nama -->
                            <div style="margin-bottom: 15px;">
                                <label for="name" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">Nama Lengkap:</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" required
                                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                            </div>

                            <!-- Input Email -->
                            <div style="margin-bottom: 15px;">
                                <label for="email" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">Email:</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" required
                                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                            </div>

                            <!-- Input Phone -->
                            <div style="margin-bottom: 25px;">
                                <label for="phone" style="display: block; margin-bottom: 5px; font-weight: bold; color: #555;">Nomor HP (Opsional):</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}"
                                    style="width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px;">
                            </div>

                            <button type="submit" style="width: 100%; padding: 12px; background-color: #007bff; color: white; border: none; border-radius: 4px; cursor: pointer; font-size: 16px; font-weight: bold; transition: background-color 0.3s;">
                                Daftar Sekarang
                            </button>
                        </form>
                    @endif
                @empty
                    <!-- Jika tidak ada event aktif -->
                    <div style="text-align: center; padding: 50px 20px;">
                        <h2 style="color: #d9534f;">Pendaftaran Ditutup Sementara</h2>
                        <p style="color: #777; margin-top: 15px;">Saat ini, tidak ada Event yang dibuka pendaftarannya. Silakan cek kembali nanti atau hubungi Admin.</p>
                    </div>
                @endforelse

            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const slider = document.getElementById('slider');
            const slides = document.getElementById('slides');
            const sliderContainer = document.getElementById('slider-container');
            const formContainer = document.getElementById('form-container');
            const contentWrapper = document.getElementById('content-wrapper');
            const selectEvent = document.getElementById('event_id');

            // Cek jumlah event aktif
            const eventCount = {{ $activeEvents->count() }};

            let currentIndex = 0;
            let slideInterval;

            if (eventCount <= 1) {
                // Logika Event Tunggal (tampilkan di tengah)
                sliderContainer.style.display = 'none';
                contentWrapper.classList.add('single-event-mode');

                // Set value Event secara otomatis jika hanya ada satu
                if (eventCount === 1 && selectEvent) {
                    selectEvent.value = '{{ $activeEvents->first()->id }}';
                    selectEvent.disabled = true; // Nonaktifkan dropdown agar user tidak bisa mengubahnya
                    selectEvent.style.backgroundColor = '#f0f0f0';
                }

            } else {
                // Logika Multi-Event (slider)
                const slideWidth = slides.children[0].clientWidth;

                function nextSlide() {
                    currentIndex = (currentIndex + 1) % eventCount;
                    slides.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
                }

                function startSlider() {
                    slideInterval = setInterval(nextSlide, 5000); // 5 detik
                }

                startSlider();

                // Bersihkan interval saat mouse hover
                sliderContainer.addEventListener('mouseenter', () => clearInterval(slideInterval));
                sliderContainer.addEventListener('mouseleave', startSlider);
            }
        });
    </script>
</body>
</html>
