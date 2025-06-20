<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('memberships', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained('members')->onDelete('cascade');
            $table->string('id_member_organization')->nullable()->unique(); // ID anggota yang diberikan super admin
            $table->enum('status', ['pending', 'active', 'inactive', 'rejected'])->default('pending');
            $table->foreignId('division_id')->nullable()->constrained('divisions')->nullOnDelete(); // divisi yang dipilih super admin
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('memberships');
    }
};
