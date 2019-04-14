<?php
	
namespace Givebutter\LaravelKeyable;
  
class KeyableClass
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