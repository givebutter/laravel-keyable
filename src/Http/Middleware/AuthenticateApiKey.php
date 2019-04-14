<?php

namespace Givebutter\LaravelKeyable\Http\Middleware;

use Closure;
use Carbon\Carbon;
use Givebutter\LaravelKeyable\Models\ApiKey;

class AuthenticateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param Closure $next
     * @param  string|null $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
	    
	    //Check for Bearer token
	    $token = $request->bearerToken();
	    
	    //Check for presence of key
	    if (!$token) return $this->unauthorizedResponse();
	    
		//Get API key
		$apiKey = ApiKey::getByKey($token);
		
		//Validate key
        if (!($apiKey instanceof ApiKey)) return $this->unauthorizedResponse();
                    
        //Get the model
        $keyable = $apiKey->keyable;
        
        //Validate model
        if (!$keyable) return $this->unauthorizedResponse();
        
        //Update this api key's last_used_at and last_ip_address
        $apiKey->update(['last_used_at' => Carbon::now()]);
		
        //Attach the apikey object to the request
        $request->apiKey = $apiKey;
        $request->keyable = $keyable;
		
		//Return
        return $next($request);
        
    }

    protected function unauthorizedResponse()
    {
        return response([
            'error' => [
                'message' => 'Unauthorized'
            ]
        ], 401);
    }
}