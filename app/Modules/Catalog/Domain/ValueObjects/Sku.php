<?php

namespace App\Modules\Catalog\Domain\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;

/**
 * SKU (Stock Keeping Unit) – unikt produktartikelnummer.
 *
 * - Immutable
 * - Normaliserar format (t.ex. versaler, inga mellanslag)
 * - Validerar mot tillåtet mönster
 */
final class Sku implements JsonSerializable
{
    private string $value;

    private function __construct(string $value)
    {
        $value = strtoupper(trim($value));

        // Tillåt A-Z, 0-9, bindestreck, understreck, punkt
        if (!preg_match('/^[A-Z0-9\-_\.]+$/', $value)) {
            throw new InvalidArgumentException("Invalid SKU format: {$value}");
        }

        if (strlen($value) > 64) {
            throw new InvalidArgumentException("SKU too long (max 64 characters).");
        }

        $this->value = $value;
    }

    public static function fromString(string $value): self
    {
        return new self($value);
    }

    public function value(): string
    {
        return $this->value;
    }

    public function equals(self $other): bool
    {
        return $this->value === $other->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function jsonSerialize(): mixed
    {
        return $this->value;
    }
}
