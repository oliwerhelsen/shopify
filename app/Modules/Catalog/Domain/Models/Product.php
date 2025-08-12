<?php

namespace App\Modules\Catalog\Domain\Models;

use Cviebrock\EloquentSluggable\Sluggable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\{BelongsToMany, HasMany};
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Laravel\Scout\Searchable;

class Product extends Model implements HasMedia
{
    use InteractsWithMedia, Searchable, Sluggable;

    protected $guarded = [];

    public function sluggable(): array
    {
        return [
            'slug' => [
                'source' => 'name'
            ]
        ];
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /** Relationer */
    public function variants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function options(): HasMany
    {
        return $this->hasMany(ProductOption::class)->orderBy('position');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class, 'category_product');
    }

    /** Media Library */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images')
            ->useDisk(config('filesystems.default'))
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp']);
    }

    /** Scout (valfritt) */
    public function toSearchableArray(): array
    {
        return [
            'id' => $this->id,
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'status' => $this->status,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
