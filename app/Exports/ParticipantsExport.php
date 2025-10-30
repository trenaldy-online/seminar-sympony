<?php

namespace App\Exports;

use App\Models\Participant;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Carbon\Carbon;

class ParticipantsExport implements FromCollection, WithHeadings
{
    protected $participants;
    protected $customFieldsConfig;
    public function __construct(Collection $participants, array $customFieldsConfig)
    {
        $this->participants = $participants;
        $this->customFieldsConfig = $customFieldsConfig;
    }

    /**
    * Menentukan header kolom
    */
    public function headings(): array
    {
        // Kolom Statis
        $headers = [
            'NAMA PESERTA',
            'EMAIL',
            'NIK',
            'NO. HP',
            'BIAYA EVENT',
            'KODE UNIK',
            'TOTAL PEMBAYARAN',
            'STATUS PEMBAYARAN',
            'STATUS CHECK-IN',
            'WAKTU DAFTAR',
        ];

        // Tambahkan header dari Custom Fields
        foreach ($this->customFieldsConfig as $field) {
            $headers[] = strtoupper($field['name']);
        }

        return $headers;
    }

    /**
    * Mengambil data peserta
    */
    public function collection(): Collection
    {
        // Mapping data peserta ke format baris Excel
        return $this->participants->map(function ($participant) {

            // --- Logika Pembayaran Baru ---
            // Asumsi relasi 'event' sudah di-load (Eager Loaded) pada koleksi $participants
            $eventPrice = $participant->event->price ?? 0;
            $uniqueCode = $participant->unique_code ?? 0;
            $totalPayment = $eventPrice + $uniqueCode;

            // --- LOGIKA PENENTUAN STATUS PEMBAYARAN YANG DIKOREKSI ---
            $paymentStatusText = 'TIDAK DIKETAHUI';

            if (!$participant->event->is_paid) {
                // 1. Jika Event GRATIS
                $paymentStatusText = 'GRATIS';
            } elseif ($participant->is_paid) {
                // 2. Jika Event BERBAYAR dan is_paid TRUE (Sudah divalidasi Admin)
                $paymentStatusText = 'SUDAH DIBAYAR';
            } else {
                // 3. Jika Event BERBAYAR dan is_paid FALSE
                $paymentStatusText = 'MENUNGGU PEMBAYARAN';
            }

            // Konversi status check-in ke teks
            $status = $participant->is_checked_in ? 'SUDAH CHECK-IN' : 'BELUM CHECK-IN';

            // Data Statis
            $data = [
                $participant->name,
                $participant->email,
                $participant->nik ?? '-',
                $participant->phone ?? '-',

                // Data Pembayaran
                number_format($eventPrice, 0, ',', '.'),
                $uniqueCode,
                number_format($totalPayment, 0, ',', '.'),
                $paymentStatusText,

                $status,
                $participant->created_at->setTimezone('Asia/Jakarta')->format('d/m/Y H:i:s'),
            ];

            // Tambahkan data dari Custom Fields
            $customData = $participant->custom_fields_data ?? [];
            foreach ($this->customFieldsConfig as $field) {
                // Ambil nilai berdasarkan 'key' yang disimpan
                $key = $field['key'];
                $data[] = $customData[$key] ?? '-';
            }

            return $data;
        });
    }
}
