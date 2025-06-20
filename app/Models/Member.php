<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ScientificField;
use App\Models\Award;
use App\Models\EducationalHistory;
use App\Models\Skill;
use App\Models\TeachingHistory;

class Member extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'gelar_depan',
        'gelar_belakang_1',
        'gelar_belakang_2',
        'gelar_belakang_3',
        'nik',
        'tempat_lahir',
        'tanggal_lahir',
        'no_hp',
        'no_wa',
        'alamat_jalan',
        'provinsi',
        'kabupaten',
        'kecamatan',
        'kelurahan',
        'kode_pos',
        'email_institusi',
        'universitas',
        'fakultas',
        'prodi',
        'biografi',
        'bidang_keilmuan',
        'keahlian',
        'riwayat_pendidikan',
        'penghargaan',
        'riwayat_mengajar'
    ];

    protected $casts = [
        'tanggal_lahir' => 'date'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scientificFields()
    {
        return $this->hasMany(ScientificField::class);
    }

    public function skills()
    {
        return $this->hasMany(Skill::class);
    }

    public function educationalHistories()
    {
        return $this->hasMany(EducationalHistory::class);
    }

    public function awards()
    {
        return $this->hasMany(Award::class);
    }

    public function teachingHistories()
    {
        return $this->hasMany(TeachingHistory::class);
    }

    public function publications()
    {
        return $this->hasMany(PublicationMember::class);
    }

    // Relasi dengan Membership
    public function membership()
    {
        return $this->hasOne(Membership::class);
    }

    // Helper methods untuk membership
    public function hasMembership()
    {
        return $this->membership()->exists();
    }

    public function getMembershipStatus()
    {
        if (!$this->hasMembership()) {
            return 'not_registered';
        }

        $membership = $this->membership;
        
        // Check if expired and auto-update status
        $membership->checkAndUpdateExpiredStatus();
        
        return $membership->status;
    }

    public function getMembershipStatusLabel()
    {
        $status = $this->getMembershipStatus();
        
        return match($status) {
            'not_registered' => 'Belum Terdaftar',
            'pending' => 'Menunggu Konfirmasi',
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
            'rejected' => 'Ditolak',
            default => 'Belum Terdaftar'
        };
    }

    public function canRegisterMembership()
    {
        return !$this->hasMembership() || $this->getMembershipStatus() === 'inactive';
    }

    // Method untuk membuat membership baru
    public function createMembership()
    {
        if ($this->hasMembership()) {
            return $this->membership;
        }

        return $this->membership()->create([
            'status' => 'pending'
        ]);
    }

    // Method untuk cek apakah membership aktif
    public function isMembershipActive()
    {
        if (!$this->hasMembership()) {
            return false;
        }

        return $this->membership->isActive();
    }

    // Method untuk mendapatkan tanggal expired membership
    public function getMembershipExpiredDate()
    {
        if (!$this->hasMembership()) {
            return null;
        }

        return $this->membership->active_until;
    }

    // Method untuk mendapatkan sisa hari membership
    public function getMembershipRemainingDays()
    {
        if (!$this->hasMembership()) {
            return 0;
        }

        return $this->membership->getRemainingDays();
    }

    // Method untuk cek apakah sudah waktunya renewal
    public function isMembershipRenewalTime()
    {
        if (!$this->hasMembership()) {
            return false;
        }

        return $this->membership->isRenewalTime();
    }

    // Method untuk cek apakah bisa melakukan renewal
    public function canRenewMembership()
    {
        if (!$this->hasMembership()) {
            return false;
        }

        return $this->membership->canRenew();
    }

    // Method untuk mendapatkan payment yang sedang pending
    public function getPendingMembershipPayment()
    {
        if (!$this->hasMembership()) {
            return null;
        }

        return $this->membership->getPendingPayment();
    }

    // Method untuk mendapatkan history payment membership
    public function getMembershipPaymentHistory()
    {
        if (!$this->hasMembership()) {
            return collect([]);
        }

        return $this->membership->getPaymentHistory();
    }

    // Method untuk mendapatkan payment yang aktif
    public function getActiveMembershipPayment()
    {
        if (!$this->hasMembership()) {
            return null;
        }

        return $this->membership->getActivePayment();
    }

    // Method untuk mendapatkan nama lengkap dengan gelar
    public function getFullNameAttribute()
    {
        $name = '';
        
        if ($this->gelar_depan) {
            $name .= $this->gelar_depan . ' ';
        }
        
        $name .= $this->user->name;
        
        $gelarBelakang = collect([
            $this->gelar_belakang_1,
            $this->gelar_belakang_2,
            $this->gelar_belakang_3
        ])->filter()->implode(', ');
        
        if ($gelarBelakang) {
            $name .= ', ' . $gelarBelakang;
        }
        
        return $name;
    }

    // Method untuk mendapatkan alamat lengkap
    public function getFullAddressAttribute()
    {
        $address = [];
        
        if ($this->alamat_jalan) $address[] = $this->alamat_jalan;
        if ($this->kelurahan) $address[] = 'Kel. ' . ucwords(strtolower($this->kelurahan));
        if ($this->kecamatan) $address[] = 'Kec. ' . ucwords(strtolower($this->kecamatan));
        if ($this->kabupaten) $address[] = ucwords(strtolower($this->kabupaten));
        if ($this->provinsi) $address[] = ucwords(strtolower($this->provinsi));
        if ($this->kode_pos) $address[] = $this->kode_pos;
        
        return implode(', ', $address);
    }
}
