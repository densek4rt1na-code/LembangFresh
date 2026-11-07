<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('harvests', function (Blueprint $table) {
            $table->id();
            $table->foreignId('farmer_id')->constrained('users')->onDelete('cascade');
            $table->string('commodity'); // E.g. Wortel, Tomat, Brokoli, Kentang, Selada
            $table->string('variety')->nullable(); // E.g. Brastagi, Granola, Cherry
            $table->integer('quantity'); // kg
            $table->integer('price_per_kg'); // Rp
            $table->enum('grade', ['A', 'B', 'C'])->default('B');
            $table->string('marketing_channel'); // Pasar Normal, Pasar Alternatif, Jalur Pengolahan
            $table->date('harvest_date');
            $table->date('ready_date');
            $table->string('photo_path')->nullable(); // Upload file gambar
            $table->text('description')->nullable();
            $table->enum('status', ['tersedia', 'sebagian_terjual', 'habis'])->default('tersedia');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('harvests');
    }
};

