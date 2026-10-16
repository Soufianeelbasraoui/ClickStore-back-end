<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('category')->default('Général');
            $table->decimal('new_price', 10, 2)->default(150.00);
            $table->string('prev_price')->nullable();
            $table->text('img')->nullable();
            $table->decimal('rating', 3, 1)->default(4.5);
            $table->string('reviews')->default('1k');
            $table->boolean('in_stock')->default(true);
            $table->text('description')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
