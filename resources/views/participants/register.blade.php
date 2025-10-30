<html lang="en">
<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>Daftar Event Seminar | {{ config('app.name', 'Laravel') }}</title>
    <link rel="icon" type="image/png" sizes="32x32" href="{{ asset('images/logo_sympony_icon.png') }}">

	@vite(['resources/css/app.css', 'resources/js/app.js'])

	<style>
		body { font-family: sans-serif; margin: 0; padding: 0; background-color: #f0f4f8; }

		/* HEADER STYLE (Disesuaikan agar lebih ringkas di mobile) */
		.page-header {
			display: flex;
			justify-content: space-between;
			align-items: center;
			padding: 10px 30px; /* Mengurangi padding default */
			background-color: white;
			box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
		}
        /* Tambahan CSS mobile spesifik untuk header */
		@media (max-width: 640px) {
			.page-header {
				padding: 10px 15px;
			}
			.header-logo {
				font-size: 20px;
			}
			.header-nav a {
				padding: 6px 10px;
				margin-left: 5px;
				font-size: 14px;
			}
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
		.main-container { padding: 20px 15px; max-width: 1200px; margin: 0 auto; }
		.slider-wrapper {
			overflow: hidden;
			border-top-left-radius: 12px;
			border-top-right-radius: 12px;
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
			width: 100%;
		}
		.slides {
			display: flex;
			transition: transform 0.5s ease-in-out;
			width: 100%;
		}
		.slide {
			/* Perbaikan Utama: Memaksa rasio 16:9 dan memastikan gambar TIDAK terpotong */
			min-width: 100%;
			width: 100%;
			height: 0;
			padding-top: 56.25%; /* Rasio 16:9 (9/16 = 0.5625) */
			position: relative;

			/* Perubahan Kunci: Menggunakan 'contain' dan latar belakang netral */
			background-size: contain;
			background-repeat: no-repeat;
			background-position: center;
			background-color: #000000; /* Latar belakang hitam untuk mengisi area kosong */

			display: block;
		}
		.slide-overlay {
			display: none;
		}

		/* KONTEN BARU: Untuk Judul, Deskripsi, dan Tombol di bawah gambar */
		.slide-content-bottom {
			background-color: white;
			padding: 30px;
			border-bottom-left-radius: 12px;
			border-bottom-right-radius: 12px;
			box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
			margin-top: 0px; /* Hapus margin negatif, biarkan diatur oleh spacing di atas */
			text-align: center;
		}
		.slide-content-bottom h2 {
			font-size: 2.2em;
			color: #333;
			margin-top: 0;
			margin-bottom: 10px;
		}
		.slide-content-bottom p {
			color: #555;
			margin-bottom: 25px;
		}

        .preserve-breaks {
            white-space: pre-wrap;
            word-wrap: break-word; /* Memastikan baris panjang terpotong */
        }

		.register-button-slide {
			padding: 15px 40px;
			background-color: #007bff;
			color: white;
			border-radius: 8px;
			font-size: 1.1em;
			font-weight: bold;
			text-decoration: none;
			display: inline-block;
			transition: background-color 0.3s;
		}
		.register-button-slide:hover { background-color: #0056b3; }

		.no-image { background-color: #1e88e5 !important; }

		@media (min-width: 800px) {
			/* Untuk desktop, gunakan rasio 16:9 */
			.slide { padding-top: 56.25%; }
		}

        /* PERBAIKAN MOBILE HEADER KHUSUS: Mengurangi padding di layar sangat kecil */
		@media (max-width: 640px) {
			.page-header {
				padding: 10px 15px; /* Lebih kecil */
			}
			.header-logo {
				font-size: 20px; /* Logo sedikit kecil */
			}
			.header-nav a {
				padding: 6px 12px; /* Tombol lebih kecil */
				margin-left: 5px;
				font-size: 14px;
		}

        /* Sesuaikan ukuran font di mobile */
		@media (max-width: 640px) {
			.main-container { padding: 20px 10px; }
			.slide-content-bottom h2 { font-size: 1.6em; }
			.slide-content-bottom { padding: 20px 15px; }
		}
	</style>
</head>
<body>

	{{-- HEADER --}}
	<div class="page-header">
        {{-- Pastikan ini menggunakan tag a untuk kembali ke home --}}
        <a href="{{ url('/') }}" class="header-logo">
        {{-- Hapus: {{ config('app.name', 'Seminar App') }} --}}

        {{-- Ganti dengan Logo --}}
        <img src="{{ asset('images/logo_sympony.PNG') }}" alt="Sympony Logo" style="height: 30px; width: auto;">

    </a>
        <div class="header-nav">
            <a href="{{ route('participant.ticket.retrieve.form') }}" class="btn-daftar" style="border: 1px solid #007bff;">
                Cari Tiket
            </a>
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
				<div class="slides" id="slides" style="transform: translateX(0%);">
					@foreach ($activeEvents as $event)
						@php
							$bgStyle = $event->banner_image
								? 'background-image: url(' . asset($event->banner_image) . ');'
								: 'background-color: hsl(' . ($event->id * 40 % 360) . ', 70%, 40%);';
						@endphp

						<div class="slide {{ $event->banner_image ? '' : 'no-image' }}"
							 style="{{ $bgStyle }}">
							{{-- Konten dipindahkan ke div di bawah --}}
						</div>
					@endforeach
				</div>
			</div>

			{{-- KONTEN DESKRIPSI DAN TOMBOL DI BAWAH GAMBAR --}}
			<div class="slide-content-bottom" id="slide-content-bottom" style="text-align: center;">
                <p id="event-date" style="font-size: 0.9em; font-weight: 500; color: #888; text-align: center;"></p>
                <h2 id="event-title" style="text-align: center;">Memuat Event...</h2>
                <p id="event-description" class="preserve-breaks" style="text-align: left;">Deskripsi acara akan ditampilkan di sini.</p>
            </div>
            <div class="slide-content-bottom" id="slide-content-bottom">
				<a href="#" class="register-button-slide" id="register-button-slide">
					DAFTAR SEKARANG
				</a>

				<p style="margin-top: 25px; font-weight: bold; font-size: 0.8em; opacity: 0.7; color: #999;
					/* Hanya tampilkan teks 'Geser otomatis' jika event > 1 */
					display: {{ $activeEvents->count() > 1 ? 'block' : 'none' }};">
					Geser otomatis setiap 5 detik
				</p>
			</div>
		@endif {{-- Ini adalah @endif yang menutup blok @if ($activeEvents->isEmpty()) @else --}}
	</div>

{{-- Script Slider --}}
	@if ($activeEvents->count() > 0)
	<script>
		document.addEventListener('DOMContentLoaded', function () {
			const slidesContainer = document.getElementById('slides');
			const slides = slidesContainer.children;
			const eventCount = slides.length;
			let currentIndex = 0;
			let slideInterval;

			// Siapkan data Event dalam format yang aman untuk JavaScript
			@php
				$eventsForJs = $activeEvents->map(function ($event) {
					$dateString = \Carbon\Carbon::parse($event->date)->isoFormat('dddd, D MMMM Y');

					// Menambahkan data kuota untuk JS
					$capacityInfo = null;
					if ($event->max_capacity !== null) {
						$remaining = $event->max_capacity - $event->registeredCount;
						$capacityInfo = [
							'max' => $event->max_capacity,
							'registered' => $event->registeredCount,
							'remaining' => $remaining,
							'isFull' => $event->isFull,
						];
					}

					return [
						'id' => $event->id,
						'name' => $event->name,
						'description' => $event->description ?? 'Klik tombol di bawah untuk informasi dan pendaftaran.',
						'date' => $dateString,
						'url' => route('participant.create', ['event_id' => $event->id]),
						'capacity' => $capacityInfo, // <<< DATA KUOTA
						'isFull' => $event->isFull, // <<< STATUS PENUH
					];
				})->values()->toArray();
			@endphp
			const eventData = @json($eventsForJs);

			const titleElement = document.getElementById('event-title');
			const dateElement = document.getElementById('event-date');
			const descElement = document.getElementById('event-description');
			const buttonElement = document.getElementById('register-button-slide');
            const capacityDisplayElement = document.getElementById('event-capacity'); // Elemen Kuota

			function updateContent(index) {
				const data = eventData[index];
				titleElement.textContent = data.name;
				dateElement.textContent = data.date;
				descElement.innerHTML = data.description;
				buttonElement.href = data.url;

                // === LOGIKA TAMPILAN KUOTA ===
                if (data.capacity) {
                    capacityDisplayElement.style.display = 'block';
                    if (data.isFull) {
                        capacityDisplayElement.innerHTML = '<span style="color: red;">KUOTA PENUH</span>';
                        buttonElement.style.display = 'none'; // Sembunyikan tombol daftar
                    } else {
                        let color = data.capacity.remaining <= 10 ? 'red' : 'green'; // Beri warna jika slot menipis
                        capacityDisplayElement.innerHTML = 'Slot Tersisa: <span style="color: ' + color + ';">' + data.capacity.remaining + '</span> dari ' + data.capacity.max;
                        buttonElement.style.display = 'inline-block'; // Tampilkan tombol daftar
                    }
                } else {
                    capacityDisplayElement.style.display = 'none'; // Sembunyikan jika kuota tidak terbatas
                    buttonElement.style.display = 'inline-block'; // Pastikan tombol ditampilkan
                }
                // ==============================
			}

			updateContent(currentIndex);
			slidesContainer.style.width = (eventCount * 100) + '%';

			if (eventCount > 1) {
				// ... Logic Slider Multi Event ...

				function nextSlide() {
					const slideWidth = slides[0].clientWidth;
					currentIndex = (currentIndex + 1) % eventCount;
					slidesContainer.style.transform = `translateX(-${currentIndex * slideWidth}px)`;
					updateContent(currentIndex);
				}

				function startSlider() {
					clearInterval(slideInterval);
					slideInterval = setInterval(nextSlide, 5000);
				}

				startSlider();

				slidesContainer.parentNode.addEventListener('mouseenter', () => clearInterval(slideInterval));
				slidesContainer.parentNode.addEventListener('mouseleave', startSlider);

				window.addEventListener('resize', () => {
					const newSlideWidth = slides[0].clientWidth;
					slidesContainer.style.transform = `translateX(-${currentIndex * newSlideWidth}px)`;
				});
			} else if (eventCount === 1) {
				slidesContainer.style.width = '100%';
				slidesContainer.style.transform = `translateX(0)`;
			}
		});
	</script>
	@endif
</body>
</html>
