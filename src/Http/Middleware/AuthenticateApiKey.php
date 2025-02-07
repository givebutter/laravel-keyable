<?php

namespace Givebutter\LaravelKeyable\Http\Middleware;

use Closure;
use Givebutter\LaravelKeyable\Models\ApiKey;

class AuthenticateApiKey
{
    /**
     * Handle an incoming request.
     *
     * @param \Illuminate\Http\Request $request
     * @param Closure                  $next
     * @param string|null              $mode
     * @param string|null              $guard
     *
     * @return mixed
     */
    public function handle($request, Closure $next, ?string $mode = null, $guard = null)
    {
        $forbidenRequestParams = ['apiKey', 'keyable'];
        
        // Check if request has forbidden params
        foreach ($forbidenRequestParams as $param) {
            if ($request->missing($param)) {
                continue;
            }

            $message = "Request param '{$param}' is not allowed.";

            if ($request->wantsJson()) {
                return response()->json(['message' => $message], 400);
            }

            return response($message, 400);
        }

        //Get API token from request
        $token = $this->getKeyFromRequest($request, $mode);

        //Check for presence of key
        if (! $token) {
            return $this->unauthorizedResponse();
        }

        //Get API key
        $apiKey = ApiKey::getByKey($token);

        //Validate key
        if (! ($apiKey instanceof ApiKey)) {
            return $this->unauthorizedResponse();
        }

        //Get the model
        $keyable = $apiKey->keyable;

        //Validate model
        if (config('keyable.allow_empty_models', false)) {
            if (! $keyable && (! is_null($apiKey->keyable_type) || ! is_null($apiKey->keyable_id))) {
                return $this->unauthorizedResponse();
            }
        } else {
            if (! $keyable) {
                return $this->unauthorizedResponse();
            }
        }

        //Attach the apikey object to the request
        $request->merge(['apiKey' => $apiKey]);
        if ($keyable) {
            $request->merge(['keyable' => $keyable]);
        }

        //Update last_used_at
        $apiKey->markAsUsed();

        //Return
        return $next($request);
    }

    protected function getKeyFromRequest($request, $mode)
    {
        $mode = $mode ?? config('keyable.mode', 'bearer');

        switch ($mode) {
            case 'bearer':
                return $request->bearerToken();
                break;
            case 'header':
                return $request->header(config('keyable.key-' . $mode, config('keyable.key', 'X-Authorization')));
                break;
            case 'parameter':
                return $request->input(config('keyable.key-' . $mode, config('keyable.key', 'api_key')));
                break;
        }
    }

    protected function unauthorizedResponse()
    {
        return response([
            'error' => [
                'message' => 'Unauthorized',
            ],
        ], 401);
    }
}
