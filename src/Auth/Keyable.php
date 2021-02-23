<?php

namespace Givebutter\LaravelKeyable\Auth;

class Keyable
{
    /**
     * @var mixed
     */
	protected $policies;

    /**
     * @param $policies
     * @return mixed
     */
    public function registerKeyablePolicies($policies)
    {
        return $this->policies = $policies;
    }

    /**
     * @return mixed
     */
    public function getKeyablePolicies()
    {
	    return $this->policies;
    }
}
