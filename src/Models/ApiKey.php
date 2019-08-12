<?php

namespace Givebutter\LaravelKeyable\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ApiKey extends Model
{
    use SoftDeletes;

    protected $table = 'api_keys';
    protected $fillable = ['key', 'keyable_id', 'keyable_type', 'last_used_at'];
    protected $casts = [
	    'last_used_at' => 'datetime'
    ];

	/**
     * @return \Illuminate\Database\Eloquent\Relations\MorphTo
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
            $key = str_random(40);
        } while (self::keyExists($key));

        return $key;
    }

    /**
     * Get ApiKey record by key value
     *
     * @param string $key
     * @return bool
     */
    public static function getByKey($key)
    {
        return self::where('key', $key)->first();
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
        return self::where('key', $key)->withTrashed()->first() instanceof self;
    }
    
    /**
     * Mark key as used
     */
    public function markAsUsed() 
    {
	    $this->forceFill(['last_used_at' => $this->freshTimestamp()])->save();	    
    }    
}