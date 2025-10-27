{{-- resources/views/admin/events/create.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Event Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">

                {{-- Tampilkan Pesan Error/Sukses jika ada --}}
                @if ($errors->any())
                    <div class="mb-4 p-4 bg-red-100 dark:bg-red-700 border border-red-400 dark:border-red-600 text-red-700 dark:text-red-100 rounded-md">
                        <h3 class="font-bold">Ada beberapa masalah dengan masukan Anda:</h3>
                        <ul class="mt-2 list-disc list-inside">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- PASTIKAN ENCTYPE ADA KARENA ADA INPUT FILE --}}
                <form method="POST" action="{{ route('admin.events.store') }}" enctype="multipart/form-data">
                    @csrf

                    <h4 class="text-xl font-semibold mb-4 border-b pb-2">Detail Acara</h4>

                    <div class="mb-4">
                        <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Event</label>
                        <x-text-input id="name" name="name" type="text" :value="old('name')" required autofocus />
                    </div>

                    <div class="mb-4">
                        <label for="date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tanggal Pelaksanaan</label>
                        <x-text-input id="date" name="date" type="date" :value="old('date')" required />
                    </div>

                    <div class="mb-4">
                        <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Deskripsi Event (Opsional)</label>
                        <textarea id="description" name="description" rows="3"
                               class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                    </div>

                    <div class="mb-6">
                        <label for="banner_image" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Gambar Banner (Opsional)</label>
                        <input type="file" id="banner_image" name="banner_image" accept="image/*"
                               class="mt-1 block w-full text-sm border border-gray-300 dark:border-gray-700 rounded-lg cursor-pointer bg-gray-50 dark:bg-gray-700 focus:outline-none">
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1">Rekomendasi Rasio 16:9. Max: 2MB.</p>
                    </div>

                    <h4 class="text-xl font-semibold mb-4 border-b pb-2 mt-8">Field Pendaftaran Tambahan</h4>
                    <p class="text-sm text-red-500 dark:text-red-400 mb-4">Catatan: Nama, Email, dan Phone sudah otomatis disertakan.</p>

                    <div id="custom-fields-container">
                        {{-- Field dinamis akan dimasukkan di sini oleh JavaScript --}}
                    </div>

                    <button type="button" id="add-field-btn" class="text-sm px-3 py-1 mt-4 border border-dashed border-indigo-400 text-indigo-600 hover:bg-indigo-50 dark:hover:bg-indigo-900 rounded-md transition duration-150">
                        + Tambah Field Baru
                    </button>

                    <div class="mb-6 border-t border-gray-200 dark:border-gray-700 pt-6 mt-6">
                        <input type="checkbox" id="is_active" name="is_active" value="1" {{ old('is_active', true) ? 'checked' : '' }}
                               class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <label for="is_active" class="ml-2 font-medium text-sm text-gray-700 dark:text-gray-300">Pendaftaran Event Aktif</label>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Jika dicentang, pendaftaran akan dibuka di halaman utama.</p>
                    </div>

                    <div class="flex items-center justify-end">
                        <x-primary-button>
                            {{ __('Simpan Event') }}
                        </x-primary-button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        // Tunggu sampai DOM sepenuhnya dimuat
        document.addEventListener('DOMContentLoaded', function() {
            // Ambil referensi elemen yang dibutuhkan
            const container = document.getElementById('custom-fields-container');
            const addButton = document.getElementById('add-field-btn');
            let fieldIndex = 0;

            // Template HTML untuk satu field
            function createFieldTemplate(index, name = '', type = 'text') {
            return `
                <div class="field-row mb-4 p-4 border border-gray-200 dark:border-gray-700 rounded-md" data-index="${index}">
                    <div class="flex justify-between items-start mb-3">
                        <h5 class="text-sm font-semibold text-gray-700 dark:text-gray-300">Field Tambahan #${index + 1}</h5>
                        <button type="button" class="remove-field-btn text-red-500 hover:text-red-700 text-sm">Hapus</button>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block font-medium text-xs text-gray-700 dark:text-gray-300">Nama Form (Key)</label>
                            <input type="text"
                                   name="custom_fields[${index}][name]"
                                   value="${name}"
                                   required
                                   placeholder="Contoh: 'Asal Instansi'"
                                   class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                        </div>
                        <div>
                            <label class="block font-medium text-xs text-gray-700 dark:text-gray-300">Tipe Data</label>
                            <select name="custom_fields[${index}][type]"
                                    required
                                    class="mt-1 block w-full rounded-md shadow-sm border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-100 text-sm">
                                <option value="text" ${type === 'text' ? 'selected' : ''}>Teks (Nama, Alamat)</option>
                                <option value="number" ${type === 'number' ? 'selected' : ''}>Numeric (Angka/Nominal)</option>
                                <option value="email" ${type === 'email' ? 'selected' : ''}>Email</option>
                            </select>
                        </div>
                    </div>
                </div>
            `;
        }

        // Fungsi untuk menambahkan field
        function addField(name = '', type = 'text') {
            const newField = document.createElement('div');
            newField.innerHTML = createFieldTemplate(fieldIndex, name, type);
            container.appendChild(newField.firstChild);
            fieldIndex++;
        }

            // Fungsi untuk menambahkan field baru
            function addField(name = '', type = 'text') {
                const template = createFieldTemplate(fieldIndex, name, type);
                const tempDiv = document.createElement('div');
                tempDiv.innerHTML = template;
                container.appendChild(tempDiv.firstElementChild);
                fieldIndex++;
            }

            // Pasang event listener untuk tombol tambah
            if (addButton) {
                addButton.addEventListener('click', function() {
                    addField();
                });
            }

            // Handler tombol hapus (menggunakan event delegation)
            if (container) {
                container.addEventListener('click', function(e) {
                if (e.target.classList.contains('remove-field-btn')) {
                    const fieldRow = e.target.closest('.field-row');
                    if (fieldRow) {
                        fieldRow.remove();
                    }
                }
            });

            // Opsional: Muat data lama (old input) jika ada error validasi
            const oldFields = @json(old('custom_fields', []));
            if (oldFields && oldFields.length > 0) {
                oldFields.forEach(field => addField(field.name, field.type));
            }
        }
    });
    </script>
</x-app-layout>
