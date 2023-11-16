<?php

namespace Givebutter\LaravelKeyable\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class ApiKey extends Model
{
    use SoftDeletes;

    public ?string $plainTextApiKey = null;

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
            if (is_null($apiKey->key)) {
                $apiKey->plainTextApiKey = self::generate();
                $apiKey->key = hash('sha256', $apiKey->plainTextApiKey);
            }
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
        $compatibilityMode = config('keyable.compatibility_mode', false);

        if ($compatibilityMode) {
            return $query->where(function (Builder $query) use ($key) {
                if ($this->isMissingId($key)) {
                    return $query->where('key', $key)
                        ->orWhere('key', hash('sha256', $key));
                }

                $id = $this->extractId($key);
                $key = $this->extractKey($key);

                return $query
                    ->where(function (Builder $query) use ($key, $id) {
                        return $query->where('key', $key)
                            ->where('id', $id);
                    })
                    ->orWhere(function (Builder $query) use ($key, $id) {
                        return $query->where('key', hash('sha256', $key))
                            ->where('id', $id);
                    });
            });
        }

        if ($this->isMissingId($key)) {
            return $query->where('key', hash('sha256', $key));
        }

        return $query->where('id', $this->extractId($key))
            ->where('key', hash('sha256', $this->extractKey($key)));
    }

    private function isMissingId(string $key): bool
    {
        return strpos($key, '|') === false;
    }

    private function extractId(string $key): string
    {
        return explode('|', $key, 2)[0];
    }

    private function extractKey(string $key): string
    {
        return explode('|', $key, 2)[1];
    }
}
