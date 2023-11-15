## Upgrade guide

### From 2.1.1 to 3.0.0

ATTENTION: It is highly recommended that you generate a backup of your database before going through the steps below, just to be safe in case something goes wrong.

#### Step 1: `api_keys` table updates

Implement the following changes on your `api_keys` table.

- Add a new nullable string column called `name`.
- Modify the existing `key` column to increase its length from 40 to 64.

#### Step 2: Update the package to version 3.0.0

```bash
composer require givebutter/laravel-keyable:3.0.0
```

#### Step 3. Turn on `compatibility_mode`

A new configuration flag was introduced in the `keyable.php` config file on version `3.0.0`, it is called `compatibility_mode`, make sure to publish the package's config file to be able to access it.

By default it is set to `false`, but when it is set to `true` the package will handle both hashed and non hashed API keys, which should keep your application running smoothly while you complete all upgrade steps.

It is specially useful if you have a very large `api_keys` table, which could take a while to hash all existing API keys.

It points to an environment variable called `KEYABLE_COMPATIBILITY_MODE`, but you can update it to whatever you need of course.

Make sure to update `KEYABLE_COMPATIBILITY_MODE` to `true` if you want to make use of that feature.

#### Step 4. Hash existing API keys

A command was added to hash existing API keys that are not currently hashed, it will ensure existing API keys will continue working properly once you finish all upgrade steps.

```bash
php artisan api-key:hash
```

It is also possible to hash a single API key at a time, by passing an `--id` option.

```bash
php artisan api-key:hash --id=API_KEY_ID
```

Be very careful with this option, as each API key should be hashed only once.

Ideally you should only use it for testing and on your own API keys.

The command tries to avoid hashing an API key twice by comparing the length of the `key` column, if it is already 64 then the command understands the key is already hashed and won't do it again.

#### Step 5. Turn off compatibility mode

If you are making use of the compatibility mode, it can now be turned off by setting `KEYABLE_COMPATIBILITY_MODE` to `false`, it is not needed anymore.
