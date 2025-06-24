<?php

namespace App\Exports;

use App\Models\Membership;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class MembershipExport implements FromCollection, WithHeadings, WithMapping, WithStyles
{
    protected $status;
    protected $division;

    public function __construct($status = null, $division = null)
    {
        $this->status = $status;
        $this->division = $division;
    }

    public function collection()
    {
        $query = Membership::with([
            'member.user',
            'division',
            'payments' => function ($q) {
                $q->where('status', 'approved')->orderBy('created_at', 'asc');
            }
        ])->whereHas('member.user');

        if ($this->status) {
            $query->where('status', $this->status);
        }

        if ($this->division) {
            $query->where('division_id', $this->division);
        }

        $memberships = $query->orderBy('created_at', 'desc')->get();

        // Transform data untuk setiap payment menjadi row terpisah
        $exportData = collect();

        foreach ($memberships as $membership) {
            $approvedPayments = $membership->payments->where('status', 'approved');

            if ($approvedPayments->isEmpty()) {
                // Jika tidak ada payment yang approved, tetap tampilkan data member
                $exportData->push((object) [
                    'membership' => $membership,
                    'payment' => null,
                    'registration_type' => '-',
                    'payment_date' => '-'
                ]);
            } else {
                // Buat row untuk setiap payment yang approved
                foreach ($approvedPayments as $index => $payment) {
                    $registrationType = $index === 0 ? 'new_registration' : 'renewal';

                    $exportData->push((object) [
                        'membership' => $membership,
                        'payment' => $payment,
                        'registration_type' => $registrationType,
                        'payment_date' => $payment->approved_at ? $payment->approved_at->format('d/m/Y H:i') : '-'
                    ]);
                }
            }
        }

        return $exportData;
    }

    public function headings(): array
    {
        return [
            'No',
            'ID Member Organisasi',
            'Nama Lengkap',
            'Email',
            'NIK',
            'Tempat, Tanggal Lahir',
            'No. HP',
            'No. WhatsApp',
            'Alamat Lengkap',
            'Universitas',
            'Fakultas',
            'Program Studi',
            'Email Institusi',
            'Status Membership',
            'Divisi',
            'Masa Aktif Sampai',
            'Tanggal Daftar',
            'Tipe Pendaftaran', // Kolom baru
            'Jumlah Pembayaran',
            'Tanggal Pembayaran'
        ];
    }

    public function map($data): array
    {
        static $no = 1;

        $membership = $data->membership;
        $payment = $data->payment;
        $member = $membership->member;
        $user = $member->user;

        // Format tempat, tanggal lahir
        $tempatTanggalLahir = $member->tempat_lahir;
        if ($member->tanggal_lahir) {
            $tempatTanggalLahir .= ', ' . $member->tanggal_lahir->format('d/m/Y');
        }

        // Format alamat lengkap dengan nama wilayah
        $alamat = $this->formatFullAddress($member);

        // Ambil masa aktif dari kolom active_until pada tabel membership_payments
        $activeUntil = '-';
        if ($payment) {
            $activeUntil = $payment->active_until ? $payment->active_until->format('d/m/Y') : '-';
        }

        // Format tipe pendaftaran
        $registrationType = $data->registration_type;
        $registrationTypeLabel = $this->getRegistrationTypeLabel($registrationType);

        // Jumlah pembayaran untuk row ini
        $paymentAmount = $payment ? 'Rp. ' . number_format($payment->amount, 0, ',', '.') : '-';

        // Tanggal pembayaran
        $paymentDate = $data->payment_date;

        return [
            $no++,
            $membership->id_member_organization ?? '-',
            $member->full_name,
            $user->email,
            $this->formatNik($member->nik),
            $tempatTanggalLahir,
            $member->no_hp ?? '-',
            $member->no_wa ?? '-',
            $alamat,
            $member->universitas,
            $member->fakultas,
            $member->prodi,
            $member->email_institusi ?? '-',
            $membership->status_label,
            $membership->division->title ?? '-',
            $activeUntil,
            $membership->created_at->format('d/m/Y H:i'),
            $registrationTypeLabel,
            $paymentAmount,
            $paymentDate
        ];
    }

    /**
     * Get label untuk tipe pendaftaran
     */
    private function getRegistrationTypeLabel($type)
    {
        switch ($type) {
            case 'new_registration':
                return 'Pendaftaran Baru';
            case 'renewal':
                return 'Perpanjangan';
            default:
                return '-';
        }
    }

    /**
     * Format NIK dengan tanda petik di depan untuk mencegah scientific notation di Excel
     */
    private function formatNik($nik)
    {
        return $nik ? "'" . $nik : '-';
    }

    /**
     * Format alamat lengkap dengan mengambil nama wilayah dari database
     */
    private function formatFullAddress($member)
    {
        $addressParts = [];

        // Alamat jalan
        if ($member->alamat_jalan) {
            $addressParts[] = $member->alamat_jalan;
        }

        // Kelurahan/Desa
        if ($member->kelurahan) {
            $kelurahanName = $this->getSubdistrictName($member->kelurahan);
            if ($kelurahanName) {
                $addressParts[] = 'Kel. ' . $kelurahanName;
            }
        }

        // Kecamatan
        if ($member->kecamatan) {
            $kecamatanName = $this->getDistrictName($member->kecamatan);
            if ($kecamatanName) {
                $addressParts[] = 'Kec. ' . $kecamatanName;
            }
        }

        // Kabupaten/Kota
        if ($member->kabupaten) {
            $kabupatenName = $this->getCityName($member->kabupaten);
            if ($kabupatenName) {
                $addressParts[] = $kabupatenName;
            }
        }

        // Provinsi
        if ($member->provinsi) {
            $provinsiName = $this->getProvinceName($member->provinsi);
            if ($provinsiName) {
                $addressParts[] = $provinsiName;
            }
        }

        // Kode pos
        if ($member->kode_pos) {
            $addressParts[] = $member->kode_pos;
        }

        return !empty($addressParts) ? implode(', ', $addressParts) : '-';
    }

    /**
     * Ambil nama provinsi berdasarkan ID
     */
    private function getProvinceName($provinsiId)
    {
        $name = DB::table('provinces')
            ->where('prov_id', $provinsiId)
            ->value('prov_name');

        return $name ? $this->formatCapitalizeWords($name) : null;
    }

    /**
     * Ambil nama kabupaten/kota berdasarkan ID
     */
    private function getCityName($cityId)
    {
        $name = DB::table('cities')
            ->where('city_id', $cityId)
            ->value('city_name');

        return $name ? $this->formatCapitalizeWords($name) : null;
    }

    /**
     * Ambil nama kecamatan berdasarkan ID
     */
    private function getDistrictName($districtId)
    {
        $name = DB::table('districts')
            ->where('dis_id', $districtId)
            ->value('dis_name');

        return $name ? $this->formatCapitalizeWords($name) : null;
    }

    /**
     * Ambil nama kelurahan/desa berdasarkan ID
     */
    private function getSubdistrictName($subdistrictId)
    {
        $name = DB::table('subdistricts')
            ->where('subdis_id', $subdistrictId)
            ->value('subdis_name');

        return $name ? $this->formatCapitalizeWords($name) : null;
    }

    /**
     * Format text menjadi Capitalize Each Word dari UPPERCASE
     */
    private function formatCapitalizeWords($text)
    {
        // Convert dari UPPERCASE ke Title Case
        return ucwords(strtolower($text));
    }

    public function styles(Worksheet $sheet)
    {
        return [
            // Style header row
            1 => [
                'font' => ['bold' => true],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => 'E2E8F0']
                ]
            ],
            // Auto width untuk semua kolom (sekarang A:T karena ada kolom tambahan)
            'A:T' => [
                'alignment' => [
                    'wrapText' => true,
                    'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_TOP
                ]
            ]
        ];
    }
}