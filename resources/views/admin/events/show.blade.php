{{-- resources/views/admin/events/show.blade.php --}}

<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 dark:text-gray-200 leading-tight">
            {{ __('Detail Event: ' . $event->name) }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">

                    {{-- Tombol Kembali --}}
                    <a href="{{ route('admin.events.index') }}" class="text-sm text-gray-600 dark:text-gray-400 hover:text-gray-800 mb-6 inline-block">
                        &larr; Kembali ke Daftar Event
                    </a>

                    <h3 class="text-2xl font-bold mb-4">{{ $event->name }}</h3>
                    <p class="text-sm text-gray-600 dark:text-gray-400 mb-6">
                        Tanggal: {{ \Carbon\Carbon::parse($event->date)->isoFormat('dddd, D MMMM Y') }} |
                        Status: {{ $event->is_active ? 'Aktif' : 'Non-Aktif' }}
                    </p>

                    {{-- Statistik Peserta --}}
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 text-center">
                        <div class="p-4 bg-blue-100 dark:bg-blue-900/50 rounded-lg shadow">
                            <p class="text-sm text-blue-800 dark:text-blue-200">Total Peserta</p>
                            <p class="text-3xl font-bold text-blue-600 dark:text-blue-400">{{ $totalParticipants }}</p>
                        </div>
                        <div class="p-4 bg-green-100 dark:bg-green-900/50 rounded-lg shadow">
                            <p class="text-sm text-green-800 dark:text-green-200">Sudah Check-in</p>
                            <p class="text-3xl font-bold text-green-600 dark:text-green-400">{{ $checkedInCount }}</p>
                        </div>
                        <div class="p-4 bg-red-100 dark:bg-red-900/50 rounded-lg shadow">
                            <p class="text-sm text-red-800 dark:text-red-200">Belum Check-in</p>
                            <p class="text-3xl font-bold text-red-600 dark:text-red-400">{{ $notCheckedInCount }}</p>
                        </div>
                    </div>

                    <h4 class="text-xl font-semibold mt-10 mb-4 border-b pb-2">Daftar Peserta (Total: {{ $totalParticipants }})</h4>

                    {{-- Tabel Peserta --}}
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200 dark:divide-gray-700">
                            <thead class="bg-gray-50 dark:bg-gray-700">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Nama Peserta</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Status Check-in</th>
                                    <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 dark:text-gray-300 uppercase tracking-wider">Token QR</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white dark:bg-gray-800 divide-y divide-gray-200 dark:divide-gray-700">
                                @forelse ($event->participants as $participant)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900 dark:text-gray-100">
                                        {{ $participant->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500 dark:text-gray-300">
                                        {{ $participant->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm">
                                        @if ($participant->is_checked_in)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800 dark:bg-green-700 dark:text-green-100">
                                                Sudah Check-in
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800 dark:bg-red-700 dark:text-red-100">
                                                Belum Check-in
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-mono text-gray-500 dark:text-gray-400">
                                        {{ $participant->qr_code_token }}
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-4 text-center text-gray-500 dark:text-gray-400">
                                        Belum ada peserta terdaftar untuk event ini.
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
