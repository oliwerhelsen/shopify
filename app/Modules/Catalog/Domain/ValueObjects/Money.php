<?php

namespace App\Modules\Catalog\Domain\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;

final class Money implements JsonSerializable
{
    /** Belopp i minor units (t.ex. öre) */
    private int $amount;
    /** ISO 4217-valuta, t.ex. "SEK" eller "EUR" */
    private string $currency;

    private function __construct(int $amount, string $currency)
    {
        $currency = strtoupper($currency);
        if (!preg_match('/^[A-Z]{3}$/', $currency)) {
            throw new InvalidArgumentException('Currency must be ISO-4217 (3 letters).');
        }
        $this->amount   = $amount;
        $this->currency = $currency;
    }

    /** Fabriksmetoder */
    public static function fromMinor(int $amount, string $currency = 'SEK'): self
    {
        return new self($amount, $currency);
    }

    /**
     * Skapa från major units (t.ex. kronor) — accepterar 123.45, "123.45", 123
     * OBS: avrundar till närmaste öre (half up).
     */
    public static function fromMajor(float|int|string $major, string $currency = 'SEK'): self
    {
        if (is_string($major)) {
            $major = str_replace(',', '.', $major);
        }
        $amountMinor = (int) round(((float) $major) * 100, 0, PHP_ROUND_HALF_UP);
        return new self($amountMinor, $currency);
    }

    /** Nollvärde */
    public static function zero(string $currency = 'SEK'): self
    {
        return new self(0, $currency);
    }

    /** Getters */
    public function amount(): int
    {
        return $this->amount;
    }          // i öre
    public function currency(): string
    {
        return $this->currency;
    }
    public function major(): float
    {
        return $this->amount / 100;
    }    // i kronor

    /** Jämförelser (samma valuta krävs) */
    public function equals(self $other): bool
    {
        $this->assertSameCurrency($other);
        return $this->amount === $other->amount;
    }

    public function greaterThan(self $other): bool
    {
        $this->assertSameCurrency($other);
        return $this->amount > $other->amount;
    }

    public function lessThan(self $other): bool
    {
        $this->assertSameCurrency($other);
        return $this->amount < $other->amount;
    }

    /** Aritmetik (immutabel) */
    public function add(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount + $other->amount, $this->currency);
    }

    public function subtract(self $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount - $other->amount, $this->currency);
    }

    /**
     * Multiplicera med ett tal (t.ex. moms 1.25 eller rabatt 0.9).
     * Resultatet avrundas till närmaste öre.
     */
    public function multiply(float $factor): self
    {
        $new = (int) round($this->amount * $factor, 0, PHP_ROUND_HALF_UP);
        return new self($new, $this->currency);
    }

    /**
     * Allokera proportioner, t.ex. [1,1,1] eller [50,30,20].
     * Returnerar en array av Money som summerar till originalet (hanterar restöre).
     */
    public function allocate(array $ratios): array
    {
        $total = array_sum($ratios) ?: 0;
        if ($total <= 0) {
            throw new InvalidArgumentException('Ratios must sum to > 0.');
        }

        $remainder = $this->amount;
        $results = [];
        foreach ($ratios as $i => $ratio) {
            $share = (int) floor($this->amount * ($ratio / $total));
            $results[$i] = new self($share, $this->currency);
            $remainder -= $share;
        }
        // fördela ev. resterande ören ett i taget
        $i = 0;
        while ($remainder > 0) {
            $results[$i] = $results[$i]->add(self::fromMinor(1, $this->currency));
            $remainder--;
            $i = ($i + 1) % count($results);
        }
        return $results;
    }

    /** Format, JSON, sträng */
    public function format(string $locale = 'sv_SE'): string
    {
        // enkel format utan NumberFormatter-dependency
        $major = number_format($this->major(), 2, ',', ' ');
        return "{$major} {$this->currency}";
    }

    public function __toString(): string
    {
        return number_format($this->major(), 2, '.', '') . " {$this->currency}";
    }

    public function jsonSerialize(): mixed
    {
        return [
            'amount'   => $this->amount,   // minor units
            'currency' => $this->currency,
            'major'    => $this->major(),  // convenience
        ];
    }

    /** Hjälpmetoder */
    private function assertSameCurrency(self $other): void
    {
        if ($this->currency !== $other->currency) {
            throw new InvalidArgumentException("Currency mismatch: {$this->currency} vs {$other->currency}");
        }
    }
}
