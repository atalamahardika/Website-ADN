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
        Schema::create('membership_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('membership_id')->constrained('memberships')->onDelete('cascade');
            $table->enum('payment_type', ['new_registration', 'renewal']); // tipe pembayaran
            $table->decimal('amount', 15, 2); // nominal yang dibayar
            $table->string('bank_name'); // nama bank tujuan pembayaran
            $table->string('account_number'); // nomor rekening tujuan
            $table->string('account_holder'); // atas nama rekening
            $table->text('payment_proof_link'); // link bukti pembayaran dari Google Drive
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('admin_notes')->nullable(); // catatan dari super admin
            $table->text('invoice_link')->nullable(); // link bukti kwitansi/invoice dari admin
            $table->timestamp('approved_at')->nullable();
            $table->foreignId('approved_by')->nullable()->constrained('users')->nullOnDelete(); // super admin yang approve
            $table->date('active_until')->nullable(); // masa aktif sampai 31 Desember tahun pendaftaran
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('membership_payments');
    }
};
