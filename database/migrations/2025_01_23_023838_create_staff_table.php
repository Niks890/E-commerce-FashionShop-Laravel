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
        Schema::create('staff', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name', 200);
            $table->string('phone', 15)->unique();
            $table->string('address', 200);
            $table->string('email', 100)->unique();
            $table->tinyInteger('sex')->default(0);
            $table->string('username', 100)->unique();
            $table->string('password');
            $table->string('position', 50);
            $table->string('status');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('staff');
    }
};
