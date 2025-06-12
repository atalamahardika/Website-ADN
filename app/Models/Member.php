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
}
