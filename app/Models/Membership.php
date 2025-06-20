<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Membership extends Model
{
    use HasFactory;

    protected $fillable = [
        'member_id',
        'id_member_organization',
        'status',
        'division_id'
    ];

    // Relationships
    public function member()
    {
        return $this->belongsTo(Member::class);
    }

    public function division()
    {
        return $this->belongsTo(Division::class);
    }

    public function payments()
    {
        return $this->hasMany(MembershipPayment::class);
    }

    // Scope untuk status
    public function scopeActive($query)
    {
        return $query->where('status', 'active');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeInactive($query)
    {
        return $query->where('status', 'inactive');
    }

    // Helper methods
    public function isActive()
    {
        if ($this->status !== 'active') {
            return false;
        }

        // Cek dari payment yang sudah approved dan belum expired
        $activePayment = $this->getActivePayment();
        return $activePayment && $activePayment->isStillActive();
    }

    public function isExpired()
    {
        $activePayment = $this->getActivePayment();
        return $activePayment ? $activePayment->isExpired() : false;
    }

    public function isPending()
    {
        return $this->status === 'pending';
    }

    // Method untuk mendapatkan payment yang aktif (approved dan belum expired)
    public function getActivePayment()
    {
        return $this->payments()
            ->approved()
            ->whereDate('active_until', '>=', now())
            ->orderBy('active_until', 'desc')
            ->first();
    }

    // Method untuk mendapatkan tanggal expired dari payment yang aktif
    public function getActiveUntilAttribute()
    {
        $activePayment = $this->getActivePayment();
        return $activePayment ? $activePayment->active_until : null;
    }

    // Method untuk auto-update status jika sudah expired
    public function checkAndUpdateExpiredStatus()
    {
        if ($this->status === 'active' && $this->isExpired()) {
            $this->status = 'inactive';
            $this->save();
        }
    }

    // Method untuk generate ID member organization yang unik
    public static function generateUniqueIdMemberOrganization($prefix = 'ORG')
    {
        do {
            $number = str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
            $id = $prefix . $number;
        } while (self::where('id_member_organization', $id)->exists());

        return $id;
    }

    // Accessor untuk status label
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'active' => 'Aktif',
            'inactive' => 'Tidak Aktif',
            'rejected' => 'Ditolak',
            default => 'Belum Terdaftar'
        };
    }

    // Method untuk mendapatkan payment yang sedang pending
    public function getPendingPayment()
    {
        return $this->payments()->where('status', 'pending')->latest()->first();
    }

    // Method untuk mendapatkan payment yang sudah approved
    public function getApprovedPayments()
    {
        return $this->payments()->where('status', 'approved')->orderBy('created_at', 'desc')->get();
    }

    // Method untuk mendapatkan payment history
    public function getPaymentHistory()
    {
        return $this->payments()->orderBy('created_at', 'desc')->get();
    }

    // Method untuk cek apakah bisa renewal
    public function canRenew()
    {
        return $this->status === 'active' || $this->status === 'inactive';
    }

    // Method untuk cek apakah sudah waktunya renewal (30 hari sebelum expired)
    public function isRenewalTime()
    {
        $activePayment = $this->getActivePayment();
        if (!$activePayment) {
            return false;
        }

        $expiredDate = Carbon::parse($activePayment->active_until);
        $renewalDate = $expiredDate->subDays(30);

        return now()->gte($renewalDate);
    }

    // Method untuk mendapatkan sisa hari aktif
    public function getRemainingDays()
    {
        $activePayment = $this->getActivePayment();
        if (!$activePayment) {
            return 0;
        }

        $expiredDate = Carbon::parse($activePayment->active_until);
        return max(0, now()->diffInDays($expiredDate, false));
    }
}
