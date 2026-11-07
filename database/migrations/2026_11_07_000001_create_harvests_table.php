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
            $table->string('commodity');
            $table->string('variety')->nullable();
            $table->integer('quantity');
            $table->integer('price_per_kg');
            $table->enum('grade', ['A', 'B', 'C'])->default('B');
            $table->string('marketing_channel');
            $table->date('harvest_date');
            $table->date('ready_date');
            $table->string('photo_path')->nullable();
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

