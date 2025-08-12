<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('prices', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->foreignUuid('product_variant_id')->constrained()->cascadeOnDelete();

            // Money (minor units + ISO)
            $table->integer('amount');        // i öre
            $table->char('currency', 3);      // ISO 4217, t.ex. SEK

            // Metadata
            $table->boolean('includes_tax')->default(false);
            $table->string('price_list')->nullable(); // t.ex. 'default', 'b2b', 'vip'
            $table->timestamp('starts_at')->nullable();
            $table->timestamp('ends_at')->nullable();

            $table->timestamps();

            // Hjälpindex: aktivt pris per variant/valuta/lista och tid
            $table->index(['product_variant_id', 'currency', 'price_list', 'starts_at', 'ends_at']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('prices');
    }
};
