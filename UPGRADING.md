## Upgrade guide

### From 2.1.1 to 3.0

ATTENTION: It is highly recommended that you generate a backup of your database before going throught the steps below, just to be safe in case something goes wrong.

#### Note before you start

A new configuration flag was introduced to instruct this package to still keep working correctly with non hashed API keys even after you upgrade this package to version `3.0`.

It is called `non_hashed_api_keys_mode`, if set to `true` this package will only work with non hashed API keys, if set to `false` it will only work with hashed API keys.

Make sure to publish the assets of this package in order to have access to the new configuration flag.

It is useful in case you have a very large `api_keys` table with tons of records, in such scenario running the new migration and hashing all existing keys can take a while.

By using that new configuration flag you can upgrade this package to version `3.0` and instruct it to still accept API keys that are not hashed yet, this should give you time to implement all changes needed on the database.

Once you finish applying the new table changes and get all existing API keys hashed, you can simply turn off that flag by using an environment variable, without needing to redeploy your app.

Check the updated 'keyable.php' configuration file for more details.

#### Step 1: `api_keys` table updates

- Add a new nullable string column called `name`.
- Modify the existing `key` column to increase its length to 255.

Sample code below.

```php
Schema::table('api_keys', function (Blueprint $table) {
    $table->string('name')->nullable();
    $table->string('key', 255)->change();
});
```

#### Step 2: Update your `composer.json` file

Update the version of this package in your `composer.json` file to `3.0`.

```json
"require": {
    // ...
    "givebutter/laravel-keyable": "^3.0",
    // ...
},
```

#### Step 3. Run composer update

```bash
composer update
```

#### Step 4. Hash existing API keys

A command was added to hash existing API keys that are not currently hashed, it will ensure existing API keys will keep working properly after upgrading.

```
php artisan api-key:hash
```

This command should be executed only once.

#### Step 5. Set "keyable.non_hashed_api_keys_mode" to false

This step is optional.

In case you are making use of the `non_hashed_api_keys_mode` configuration flag, make sure to turn it off by setting it to false, so that the package can correctly handle the hashed API keys.
