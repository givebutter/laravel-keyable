<?php

namespace Givebutter\LaravelKeyable\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use SoftDeletes;

    protected $table = 'api_keys';

    protected $fillable = [
        'key', 
        'keyable_id', 
        'keyable_type', 
        'name', 
        'last_used_at',
    ];

    protected $casts = [
        'last_used_at' => 'datetime',
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function (ApiKey $apiKey) {
            $apiKey->key = hash('sha256', $apiKey->key ?? self::generate());
        });
    }

    /**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
     */
    public function keyable()
    {
        return $this->morphTo();
    }

    /**
     * Generate a secure unique API key.
     *
     * @return string
     */
    public static function generate()
    {
        do {
            $key = Str::random(40);
        } while (self::keyExists($key));

        return $key;
    }

    /**
     * Get ApiKey record by key value.
     *
     * @param string $key
     *
     * @return bool
     */
    public static function getByKey($key)
    {
        return self::ofKey($key)->first();
    }

    /**
     * Check if a key already exists.
     *
     * Includes soft deleted records
     *
     * @param string $key
     *
     * @return bool
     */
    public static function keyExists($key)
    {
        return self::ofKey($key)
            ->withTrashed()
            ->first() instanceof self;
    }

    /**
     * Mark key as used.
     */
    public function markAsUsed()
    {
        return $this->forceFill([
            'last_used_at' => $this->freshTimestamp()
        ])->save();
    }

    public function scopeOfKey(Builder $query, string $key): Builder
    {
        if (strpos($key, '|') === false) {
            return $query->where('key', hash('sha256', $key));
        }

        [$id, $key] = explode('|', $key, 2);

        return $query->where('id', $id)->where('key', hash('sha256', $key));
    }
}
