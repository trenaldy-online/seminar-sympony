{{-- resources/views/checkin/index.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Check-in Peserta (Scan QR Code)') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6">

                    {{-- Pesan Status Flash (Dibutuhkan untuk input manual) --}}
                    @if (session('success'))
                        <div class="p-4 mb-4 text-sm text-green-800 bg-green-100 rounded-lg dark:bg-green-700 dark:text-green-200">
                            {{ session('success') }}
                        </div>
                    @endif
                    @if (session('warning'))
                        <div class="p-4 mb-4 text-sm text-yellow-800 bg-yellow-100 rounded-lg dark:bg-yellow-700 dark:text-yellow-200">
                            {{ session('warning') }}
                        </div>
                    @endif
                    @if (session('error'))
                        <div class="p-4 mb-4 text-sm text-red-800 bg-red-100 rounded-lg dark:bg-red-700 dark:text-red-200">
                            {{ session('error') }}
                        </div>
                    @endif

                    <h3 class="text-lg font-medium text-gray-900 dark:text-gray-100 mb-4 text-center">
                        Metode Check-in
                    </h3>

                    <div class="mb-4">
                        <label for="camera-select" class="block text-sm font-medium text-gray-700 dark:text-gray-300 mb-2">
                            Pilih Kamera:
                        </label>
                        <select id="camera-select" disabled
                            class="mt-1 block w-full pl-3 pr-10 py-2 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-md bg-gray-50 dark:bg-gray-700 dark:text-gray-200">
                            <option value="">Memuat perangkat...</option>
                        </select>
                    </div>

                    {{-- Kontrol Tombol Kamera --}}
                    <div class="mb-6 space-x-4 flex justify-center">
                        <button id="start-camera-btn"
                                class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Start Camera
                        </button>
                        <button id="stop-camera-btn" disabled
                                class="inline-flex items-center px-4 py-2 bg-red-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-red-500 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition ease-in-out duration-150 opacity-50 cursor-not-allowed">
                            Stop Camera
                        </button>
                    </div>

                    {{-- Container Kamera --}}
                    <div id="qr-reader" style="width: 100%; max-width: 500px; margin: 0 auto;"></div>

                    {{-- Area Tampilan Status Scan --}}
                    <div id="qr-reader-results" class="mt-6 text-center">
                        <p id="scan-status" class="text-gray-600 dark:text-gray-400">Status: Tekan 'Start Camera' untuk memulai.</p>
                    </div>

                    <div class="mt-8 border-t border-gray-200 dark:border-gray-700 pt-6">
                        <h4 class="text-md font-semibold mb-3 text-gray-900 dark:text-gray-100">
                            Check-in Manual (Token)
                        </h4>

                        {{-- Form Input Manual (Form Standard Submit) --}}
                        <form id="manual-checkin-form" method="POST" action="{{ route('checkin.process') }}" class="flex flex-col sm:flex-row space-y-3 sm:space-y-0 sm:space-x-3 items-end">
                            @csrf

                            <div class="flex-grow w-full">
                                <label for="manual_qr_token" class="block text-sm font-medium text-gray-700 dark:text-gray-300">
                                    Masukkan Kode Token QR:
                                </label>
                                <input type="text" id="manual_qr_token" name="qr_token" required maxlength="10"
                                    class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">
                            </div>

                            <button type="submit"
                                    class="w-full sm:w-auto inline-flex justify-center items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-500 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                                Proses Check-in
                            </button>
                        </form>
                    </div>

                    {{-- Form Tersembunyi (Digunakan oleh Scanner AJAX) --}}
                    <form id="checkin-form-scanner" class="hidden"></form>

                </div>
            </div>
        </div>
    </div>

    {{-- SCRIPT JAVASCRIPT --}}
    <script src="https://unpkg.com/html5-qrcode"></script>
    <script>
        const resultsArea = document.getElementById('scan-status');
        const startBtn = document.getElementById('start-camera-btn');
        const stopBtn = document.getElementById('stop-camera-btn');
        const cameraSelect = document.getElementById('camera-select');

        let html5Qrcode = null;
        let scannerActive = false;
        let isProcessing = false;

        const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

        // --- FUNGSI POP-UP FEEDBACK ---
        function showFeedback(status, participantName, eventName, message) {
            setProcessing(false);

            let icon = '✅';
            let title = 'Check-in Berhasil!';
            let mainMessage = `Selamat datang, ${participantName}! Anda terdaftar untuk Event: ${eventName}. Selamat Mengikuti Event!`;

            if (status === 'error' || status === '404' || status === 'already_checked_in') {
                icon = '❌';
                title = (status === 'already_checked_in' ? 'GAGAL (Sudah Check-in)' : 'Pendaftaran Gagal');
                mainMessage = message;
            }

            // Tampilkan pop-up
            alert(`${icon} ${title}\n\n${mainMessage}`);

            // Atur ulang status scanner setelah feedback
            resultsArea.textContent = 'Status: Pemindaian aktif. Siap memindai QR Code berikutnya.';
            updateButtonState(true);
        }

        // FUNGSI: Mengirim token via AJAX (Hanya digunakan oleh Scanner)
        async function sendTokenForCheckin(qrToken) {
            if (isProcessing) return;
            setProcessing(true);

            try {
                const response = await fetch('{{ route('checkin.process') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'Accept': 'application/json',
                        'X-CSRF-TOKEN': csrfToken
                    },
                    body: JSON.stringify({ qr_token: qrToken })
                });

                const data = await response.json();

                if (!response.ok) {
                    showFeedback(data.status || 'error', data.participant_name, data.event_name, data.message);
                } else {
                    showFeedback(data.status, data.participant_name, data.event_name, data.message);
                }

            } catch (error) {
                console.error("Kesalahan AJAX Check-in:", error);
                showFeedback('error', 'N/A', 'N/A', 'Gagal terhubung ke server atau respon tidak valid. Cek console F12.');
            }
        }

        // Fungsi yang dipanggil saat QR Code berhasil dipindai
        function onScanSuccess(decodedText, decodedResult) {
            sendTokenForCheckin(decodedText.trim());
        }

        function onScanFailure(error) {
            // ... Error handling ringan ...
        }

        // --- FUNGSI KONTROL KAMERA ---

        function setProcessing(isLocked) {
            isProcessing = isLocked;
            // Kunci tombol START & STOP saat AJAX berjalan
            startBtn.disabled = isLocked;
            stopBtn.disabled = isLocked;
        }

        function listCameras() {
            cameraSelect.innerHTML = '<option value="">Memuat perangkat...</option>';
            cameraSelect.disabled = true;

            Html5Qrcode.getCameras().then(devices => {
                if (devices && devices.length) {
                    cameraSelect.innerHTML = '';
                    devices.forEach((device, index) => {
                        const option = document.createElement('option');
                        option.value = device.id;
                        if (device.label.toLowerCase().includes('environment') || device.label.toLowerCase().includes('back') || index === 0) {
                            option.selected = true;
                        }
                        option.text = device.label || `Kamera ${index + 1}`;
                        cameraSelect.appendChild(option);
                    });
                    cameraSelect.disabled = false;
                    resultsArea.textContent = 'Status: Perangkat kamera siap. Tekan Start Camera.';
                } else {
                    cameraSelect.innerHTML = '<option value="">Tidak ada kamera terdeteksi</option>';
                    resultsArea.textContent = 'ERROR: Tidak ada perangkat kamera yang terdeteksi.';
                }
            }).catch(err => {
                console.error("Gagal mendapatkan daftar kamera: ", err);
                cameraSelect.innerHTML = '<option value="">Gagal mendapatkan izin kamera</option>';
                resultsArea.textContent = 'ERROR: Gagal mendapatkan izin kamera. Pastikan HTTPS aktif.';
            });
        }

        function initializeScanner() {
            if (!html5Qrcode) {
                html5Qrcode = new Html5Qrcode("qr-reader");
            }
            listCameras();
        }

        function updateButtonState(isStarting) {
            startBtn.disabled = isStarting;
            startBtn.classList.toggle('opacity-50', isStarting);
            startBtn.classList.toggle('cursor-not-allowed', isStarting);

            stopBtn.disabled = !isStarting;
            stopBtn.classList.toggle('opacity-50', !isStarting);
            stopBtn.classList.toggle('cursor-not-allowed', !isStarting);
            scannerActive = isStarting;

            cameraSelect.disabled = isStarting;
        }

        async function startScanner() {
            if (scannerActive) return;

            const cameraId = cameraSelect.value;
            if (!cameraId) {
                resultsArea.textContent = 'ERROR: Pilih kamera terlebih dahulu.';
                return;
            }

            initializeScanner();
            resultsArea.textContent = 'Status: Memulai pemindaian...';

            try {
                await html5Qrcode.start(
                    cameraId,
                    { fps: 10, qrbox: {width: 250, height: 250} },
                    onScanSuccess,
                    onScanFailure
                );
                updateButtonState(true);
                resultsArea.textContent = 'Status: Pemindaian aktif. Siap memindai QR Code.';
            } catch (err) {
                console.error("Gagal memulai scanner: ", err);
                resultsArea.textContent = 'ERROR: Gagal menghubungkan kamera. Detail: ' + (err.message || err);
                updateButtonState(false);
            }
        }

        async function stopScanner() {
            if (!scannerActive) return;

            try {
                await html5Qrcode.stop();
                resultsArea.textContent = 'Status: Pemindai dihentikan. Tekan Start Camera untuk memulai.';
                updateButtonState(false);
            } catch (err) {
                 console.warn("Gagal menghentikan scanner secara clean, mungkin sudah berhenti: ", err);
                 resultsArea.textContent = 'Status: Pemindai dihentikan. Tekan Start Camera untuk memulai.';
                 updateButtonState(false);
            }
        }

        // Inisialisasi saat halaman dimuat
        document.addEventListener('DOMContentLoaded', initializeScanner);

        // Event Listeners untuk Tombol
        startBtn.addEventListener('click', startScanner);
        stopBtn.addEventListener('click', stopScanner);
    </script>
</x-app-layout>
