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
        Schema::create('costs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('project_id')->constrained()->onDelete('cascade');
            $table->string('description'); // Deskripsi biaya
            $table->decimal('amount', 15, 2); // Jumlah biaya
            $table->enum('type', ['Anggaran', 'Realisasi']);
            $table->enum('status', ['Pending', 'Approved', 'Paid'])->default('Pending');
            $table->string('vendor_name')->nullable(); // Nama vendor/subkontraktor
            $table->string('invoice_path')->nullable(); // Path ke file invoice/kuitansi
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('costs');
    }
};
