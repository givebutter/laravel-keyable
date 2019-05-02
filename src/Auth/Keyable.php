<?php
	
namespace Givebutter\LaravelKeyable\Auth;
  
class Keyable
{
	
	protected $policies;
	
    public function registerKeyablePolicies($policies)
    {
        return $this->policies = $policies;
    }
    
    public function getKeyablePolicies() 
    {
	    return $this->policies;
    }
    
}