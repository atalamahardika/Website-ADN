<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->string('gelar_depan')->nullable();
            $table->string('gelar_belakang_1')->nullable();
            $table->string('gelar_belakang_2')->nullable();
            $table->string('gelar_belakang_3')->nullable();
            $table->string('nik')->nullable()->unique();
            $table->string('tempat_lahir')->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->string('no_hp')->nullable();
            $table->string('no_wa')->nullable();
            $table->text('alamat_jalan')->nullable();
            $table->string('provinsi')->nullable();
            $table->string('kabupaten')->nullable();
            $table->string('kecamatan')->nullable();
            $table->string('kelurahan')->nullable();
            $table->string('kode_pos')->nullable();
            $table->string('email_institusi')->nullable();
            $table->string('universitas')->nullable();
            $table->string('fakultas')->nullable();
            $table->string('prodi')->nullable();
            $table->text('biografi')->nullable();
            $table->timestamps();
        });

        Schema::create('scientific_fields', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable(); // contoh: Teknologi Informasi, Pendidikan, dll.
            $table->timestamps();
        });

        Schema::create('skills', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->string('name')->nullable(); // contoh: Laravel, Penelitian, Manajemen Proyek
            $table->timestamps();
        });

        Schema::create('educational_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->string('jenjang')->nullable(); // S1, S2, S3
            $table->string('institusi')->nullable();
            $table->string('program_studi')->nullable();
            $table->year('tahun_masuk')->nullable();
            $table->year('tahun_lulus')->nullable();
            $table->timestamps();
        });

        Schema::create('awards', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->string('nama')->nullable();
            $table->string('penyelenggara')->nullable();
            $table->year('tahun')->nullable();
            $table->timestamps();
        });

        Schema::create('teaching_histories', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->string('mata_kuliah')->nullable();
            $table->string('institusi')->nullable();
            $table->year('tahun_ajar')->nullable();
            $table->timestamps();
        });


    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('scientific_fields');
        Schema::dropIfExists('skills');
        Schema::dropIfExists('educational_histories');
        Schema::dropIfExists('awards');
        Schema::dropIfExists('teaching_histories');
        Schema::dropIfExists('members');
    }
};
