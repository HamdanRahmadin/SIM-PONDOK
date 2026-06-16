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
        Schema::create('kamar', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kamar', 50);
            $table->text('keterangan')->nullable();
            $table->timestamps();
        });

        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas', 50);
            $table->foreignId('ustaz_id')->nullable()->constrained('users')->nullOnDelete();
            $table->tinyInteger('urutan')->unsigned()->default(1);
            $table->timestamps();
        });

        Schema::create('santris', function (Blueprint $table) {
            $table->id();
            $table->string('nis', 20)->unique()->nullable();
            $table->string('nama_lengkap', 100);
            $table->string('tempat_lahir', 100)->nullable();
            $table->date('tanggal_lahir')->nullable();
            $table->text('alamat')->nullable();
            $table->foreignId('kamar_id')->nullable()->constrained('kamar')->nullOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->enum('status', ['aktif', 'nonaktif', 'lulus'])->default('aktif');
            $table->date('tanggal_masuk')->nullable();
            $table->date('tanggal_keluar')->nullable();
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->index('status');
            $table->index('kelas_id');
            $table->index('kamar_id');
        });

        Schema::create('tahun_ajarans', function (Blueprint $table) {
            $table->id();
            $table->string('nama', 20);
            $table->unsignedSmallInteger('tahun_hijri');
            $table->boolean('is_aktif')->default(false);
            $table->tinyInteger('koreksi_hilal')->default(0);
            $table->timestamps();
        });

        Schema::create('riwayat_kelas', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
            $table->foreignId('kelas_lama_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->foreignId('kelas_baru_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->foreignId('dipindah_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->index('santri_id');
            $table->index('tahun_ajaran_id');
        });

        Schema::create('libur_massals', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->string('nama_libur', 100);
            $table->date('start_date');
            $table->date('end_date');
            $table->foreignId('created_by')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->date('tanggal_masehi');
            $table->unsignedTinyInteger('bulan_hijri');
            $table->unsignedSmallInteger('tahun_hijri');
            $table->enum('sesi', ['pagi', 'malam']);
            $table->enum('status', ['hadir', 'izin_sakit', 'alfa'])->default('alfa');
            $table->text('catatan')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamps();

            $table->unique(['santri_id', 'tanggal_masehi', 'sesi']);
            $table->index('tanggal_masehi');
            $table->index(['santri_id', 'bulan_hijri', 'tahun_hijri']);
            $table->index('kelas_id');
        });

        Schema::create('konfigurasi_keuangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tahun_ajaran_id')->unique()->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->decimal('nominal_daftar_ulang', 12, 0)->default(0);
            $table->decimal('nominal_syahriah_sem1', 12, 0)->default(0);
            $table->decimal('nominal_syahriah_sem2', 12, 0)->default(0);
            $table->decimal('nominal_majeg_makan', 12, 0)->default(0);
            $table->text('catatan')->nullable();
            $table->timestamps();
        });

        Schema::create('tagihans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
            $table->foreignId('tahun_ajaran_id')->constrained('tahun_ajarans')->cascadeOnDelete();
            $table->enum('kategori', ['daftar_ulang', 'syahriah_sem1', 'syahriah_sem2', 'majeg_makan']);
            $table->unsignedTinyInteger('bulan_hijri')->nullable();
            $table->unsignedSmallInteger('tahun_hijri')->nullable();
            $table->decimal('nominal', 12, 0)->default(0);
            $table->enum('status', ['belum_bayar', 'dicicil', 'lunas', 'pulang'])->default('belum_bayar');
            $table->decimal('nominal_terbayar', 12, 0)->default(0);
            $table->decimal('sisa_tagihan', 12, 0)->storedAs('nominal - nominal_terbayar');
            $table->text('catatan')->nullable();
            $table->timestamps();

            $table->unique(['santri_id', 'tahun_ajaran_id', 'kategori', 'bulan_hijri'], 'tagihan_unique_constraint');
            $table->index(['santri_id', 'tahun_ajaran_id']);
            $table->index('status');
            $table->index('kategori');
        });

        Schema::create('cicilans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('tagihan_id')->constrained('tagihans')->cascadeOnDelete();
            $table->decimal('nominal', 12, 0);
            $table->text('catatan')->nullable();
            $table->foreignId('dicatat_oleh')->nullable()->constrained('users')->nullOnDelete();
            $table->timestamp('created_at')->nullable();

            $table->index('tagihan_id');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('cicilans');
        Schema::dropIfExists('tagihans');
        Schema::dropIfExists('konfigurasi_keuangans');
        Schema::dropIfExists('presensis');
        Schema::dropIfExists('libur_massals');
        Schema::dropIfExists('riwayat_kelas');
        Schema::dropIfExists('tahun_ajarans');
        Schema::dropIfExists('santris');
        Schema::dropIfExists('kelas');
        Schema::dropIfExists('kamar');
    }
};
