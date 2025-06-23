<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'role',
        'password',
        'profile_photo',
        'last_login',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getProfilePhotoUrlAttribute()
    {
        // Jika ada profile_photo di database
        if ($this->profile_photo) {
            // PERBAIKAN: Gunakan Storage::disk('public')->exists() untuk memeriksa keberadaan file di storage
            if (Storage::disk('public')->exists($this->profile_photo)) {
                // PERBAIKAN: Gunakan asset('storage/' . ...) untuk menghasilkan URL yang benar
                return asset('storage/' . $this->profile_photo);
            }
        }

        // Fallback: Jika profile_photo null/kosong atau file tidak ditemukan di storage
        // Lokasi template photo yang Anda sebutkan: public/images/profile-user/template_photo_profile.png
        return asset('images/profile-user/template_photo_profile.png');
    }

    public function member()
    {
        return $this->hasOne(Member::class);
    }

    public function division()
    {
        return $this->hasOne(Division::class, 'admin_id');
    }

    public function scopeAvailableAdmins($query)
    {
        return $query->where('role', 'admin')->whereDoesntHave('division');
    }
}
