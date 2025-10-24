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

                    <div class="mb-4">
                        <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Deskripsi (Opsional)</label>
                        <textarea id="description" name="description" rows="3"
                               class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description', $event->description) }}</textarea>
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
