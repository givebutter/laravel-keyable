# Laravel Keyable

Laravel Keyable is a package that allows you to add API Keys to any model. This allows you to associate incoming requests with their respective models. You can also use Policies to authorize requests.

[![Latest Stable Version](https://poser.pugx.org/givebutter/laravel-keyable/v/stable)](https://packagist.org/packages/givebutter/laravel-keyable) [![Total Downloads](https://poser.pugx.org/givebutter/laravel-keyable/downloads)](https://packagist.org/packages/givebutter/laravel-keyable) [![License](https://poser.pugx.org/givebutter/laravel-keyable/license)](https://packagist.org/packages/givebutter/laravel-keyable)

## Installation

Require the ```givebutter/laravel-keyable``` package in your ```composer.json``` and update your dependencies:

```bash
composer require givebutter/laravel-keyable
```

Publish the migration and config files:
```bash
php artisan vendor:publish --provider="Givebutter\LaravelKeyable\KeyableServiceProvider"
```

Run the migration:
```bash
php artisan migrate
```

## Usage

Add the ```Givebutter\LaravelKeyable\Keyable``` trait to your model(s):

```php
use Illuminate\Database\Eloquent\Model;
use Givebutter\LaravelKeyable\Keyable;

class Account extends Model
{
    use Keyable;

    // ...
}
```

Add the ```auth.apiKey``` middleware to the ```mapApiRoutes()``` function in your ```App\Providers\RouteServiceProvider``` file:

```php
// ...

protected function mapApiRoutes()
{
    Route::prefix('api')
        ->middleware(['api', 'auth.apikey'])
	->namespace($this->namespace . '\API')
	->group(base_path('routes/api.php'));
}

// ...
```

The middleware will authenticate API requests, ensuring they contain an API key that is valid.

### Accessing keyable models in your controllers
The model associated with the key will be attached to the incoming request as ```keyable```:

```php
use App\Http\Controllers\Controller;

class FooController extends Controller {

    public function index(Request $request)
    {
        $model = $request->keyable;

        // ...
    }

}
```
Now you can use the keyable model to scope your associated API resources, for example:
```php
return $model->foo()->get();
```

### Keys Without Models

Sometimes you may not want to attach a model to an API key (if you wanted to have administrative access to your API). By default this functionality is turned off:

```php
<?php

return [

    'allow_empty_models' => true

];
```

## Making Requests

By default, laravel-keyable uses bearer tokens to authenticate requests. Attach the API key to the header of each request:

```
Authorization: Bearer <key>
```

You can change where the API key is retrieved from by altering the setting in the `keyable.php` config file. Supported options are: `bearer`, `header`, and `parameter`.
```php
<?php

return [

    'mode' => 'header',

    'key' => 'X-Authorization',

];
```

Need to pass the key as a URL parameter? Set the mode to `parameter` and the key to the string you'll use in your URL:
```php
<?php

return [

    'mode' => 'parameter',

    'key' => 'api_key'

];
```
Now you can make requests like this:
```php
https://example.com/api/posts?api_key=<key>
```

## Authorizing Requests

Laravel offers a great way to perform [Authorization](https://laravel.com/docs/5.8/authorization) on incoming requests using Policies. However, they are limited to authenticated users. We replicate that functionality to let you authorize requests on any incoming model.

To begin, add the `AuthorizesKeyableRequests` trait to your base `Controller.php` class:

```php
<?php

namespace App\Http\Controllers;

// ...

use Givebutter\LaravelKeyable\Auth\AuthorizesKeyableRequests;

class Controller extends BaseController
{
    use AuthorizesKeyableRequests;
}
```

Next, create the `app/Policies/KeyablePolicies` folder and create a new policy:

```php
<?php

namespace App\Policies\KeyablePolicies;

use App\Models\Post;
use Illuminate\Database\Eloquent\Model;
use Givebutter\LaravelKeyable\Models\ApiKey;

class PostPolicy {

    public function view(ApiKey $apiKey, Model $keyable, Post $post) {
    	return !is_null($keyable->posts()->find($post->id));
    }

}
```

Lastly, register your policies in `AuthServiceProvider.php`:

```php
<?php

namespace App\Providers;

// ...

use App\Models\Post;
use App\Policies\KeyablePolicies\PostPolicy;
use Givebutter\LaravelKeyable\Facades\Keyable;

class AuthServiceProvider extends ServiceProvider
{

    // ...

    protected $keyablePolicies = [
        Post::class => PostPolicy::class
    ];

    public function boot(GateContract $gate)
    {
        // ...
        Keyable::registerKeyablePolicies($this->keyablePolicies);
    }

}
```

In your controller, you can now authorize the request using the policy by calling `$this->authorizeKeyable(<ability>, <model>)`:

```php
<?php

namespace App\Http\Controllers\PostController;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class PostController extends Controller {

    public function show(Post $post) {
        $this->authorizeKeyable('view', $post);
        // ...
    }

}
```

## Keyable Model Scoping

When using implicit model binding, you may wish to scope the first model such that it must be a child of the keyable model. Consider an example where we have a post resource:

```php
use App\Models\Post;

Route::get('/posts/{post}', function (Post $post) {
    return $post;
});
```

You may instruct the package to apply the scope by invoking the `keyableScoped` method when defining your route:

```php
use App\Models\Post;

Route::get('/posts/{post}', function (Post $post) {
    return $post;
})->keyableScoped();
```

The benefits of applying this scope are two-fold. First, models not belonging to the keyable model are caught before the controller. That means you don't have to handle this repeatedly in the controller methods. Second, models that don't belong to the keyable model will trigger a 404 response instead of a 403, keeping information hidden about other users.

You may use this in tandem with Laravel's scoping to ensure the entire heirarchy has a parent-child relationship starting with the keyable model:

```php
use App\Models\Post;
use App\Models\User;

Route::get('/users/{user}/posts/{post}', function (User $user, Post $post) {
    return $post;
})->scopeBindings()->keyableScoped();
```

## Artisan Commands

Generate an API key:

```bash
php artisan api-key:generate --id=1 --type="App\Models\Account" --name="My api key"
```

Delete an API key:
```bash
php artisan api-key:delete --id=12345
```

## Security

If you discover any security related issues, please email [liran@givebutter.com](mailto:liran@givebutter.com).

## License
Released under the [MIT](https://choosealicense.com/licenses/mit/) license. See [LICENSE](LICENSE.md) for more information.
