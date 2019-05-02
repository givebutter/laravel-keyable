<?php
	
namespace Givebutter\LaravelKeyable\Auth;

use Illuminate\Auth\Access\Response;
use Givebutter\LaravelKeyable\Facades\Keyable;
use Illuminate\Auth\Access\AuthorizationException;

trait AuthorizesKeyableRequests
{
	/**
     * Authorize a request
     *
     * @return Response or throw exception
     */
    public function authorizeKeyable($ability, $object) 
    {
	    //Helpers
		$apiKey = request()->apiKey;
		$keyable = request()->keyable;
	    $policy = $this->getKeyablePolicy($object);
	    
	    //Run before function
	    $before = (new $policy)->before($apiKey, $keyable, $object);
	    if (!is_null($before) && $before) return new Response('');
	    
	    //Check Policy
	    if ($policy && (new $policy)->$ability($apiKey, $keyable, $object)) return new Response('');
	    	    
		//Throw exception
		throw new AuthorizationException('This action is unauthorized.');
    }
    
    /**
     * Get the associated policy
     *
     * @return policy
     */
    public function getKeyablePolicy($object)
    {
	    return Keyable::getKeyablePolicies()[get_class($object)] ?? null;
    }
    
}