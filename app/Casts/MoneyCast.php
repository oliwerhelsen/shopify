<?php

namespace App\Casts;

use App\Modules\Catalog\Domain\ValueObjects\Money;
use Illuminate\Contracts\Database\Eloquent\CastsAttributes;
use Illuminate\Database\Eloquent\Model;
use InvalidArgumentException;

class MoneyCast implements CastsAttributes
{
    /**
     * Get the value from the database and return a Money VO.
     */
    public function get(Model $model, string $key, $value, array $attributes): Money
    {
        $amountKey = "{$key}_amount";
        $currencyKey = "{$key}_currency";

        if (! isset($attributes[$amountKey], $attributes[$currencyKey])) {
            throw new InvalidArgumentException("Missing amount or currency for {$key}");
        }

        return Money::from(
            (int) $attributes[$amountKey],
            $attributes[$currencyKey]
        );
    }

    /**
     * Set the value in the database from a Money VO or array.
     */
    public function set(Model $model, string $key, $value, array $attributes): array
    {
        if ($value instanceof Money) {
            return [
                "{$key}_amount"   => $value->amount(),
                "{$key}_currency" => $value->currency(),
            ];
        }

        if (is_array($value) && isset($value['amount'], $value['currency'])) {
            return [
                "{$key}_amount"   => (int) $value['amount'],
                "{$key}_currency" => (string) $value['currency'],
            ];
        }

        throw new InvalidArgumentException('Value for MoneyCast must be a Money instance or array with amount and currency.');
    }
}
