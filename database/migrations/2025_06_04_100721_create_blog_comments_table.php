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
        Schema::create('blog_comments', function (Blueprint $table) {
            $table->id('comment_id')->autoIncrement();
            $table->text('content');
            $table->enum('status', ['Hidden', 'Visible'])->default('Visible');
            $table->unsignedInteger('parent_id')->nullable()->references('comment_id')->on('blog_comments');
            $table->unsignedInteger('blog_id');
            $table->foreign('blog_id')->references('id')->on('blogs');
            $table->unsignedInteger('customer_id');
            $table->foreign('customer_id')->references('id')->on('customers');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('blog_comments');
    }
};
