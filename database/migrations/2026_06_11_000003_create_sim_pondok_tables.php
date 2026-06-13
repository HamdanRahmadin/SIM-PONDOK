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
        Schema::create('kelas', function (Blueprint $table) {
            $table->id();
            $table->string('nama_kelas');
            $table->timestamps();
        });

        Schema::create('santris', function (Blueprint $table) {
            $table->id();
            $table->string('nama_lengkap');
            $table->string('tempat_lahir');
            $table->date('tanggal_lahir');
            $table->text('alamat');
            $table->string('status')->default('aktif'); // aktif, nonaktif, lulus
            $table->foreignId('kelas_id')->nullable()->constrained('kelas')->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('presensis', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
            $table->date('tanggal_gregorian');
            $table->string('tanggal_hijriah');
            $table->unsignedTinyInteger('bulan_hijriah'); // 1-12
            $table->unsignedInteger('tahun_hijriah');
            $table->string('sesi'); // pagi, malam
            $table->string('status')->nullable(); // hadir, alfa, izin, sakit (null = belum input)
            $table->text('catatan_setoran')->nullable();
            $table->timestamps();

            $table->unique(['santri_id', 'tanggal_gregorian', 'sesi']);
        });

        Schema::create('hafalans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
            $table->unsignedTinyInteger('bulan_hijriah'); // 1-12
            $table->unsignedInteger('tahun_hijriah');
            $table->text('hafalan_text');
            $table->timestamps();

            $table->unique(['santri_id', 'bulan_hijriah', 'tahun_hijriah']);
        });

        Schema::create('keuangans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('santri_id')->constrained('santris')->cascadeOnDelete();
            $table->unsignedInteger('tahun_ajaran'); // e.g. 1447
            $table->string('kategori'); // daftar_ulang, majeg_makan_1 to _10, syahriah_dzulqadah, syahriah_semester_1, syahriah_semester_2
            $table->string('status')->default('belum_bayar'); // belum_bayar, dicicil, lunas
            $table->integer('nominal_bayar')->default(0); // to store partial amount if dicicil
            $table->string('catatan')->nullable(); // e.g. "Santri Pindahan"
            $table->timestamps();

            $table->unique(['santri_id', 'tahun_ajaran', 'kategori']);
        });

        Schema::create('libur_massals', function (Blueprint $table) {
            $table->id();
            $table->string('nama_libur');
            $table->date('start_date');
            $table->date('end_date');
            $table->timestamps();
        });

        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value');
            $table->timestamps();
        });

        Schema::create('activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
            $table->string('nama_aktor');
            $table->string('aksi');
            $table->text('details')->nullable();
            $table->timestamp('created_at')->useCurrent();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('activity_logs');
        Schema::dropIfExists('settings');
        Schema::dropIfExists('libur_massals');
        Schema::dropIfExists('keuangans');
        Schema::dropIfExists('hafalans');
        Schema::dropIfExists('presensis');
        Schema::dropIfExists('santris');
        Schema::dropIfExists('kelas');
    }
};
