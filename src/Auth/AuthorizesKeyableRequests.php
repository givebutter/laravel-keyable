<?php
	
namespace Givebutter\LaravelKeyable\Auth;

use Illuminate\Auth\Access\Response;
use Illuminate\Auth\Access\AuthorizationException;

use Givebutter\LaravelKeyable\Facades\Keyable;

trait AuthorizesKeyableRequests
{

    public function authorizeKeyable($ability, $object) {
	    
	    //For Laravel user authentication
	    if (!request()->apiKey) return $this->authorize($ability, $object);
		
		//For Keyable authentication
		$keyable = request()->keyable;
		
	    //Get Policy
	    $policy = $this->getKeyablePolicy($object);
	    
	    //Check Policy
	    if (!$policy || (new $policy)->$ability($keyable, $object)) return new Response('');
	    	    
		//Throw exception
		throw new AuthorizationException('This action is unauthorized.');
		
    }
    
    public function getKeyablePolicy($object) {
	    
	    return Keyable::getKeyablePolicies()[get_class($object)] ?? null;
	    
    }
    
}