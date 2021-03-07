<?php

namespace Givebutter\LaravelKeyable\Auth;

use Givebutter\LaravelKeyable\Facades\Keyable;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\Access\Response;

trait AuthorizesKeyableRequests
{
    /**
     * Authorize a request.
     *
     * @return Response or throw exception
     */
    public function authorizeKeyable($ability, $object)
    {
        $apiKey = request()->apiKey;
        $keyable = request()->keyable;

        if ($policy = $this->getKeyablePolicy($object)) {
            $policyClass = (new $policy());

            if (method_exists($policyClass, 'before')) {
                $before = $policyClass->before($apiKey, $keyable, $object);
                if (!is_null($before) && $before) {
                    return new Response('');
                }
            }

            if ($policyClass->$ability($apiKey, $keyable, $object)) {
                return new Response('');
            }
        }

        //Throw exception
        throw new AuthorizationException('This action is unauthorized.');
    }

    /**
     * Get the associated policy.
     *
     * @return policy
     */
    public function getKeyablePolicy($object)
    {
        return Keyable::getKeyablePolicies()[get_class($object)] ?? null;
    }
}
