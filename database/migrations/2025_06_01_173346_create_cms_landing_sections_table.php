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
        Schema::create('cms_landing_sections', function (Blueprint $table) {
            $table->id();
            $table->string('section'); // contoh: 'hero', 'about', 'contact'
            $table->string('key');     // contoh: 'title', 'content', 'image', 'email'
            $table->text('value')->nullable(); // konten utama
            $table->string('icon')->nullable(); // khusus untuk contact (icon font-awesome)
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cms_landing_sections');
    }
};
