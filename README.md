# Laravel Keyable

Laravel Keyable is a package that allows you to add API Keys to any model. This allows you to associate incoming requests with their respective models. You can also use Policies to authorize requests.

[![Latest Stable Version](https://poser.pugx.org/givebutter/laravel-keyable/v/stable)](https://packagist.org/packages/givebutter/laravel-keyable) [![Total Downloads](https://poser.pugx.org/givebutter/laravel-keyable/downloads)](https://packagist.org/packages/givebutter/laravel-keyable) [![License](https://poser.pugx.org/givebutter/laravel-keyable/license)](https://packagist.org/packages/givebutter/laravel-keyable)

## Installation

Require the ```givebutter/laravel-keyable``` package in your ```composer.json``` and update your dependencies:

```bash
composer require givebutter/laravel-keyable
```

Publish and run the migration:
```bash
php artisan vendor:publish --provider="Givebutter\LaravelKeyable\KeyableServiceProvider" --tag="migrations"
```
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

The middleware will authenticate API requests, ensuring they contain an API key and are valid.

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

## Authorizing Requests

If you'd like to use this feature, add the controller class...

Laravel offers  [Authorization](https://laravel.com/docs/5.8/authorization) 

## Artisan Commands

Generate an API key:

```bash
php artisan api-key:generate --id=1 --type="App\Models\Account"
```

Delete an API key:
```bash
php artisan api-key:delete --id=12345
```

## Security

If you discover any security related issues, please email [liran@givebutter.com](mailto:liran@givebutter.com).

## License
Released under the [MIT](https://choosealicense.com/licenses/mit/) license. See [LICENSE](LICENSE.md) for more information.
