## Upgrade guide

### From 2.1.1 to 3.0.0

ATTENTION: It is highly recommended that you generate a backup of your database before going through the steps below, just to be safe in case something goes wrong.

#### Step 1: `api_keys` table updates

Implement the following changes on your `api_keys` table.

- Add a new nullable string column called `name`.
- Modify the existing `key` column to increase its length from 40 to 255.

#### Step 2: Update this package to version 3.0.0

```bash
composer require givebutter/laravel-keyable:3.0.0
```

#### Step 3. Hash existing API keys

A command was added to hash existing API keys that are not currently hashed, it will ensure existing API keys will continue working properly once you finish all upgrade steps.

```
php artisan api-key:hash
```

This command should be executed only once.

#### Step 4. Set `keyable.compatibility_mode` to false

A new configuration flag was introduced in the `keyable.php` config file on version `3.0.0`, it is called `compatibility_mode`, make sure to publish this package's config file to be able to access it.

By default it points to an environment variable called `COMPATIBILITY_MODE`, but you can update it to whatever you need of course.

Update your environment variable (that points to the new flag mentioned above) to `false`, this will instruct this package to only handle hashed API keys.
