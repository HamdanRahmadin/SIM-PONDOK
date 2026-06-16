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
        Schema::create('presensi_halaqohs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
            $table->date('tanggal_masehi');
            $table->tinyInteger('bulan_hijri')->unsigned();
            $table->smallInteger('tahun_hijri')->unsigned();
            $table->enum('status', ['hadir', 'izin_sakit', 'alfa'])->default('alfa');
            $table->text('catatan')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['santri_id', 'tanggal_masehi']);
            $table->index('tanggal_masehi');
            $table->index(['santri_id', 'bulan_hijri', 'tahun_hijri']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('presensi_halaqohs');
    }
};
