
<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('orders', function (Blueprint $table) {
            $table->id();
            $table->string('order_number')->unique();
            $table->foreignId('buyer_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('harvest_id')->constrained('harvests')->onDelete('cascade');
            $table->foreignId('mitra_id')->nullable()->constrained('users')->nullOnDelete();
            $table->integer('quantity');
            $table->integer('unit_price');
            $table->string('delivery_method');
            $table->integer('shipping_cost')->default(0);
            $table->integer('total_price');
            $table->enum('status', [
                'dibuat', 'diproses', 'diambil',
                'dikirim', 'selesai', 'ditolak'
            ])->default('dibuat');
            $table->string('proof_path')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('orders');
    }
};
