<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MembershipPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'membership_id',
        'payment_type',
        'amount',
        'bank_name',
        'account_number',
        'account_holder',
        'payment_proof_link',
        'status',
        'admin_notes',
        'invoice_link',
        'approved_at',
        'approved_by',
        'active_until'
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'approved_at' => 'datetime',
        'active_until' => 'date'
    ];

    // Relationships
    public function membership()
    {
        return $this->belongsTo(Membership::class);
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    // Scopes
    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeApproved($query)
    {
        return $query->where('status', 'approved');
    }

    public function scopeRejected($query)
    {
        return $query->where('status', 'rejected');
    }

    public function scopeNewRegistration($query)
    {
        return $query->where('payment_type', 'new_registration');
    }

    public function scopeRenewal($query)
    {
        return $query->where('payment_type', 'renewal');
    }

    public function scopeActive($query)
    {
        return $query->approved()->whereDate('active_until', '>=', now());
    }

    public function scopeExpired($query)
    {
        return $query->approved()->whereDate('active_until', '<', now());
    }

    // Helper methods
    public function isPending()
    {
        return $this->status === 'pending';
    }

    public function isApproved()
    {
        return $this->status === 'approved';
    }

    public function isRejected()
    {
        return $this->status === 'rejected';
    }

    public function isNewRegistration()
    {
        return $this->payment_type === 'new_registration';
    }

    public function isRenewal()
    {
        return $this->payment_type === 'renewal';
    }

    // Method untuk cek apakah payment masih aktif
    public function isStillActive()
    {
        return $this->isApproved() && $this->active_until && Carbon::parse($this->active_until)->isFuture();
    }

    // Method untuk cek apakah payment sudah expired
    public function isExpired()
    {
        return $this->active_until && Carbon::parse($this->active_until)->isPast();
    }

    // Method untuk mendapatkan sisa hari aktif
    public function getRemainingDays()
    {
        if (!$this->active_until) {
            return 0;
        }

        $expiredDate = Carbon::parse($this->active_until);
        return max(0, now()->diffInDays($expiredDate, false));
    }

    // Method untuk cek apakah sudah waktunya renewal (30 hari sebelum expired)
    public function isRenewalTime()
    {
        if (!$this->active_until) {
            return false;
        }

        $expiredDate = Carbon::parse($this->active_until);
        $renewalDate = $expiredDate->subDays(30);

        return now()->gte($renewalDate);
    }

    // Accessor untuk format amount
    public function getFormattedAmountAttribute()
    {
        return 'Rp. ' . number_format($this->amount, 0, ',', '.');
    }

    // Accessor untuk payment type label
    public function getPaymentTypeLabelAttribute()
    {
        return match ($this->payment_type) {
            'new_registration' => 'Pendaftaran Baru',
            'renewal' => 'Perpanjangan',
            default => 'Tidak Diketahui'
        };
    }

    // Accessor untuk status label
    public function getStatusLabelAttribute()
    {
        return match ($this->status) {
            'pending' => 'Menunggu Konfirmasi',
            'approved' => 'Disetujui',
            'rejected' => 'Ditolak',
            default => 'Tidak Diketahui'
        };
    }

    // Accessor untuk formatted active until date
    public function getFormattedActiveUntilAttribute()
    {
        return $this->active_until ? Carbon::parse($this->active_until)->format('d M Y') : '-';
    }

    // Method untuk set masa aktif (sampai 31 Desember tahun ini)
    public function setActiveUntilEndOfYear()
    {
        $this->active_until = Carbon::now()->endOfYear()->toDateString();
        $this->save();
    }

    // Method untuk set masa aktif custom
    public function setActiveUntil($date)
    {
        $this->active_until = Carbon::parse($date)->toDateString();
        $this->save();
    }

    // Method untuk approve payment
    public function approve($superAdminId, $invoiceLink = null, $notes = null, $activeUntil = null)
    {
        $this->status = 'approved';
        $this->approved_by = $superAdminId;
        $this->approved_at = now();
        $this->invoice_link = $invoiceLink;
        $this->admin_notes = $notes;

        // Set active until date
        if ($activeUntil) {
            $this->active_until = Carbon::parse($activeUntil)->toDateString();
        } else {
            // Default ke akhir tahun
            $this->active_until = Carbon::now()->endOfYear()->toDateString();
        }

        $this->save();

        // Update membership status
        $membership = $this->membership;
        $membership->status = 'active';
        $membership->save();

        return $this;
    }

    // Method untuk reject payment
    public function reject($superAdminId, $notes = null)
    {
        $this->status = 'rejected';
        $this->approved_by = $superAdminId;
        $this->approved_at = now();
        $this->admin_notes = $notes;
        $this->save();

        return $this;
    }

    // Method untuk extend membership (perpanjangan)
    public function extendMembership($months = 12)
    {
        if (!$this->isApproved()) {
            return false;
        }

        // Ambil tanggal expired dari payment terakhir yang aktif
        $lastActivePayment = $this->membership->payments()
            ->approved()
            ->where('id', '!=', $this->id)
            ->orderBy('active_until', 'desc')
            ->first();

        if ($lastActivePayment && $lastActivePayment->active_until) {
            // Extend dari tanggal expired sebelumnya
            $newActiveUntil = Carbon::parse($lastActivePayment->active_until)->addMonths($months);
        } else {
            // Extend dari sekarang
            $newActiveUntil = Carbon::now()->addMonths($months);
        }

        $this->active_until = $newActiveUntil->toDateString();
        $this->save();

        return true;
    }
}
