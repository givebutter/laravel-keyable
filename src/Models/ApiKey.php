<?php

namespace Givebutter\LaravelKeyable\Models;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class ApiKey extends Model
{
    use SoftDeletes;

    protected $table = "api_keys";

    protected $fillable = ["key", "keyable_id", "keyable_type", "last_used_at"];

    protected $casts = [
        "last_used_at" => "datetime",
    ];

    public function __construct(array $attributes = [])
    {
        $identifier = config('keyable.identifier', 'bigint');

        if($identifier === 'uuid') {
            $this->keyType = 'string';
            $this->incrementing = false;
        }

        parent::__construct($attributes);
    }

    /**
     * The "booting" method of the model.
     */
    protected static function boot(): void
    {
        parent::boot();

        $identifier = config('keyable.identifier', 'bigint');

        static::creating(function (self $model) use ($identifier): void {
            // Automatically generate a UUID if using them, and not provided.
            if ($identifier === 'uuid' && empty($model->{$model->getKeyName()})) {
                $model->{$model->getKeyName()} = $model->generateUuid();
            }

            $model->key = self::generate();
        });
    }

    /**
     * @throws \Exception
     * @return string
     */
    protected function generateUuid(): string
    {
        $uuidVersion = config('keyable.uuid.version', 4);

        switch ($uuidVersion) {
            case 1:
                return Uuid::uuid1()->toString();
            case 4:
                return Uuid::uuid4()->toString();
        }

        throw new Exception("UUID version [{$uuidVersion}] not supported.");
    }

    /**
     * @return MorphTo
     */
    public function keyable()
    {
        return $this->morphTo();
    }

    /**
     * Generate a secure unique API key
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
     * Get ApiKey record by key value
     *
     * @param string $key
     * @return self
     */
    public static function getByKey($key)
    {
        /** @var ApiKey $apiKey */
        $apiKey = self::where('key', $key)->first();

        return $apiKey;
    }

    /**
     * Check if a key already exists
     *
     * Includes soft deleted records
     *
     * @param string $key
     * @return bool
     */
    public static function keyExists($key)
    {
        return self::where("key", $key)
      ->withTrashed()
      ->first() instanceof self;
    }

    /**
     * Mark key as used
     */
    public function markAsUsed()
    {
        $this->forceFill(["last_used_at" => $this->freshTimestamp()])->save();
    }
}
