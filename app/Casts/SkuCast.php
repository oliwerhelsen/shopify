<?php

namespace App\Casts;

use App\Modules\Catalog\Domain\ValueObjects\Sku;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class SkuCast implements CastsAttributes
{
    public function get(Model $model, string $key, $value, array $attributes): Sku
    {
        return Sku::fromString((string) $value);
    }

    public function set(Model $model, string $key, $value, array $attributes): string
    {
        if ($value instanceof Sku) {
            return (string) $value;
        }

        if (is_string($value)) {
            return (string) Sku::fromString($value);
        }

        throw new InvalidArgumentException('Value for SkuCast must be a string or Sku instance.');
    }
}
