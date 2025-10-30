<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Edit Event: ' . $event->name) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">

                @if ($errors->any())
                    <div class="p-3 mb-4 text-sm text-red-800 rounded-lg bg-red-100 dark:bg-gray-700 dark:text-red-400">
                        <strong>Validasi Gagal:</strong>
                        <ul class="mt-1 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- PASTIKAN ENCTYPE ADA JIKA ANDA MENGGUNAKAN INPUT FILE GAMBAR --}}
                <form method="POST" action="{{ route('admin.events.update', $event) }}" enctype="multipart/form-data">
                    @csrf
                    @method('PATCH')

                    <div class="mb-4">
                        <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Event</label>
                        <input type="text" id="name" name="name"
                               value="{{ old('name', $event->name) }}"
                               required autofocus
                               class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label for="date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tanggal Pelaksanaan</label>
                        <input type="date" id="date" name="date"
                               value="{{ old('date', $event->date) }}"
                               required
                               class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </div>

                    <h4 class="text-xl font-semibold mb-4 border-b pb-2 mt-8">Konfigurasi Pembayaran</h4>

                    <div class="mb-4">
                        <input type="checkbox" id="is_paid_edit" name="is_paid" value="1"
                            @if(old('is_paid', $event->is_paid)) checked @endif
                            class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <label for="is_paid_edit" class="ml-2 font-medium text-sm text-gray-700 dark:text-gray-300">Event Membutuhkan Pembayaran (Berbayar)</label>
                    </div>

                    @php
                        $showPaymentDetails = old('is_paid', $event->is_paid) ? 'block' : 'none';
                    @endphp

                    <div id="payment-details-container-edit" style="display: {{ $showPaymentDetails }};" class="ml-6 p-4 border border-gray-300 dark:border-gray-700 rounded-md bg-gray-50 dark:bg-gray-700">
                        <div class="mb-4">
                            <label for="price" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Harga Tiket (Rp)</label>
                            <input type="number" id="price" name="price" value="{{ old('price', $event->price) }}" min="1000" step="1000"
                                class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                        </div>

                        <div class="mb-4">
                            <label for="bank_name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Bank Tujuan</label>
                            <input type="text" id="bank_name" name="bank_name" value="{{ old('bank_name', $event->bank_name) }}"
                                class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                        </div>

                        <div class="mb-4">
                            <label for="account_number" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nomor Rekening Tujuan</label>
                            <input type="text" id="account_number" name="account_number" value="{{ old('account_number', $event->account_number) }}"
                                class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                        </div>

                        <div>
                            <label for="account_holder" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Pemilik Rekening</label>
                            <input type="text" id="account_holder" name="account_holder" value="{{ old('account_holder', $event->account_holder) }}"
                                class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Deskripsi (Opsional)</label>

                    <div class="mb-4">
                        <label for="max_capacity" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Kuota Maksimal Peserta (Kosongkan jika tidak terbatas)</label>
                        <input type="number" id="max_capacity" name="max_capacity"
                            value="{{ old('max_capacity', $event->max_capacity) }}"
                            min="1"
                            placeholder="Contoh: 100"
                            class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Deskripsi (Opsional)</label>

                        @php
                            // Gunakan old() jika ada error, jika tidak gunakan data dari database
                            $descriptionContent = old('description', $event->description);
                        @endphp

                        {{-- 1. Tambahkan input HIDDEN dengan nama 'description' --}}
                        <input id="x_description_edit" type="hidden" name="description" value="{{ $descriptionContent }}">

                        {{-- 2. Tambahkan Trix Editor --}}
                        <trix-editor input="x_description_edit" class="trix-content mt-1 block w-full">
                            {!! $descriptionContent !!}
                        </trix-editor>

                        {{-- Hapus textarea lama Anda --}}
                        {{-- <textarea id="description" name="description" rows="3" class="..."></textarea> --}}
                    </div>

                    <div class="mb-6">
                        <input type="checkbox" id="is_active" name="is_active" value="1"
                               @if(old('is_active', $event->is_active)) checked @endif
                               class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <label for="is_active" class="ml-2 font-medium text-sm text-gray-700 dark:text-gray-300">Pendaftaran Event Aktif</label>
                    </div>

                    {{-- BAGIAN TOMBOL AKSI (Pastikan ini yang terbaru) --}}
                    <div class="flex items-center justify-end mt-6">
                        <a href="{{ route('admin.events.index') }}"
                           class="text-red-700">
                            Batal
                        </a>

                        <button type="submit"
                            class="text-blue-700">
                            Perbarui Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
<script>
document.addEventListener('DOMContentLoaded', function() {
    const isPaidCheckbox = document.getElementById('is_paid_edit');
    const paymentContainer = document.getElementById('payment-details-container-edit');

    if (isPaidCheckbox && paymentContainer) {
        isPaidCheckbox.addEventListener('change', function() {
            if (this.checked) {
                paymentContainer.style.display = 'block';
            } else {
                paymentContainer.style.display = 'none';
            }
        });
    }
});
</script>
