<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PaymentSetting extends Model
{
    use HasFactory;

    protected $fillable = [
        'bank_name',
        'account_number',
        'account_holder',
        'payment_amount',
        'is_active'
    ];

    protected $casts = [
        'payment_amount' => 'decimal:2',
        'is_active' => 'boolean'
    ];

    // Helper method untuk format rupiah
    public function getFormattedAmountAttribute()
    {
        return 'Rp. ' . number_format($this->payment_amount, 0, ',', '.');
    }

    // Scope untuk mendapatkan setting yang aktif
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Method untuk mendapatkan setting pembayaran aktif
    public static function getActiveSetting()
    {
        return self::where('is_active', true)->first();
    }
}
