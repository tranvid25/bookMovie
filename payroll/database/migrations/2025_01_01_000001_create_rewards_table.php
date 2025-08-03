<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('rewards', function (Blueprint $table) {
            $table->id();
            $table->string('ten_qua');
            $table->text('mo_ta');
            $table->string('hinh_anh')->nullable();
            $table->integer('so_diem_can');
            $table->integer('so_luong_con')->default(0);
            $table->enum('trang_thai', ['active', 'inactive'])->default('active');
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('rewards');
    }
}; 