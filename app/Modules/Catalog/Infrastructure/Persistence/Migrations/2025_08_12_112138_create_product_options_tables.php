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
        Schema::create('product_options', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_id')->constrained()->cascadeOnDelete();
            $table->string('name'); // t.ex. Färg
            $table->string('handle'); // t.ex. color
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['product_id', 'handle']);
        });

        Schema::create('product_option_values', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_option_id')->constrained()->cascadeOnDelete();
            $table->string('value'); // t.ex. Röd
            $table->string('code')->nullable(); // t.ex. RED
            $table->unsignedInteger('position')->default(0);
            $table->timestamps();

            $table->unique(['product_option_id', 'value']);
        });

        Schema::create('product_variant_option_values', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_variant_id')->constrained()->cascadeOnDelete();
            $table->foreignUuid('product_option_value_id')->constrained()->cascadeOnDelete();
            $table->unique(['product_variant_id', 'product_option_value_id']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('product_options');
        Schema::dropIfExists('product_option_values');
        Schema::dropIfExists('product_variant_option_values');
    }
};
