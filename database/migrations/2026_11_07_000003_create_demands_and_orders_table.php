<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('demands', function (Blueprint $table) {
            $table->id();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->string('commodity');
            $table->integer('quantity'); // kg
            $table->enum('grade', ['A', 'B', 'C'])->default('B');
            $table->integer('max_price_per_kg');
            $table->text('notes')->nullable();
            $table->string('document_path')->nullable();
            $table->enum('status', ['terbuka', 'terpenuhi', 'dibatalkan'])->default('terbuka');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('demands');
    }
};

