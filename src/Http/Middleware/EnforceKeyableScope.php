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
        $parameter = $route->parameters()[$parameterName];

        if (! is_object($parameter)) {
            $parameter = Arr::first($route->signatureParameters(UrlRoutable::class));
            $instance = app(Reflector::getParameterClassName($parameter));

            $routeBindingMethod = $route->allowsTrashedBindings()
                        ? 'resolveSoftDeletableRouteBinding'
                        : 'resolveRouteBinding';

            $instance->{$routeBindingMethod}($parameter, $route->bindingFieldFor($parameterName));
        }

        $childRouteBindingMethod = $route->allowsTrashedBindings()
                ? 'resolveSoftDeletableChildRouteBinding'
                : 'resolveChildRouteBinding';

        if (! $request->keyable->{$childRouteBindingMethod}(
            $parameterName,
            $parameter->id,
            $route->bindingFieldFor($parameterName)
        )) {
            throw (new ModelNotFoundException)->setModel(get_class($parameter), [$parameter->id]);
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
