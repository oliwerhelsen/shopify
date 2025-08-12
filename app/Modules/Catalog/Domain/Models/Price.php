<?php

namespace App\Modules\Catalog\Domain\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Casts\MoneyCast;

class Price extends Model
{
    protected $guarded = [];

    // Logiskt attribut "money" mappar mot amount/currency via MoneyCast
    protected $casts = [
        'money' => MoneyCast::class,
        'starts_at' => 'datetime',
        'ends_at' => 'datetime',
        'includes_tax' => 'boolean',
    ];

    // Accessor/Mutator för att exponera Money som "money"
    public function getMoneyAttribute()
    {
        // MoneyCast läser money_amount/money_currency – vi vill återanvända amount/currency
        // liten adapter: returnera array som MoneyCast kan tolka
        return ['amount' => $this->attributes['amount'] ?? 0, 'currency' => $this->attributes['currency'] ?? 'SEK'];
    }

    public function setMoneyAttribute($value): void
    {
        // Tillåt sättning via Money/array → mappar ner till amount/currency
        if ($value instanceof \App\Modules\Catalog\Domain\ValueObjects\Money) {
            $this->attributes['amount'] = $value->amount();
            $this->attributes['currency'] = $value->currency();
        } elseif (is_array($value)) {
            $this->attributes['amount'] = (int) $value['amount'];
            $this->attributes['currency'] = strtoupper($value['currency']);
        }
    }

    public function variant(): BelongsTo
    {
        return $this->belongsTo(ProductVariant::class, 'product_variant_id');
    }

    /* Scopes */
    public function scopeCurrency($q, string $currency)
    {
        return $q->where('currency', strtoupper($currency));
    }

    public function scopeList($q, ?string $priceList)
    {
        return $priceList ? $q->where('price_list', $priceList) : $q->whereNull('price_list');
    }

    public function scopeActive($q, ?\DateTimeInterface $at = null)
    {
        $now = $at?->format('Y-m-d H:i:s') ?? now();
        return $q->where(function ($q) use ($now) {
            $q->whereNull('starts_at')->orWhere('starts_at', '<=', $now);
        })->where(function ($q) use ($now) {
            $q->whereNull('ends_at')->orWhere('ends_at', '>=', $now);
        });
    }
}
