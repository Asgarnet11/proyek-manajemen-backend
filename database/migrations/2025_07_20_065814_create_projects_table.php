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

        Schema::create('projects', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('location');
            $table->string('type');
            $table->enum('status', ['berjalan', 'selesai', 'ditunda'])->default('berjalan');
            $table->text('description')->nullable();
            $table->string('client_name');
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('pic_id')->constrained('users');
            $table->string('dokumen_path')->nullable();
            $table->softDeletes();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('projects');
    }
};
