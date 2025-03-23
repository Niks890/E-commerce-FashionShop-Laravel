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
        Schema::create('customers', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 200);
            $table->string('phone', 15)->unique()->nullable();
            $table->string('address', 200)->nullable();
            $table->string('email', 100)->unique();
            $table->string('username', 100)->unique()->nullable();
            $table->string('password')->nullable();
            $table->tinyInteger('sex')->default(0);
            $table->string('image')->nullable();
            $table->string('platform_id')->nullable(); //Đăng nhập bằng nền tảng gì đó (google, facebook,...)
            $table->string('platform_name')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};
