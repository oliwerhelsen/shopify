<?php

namespace App\Modules\Catalog\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsTo, BelongsToMany};
use Spatie\Multitenancy\Models\Concerns\UsesTenantConnection;

class ProductOptionValue extends Model
{
    use HasUuids, UsesTenantConnection;

    protected $guarded = [];

    public function option(): BelongsTo
    {
        return $this->belongsTo(ProductOption::class, 'product_option_id');
    }

    public function variants(): BelongsToMany
    {
        return $this->belongsToMany(
            ProductVariant::class,
            'product_variant_option_values'
        );
    }
}
