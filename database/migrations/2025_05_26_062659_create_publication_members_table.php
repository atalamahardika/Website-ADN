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
        Schema::create('publication_members', function (Blueprint $table) {
            $table->id();
            $table->foreignId('member_id')->constrained()->onDelete('cascade');
            $table->json('authors'); // disimpan dalam format array
            $table->string('formatted_authors')->nullable(); // untuk menyimpan nama penulis dalam format harvard
            $table->string('title');
            $table->year('year');
            $table->string('journal_name');
            $table->string('volume')->nullable();
            $table->string('pages')->nullable();
            $table->string('link')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('publication_members');
    }
};
