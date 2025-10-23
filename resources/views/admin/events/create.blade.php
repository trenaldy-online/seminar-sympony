<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Buat Event Baru') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 shadow sm:rounded-lg p-6">

                {{-- Tampilkan Pesan Error/Sukses jika ada --}}
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

                <form method="POST" action="{{ route('admin.events.store') }}">
                    @csrf

                    <!-- Nama Event -->
                    <div class="mb-4">
                        <label for="name" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Nama Event</label>
                        <input type="text" id="name" name="name" value="{{ old('name') }}" required autofocus
                               class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </div>

                    <!-- Tanggal Event -->
                    <div class="mb-4">
                        <label for="date" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Tanggal Pelaksanaan</label>
                        <input type="date" id="date" name="date" value="{{ old('date') }}" required
                               class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">
                    </div>

                    <!-- Deskripsi -->
                    <div class="mb-4">
                        <label for="description" class="block font-medium text-sm text-gray-700 dark:text-gray-300">Deskripsi (Opsional)</label>
                        <textarea id="description" name="description" rows="3"
                               class="mt-1 block w-full border-gray-300 dark:border-gray-700 dark:bg-gray-900 dark:text-gray-300 focus:border-indigo-500 focus:ring-indigo-500 rounded-md shadow-sm">{{ old('description') }}</textarea>
                    </div>

                    <!-- Status Aktif (Checkbox) -->
                    <div class="mb-6">
                        <input type="checkbox" id="is_active" name="is_active" value="1" checked
                               class="rounded dark:bg-gray-900 border-gray-300 dark:border-gray-700 text-indigo-600 shadow-sm focus:ring-indigo-500">
                        <label for="is_active" class="font-medium text-sm text-gray-700 dark:text-gray-300">Pendaftaran Event Aktif</label>
                        <p class="text-xs text-gray-500 dark:text-gray-400">Jika dicentang, event ini akan muncul di halaman pendaftaran peserta.</p>
                    </div>

                    <div class="flex items-center justify-end">
                        <button type="submit" class="px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-500 focus:bg-indigo-500 active:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 dark:focus:ring-offset-gray-800 transition ease-in-out duration-150">
                            Simpan Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
