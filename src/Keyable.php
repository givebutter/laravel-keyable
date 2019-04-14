<?php
	
namespace Givebutter\LaravelKeyable;

use Givebutter\LaravelKeyable\Models\ApiKey;

trait Keyable
{
	
    public function apiKeys()
    {
        return $this->morphMany(ApiKey::class, 'keyable');
    }
    
    public function createApiKey()
    {
        return ApiKey::generate($this);
    }
    
}