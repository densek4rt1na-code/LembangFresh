<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('logistics_shipments', function (Blueprint $table) {
            $table->id();
            $table->string('tracking_code')->unique();
            $table->foreignId('mitra_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('sender_name');
            $table->string('receiver_name');
            $table->string('commodity_info');
            $table->integer('weight_kg')->default(100);
            $table->double('distance_km', 8, 2)->default(1.0);
            $table->string('shipping_method')->default('mitra_pengangkut'); // ambil_sendiri, antar_petani, mitra_pengangkut
            $table->integer('shipping_cost')->default(55000);
            $table->enum('status', ['menunggu', 'diproses', 'diambil', 'dikirim', 'sampai_tujuan', 'selesai'])->default('menunggu');
            $table->string('document_path')->nullable(); // Upload file surat jalan/bukti serah terima/foto pengiriman
            $table->text('notes')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('logistics_shipments');
    }
};

