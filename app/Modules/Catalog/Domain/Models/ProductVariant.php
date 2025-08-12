<?php

namespace App\Modules\Catalog\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};
use App\Casts\MoneyCast;
use App\Casts\SkuCast;

class ProductVariant extends Model
{
    protected $guarded = [];

    protected $casts = [
        'sku'   => SkuCast::class,     // sparas i kolumnen 'sku'
        'price' => MoneyCast::class,   // använder price_amount + price_currency
    ];

    // Mappar den logiska "price"-casten till backing columns
    protected function setAttribute($key, $value)
    {
        if ($key === 'price') {
            // MoneyCast hanterar set/get via $casts
            return parent::setAttribute($key, $value);
        }
        return parent::setAttribute($key, $value);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function optionValues(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductOptionValue::class,
            'product_variant_option_values'
        );
    }
}
