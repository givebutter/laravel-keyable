<?php

namespace Givebutter\LaravelKeyable\Http\Middleware;

use Closure;
use Illuminate\Support\Arr;
use Illuminate\Support\Reflector;
use Illuminate\Contracts\Routing\UrlRoutable;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class EnforceKeyableScope
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure                  $next
     * @param string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        $route = $request->route();

        if (empty($route->parameterNames())) {
            return $next($request);
        }

        $parameterName = $route->parameterNames()[0];
        $parameterValue = $route->originalParameters()[$parameterName];
        $parameter = Arr::first($route->signatureParameters(UrlRoutable::class));
        $instance = app(Reflector::getParameterClassName($parameter));

        $childRouteBindingMethod = $route->allowsTrashedBindings()
                ? 'resolveSoftDeletableChildRouteBinding'
                : 'resolveChildRouteBinding';

        if (! $request->keyable->{$childRouteBindingMethod}(
            $parameterName,
            $parameterValue,
            $route->bindingFieldFor($parameterName)
        )) {
            throw (new ModelNotFoundException)->setModel(get_class($instance), [$parameterValue]);
        }

        return $next($request);
    }

    protected static function getParameterName($name, $parameters)
    {
        if (array_key_exists($name, $parameters)) {
            return $name;
        }

        if (array_key_exists($snakedName = Str::snake($name), $parameters)) {
            return $snakedName;
        }
    }
}
