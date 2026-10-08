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
        Schema::create('promotions', function (Blueprint $table) {
            $table->id();
            // Aktor Admin yang membuat & mengajukan promo (relasi ke tabel users)
            $table->foreignId('admin_id')->constrained('users')->onDelete('cascade');
            $table->string('nama_promo');
            $table->string('gambar')->nullable(); // Upload Gambar Makanan/Minuman
            $table->enum('tipe_diskon', ['persen', 'nominal']);
            $table->decimal('nilai_diskon', 10, 2);
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
            $table->text('alasan_verifikasi')->nullable(); // Input Alasan Penolakan / Disetujui
            // Aktor Manajer yang melakukan verifikasi
            $table->foreignId('verified_by')->nullable()->constrained('users')->nullOnDelete();
            $table->date('tanggal_mulai');
            $table->date('tanggal_selesai');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('promotions');
    }
};