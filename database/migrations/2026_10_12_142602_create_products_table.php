<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('products', function (Blueprint $table) {
            $table->id('product_id');

            $table->foreignId('category_id')
                ->constrained('categories', 'category_id')
                ->restrictOnDelete();

            $table->string('product_name', 150);
            $table->string('barcode', 50)->nullable()->unique();
            $table->string('unit', 30);

            $table->decimal('cost_price', 10, 2);
            $table->decimal('selling_price', 10, 2);

            $table->unsignedInteger('current_stock')->default(0);
            $table->unsignedInteger('minimum_stock')->default(0);

            $table->text('description')->nullable();

            $table->enum('status', ['active', 'inactive'])
                ->default('active');

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
