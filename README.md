[![Build Status](https://travis-ci.org/GeneaLabs/laravel-governor-tests.svg?branch=master)](https://travis-ci.org/GeneaLabs/laravel-governor-tests)
 [![Coverage Status](https://coveralls.io/repos/GeneaLabs/laravel-governor-tests/badge.svg?branch=master&service=github)](https://coveralls.io/github/GeneaLabs/laravel-governor-tests?branch=master)

![governor for laravel](https://cloud.githubusercontent.com/assets/1791050/9620997/05b36650-50d6-11e5-864b-f15bd9622d08.jpg)

## Goal
Provide a simple method of managing ACL in a Laravel application built on the Laravel Authorization functionality.
By leveraging Laravel's native Authorization functionality there is no additional learning or implementation curve. All
you need to know is Laravel, and you will know how to use Governor for Laravel.

## Documentation
Please see https://governor.forlaravel.com for complete documentation.

## Installation
```sh
composer require genealabs/laravel-governor:^0.1.0
```

And then add the service providers and aliases to your app.php config file:
```php
	// 'providers' => [
        Collective\Html\HtmlServiceProvider::class,
		GeneaLabs\LaravelGovernor\Providers\LaravelGovernorServiceProvider::class,
    // ],
    // 'aliases' => [
        'Form' => Collective\Html\FormFacade::class,
        'Html' => Collective\Html\HtmlFacade::class,
    // ],
```

Before we can get started, we need to update the database by running the migrations and data seeders:
```sh
php artisan migrate --path=vendor/genealabs/laravel-governor/database/migrations
php artisan db:seed --class=LaravelGovernorDatabaseSeeder
```

Now we need to make the assets and configuration available:
```sh
php artisan vendor:publish --tag=genealabs-laravel-governor --force
```

## Configuration
Once you have published the assets, you will be able to customize the configuration of Governor for Laravel in
`/app/config/genealabs-laravel-governor.php`. (See the Examples section for what the default config file looks like.)
There are only three aspects to this:
- The master layout view (Blade template), by default it includes a bare-bones layout. Customizing this to your own view
  lets it adopt your site's theme (as long as it is a Bootstrap theme).
- The blade section used to display body content in your layout template. Change this to what your blade layout template
  uses.
- The field you want to use as a display name field. This defaults to `name`, but you can use email, or any other field
  in the User model (you can also create your own custom attribute getter to concatenate fields, etc.).

## Implementation
### Admin Views
The easiest way to integrate Governor for Laravel into your app is to add the menu items to the relevant section of your
 app's menu (make sure to restrict access appropriately using the Laravel Authorization methods). The following routes
 can be added:
- Entity Management: `genealabs.laravel-governor.entities`
- Role Management: `genealabs.laravel-governor.roles`
- User-Role Assignments: `genealabs.laravel-governor.assignments`

### 403 Unauthorized
We recommend making a custom 403 error page to let the user know they don't have access. Otherwise the user will just
see the Symfony Whoops error message.

## Examples
### Migration
The following migration should be a good starting point, if not provide all the functionality you need to add a
`created_by` column to all your tables. Customize as necessary.
```php
  use Illuminate\Database\Schema\Blueprint;
  use Illuminate\Database\Migrations\Migration;

  class AddCreatedByToAllTables extends Migration
  {
      public function up()
      {
          $user = app(config('auth.model'));
          $userIdFieldName = $user->getKeyName();
          $userTableName = $user->getTable();
          $tables = DB::table('information_schema.tables')
              ->where('table_schema', env('DB_DATABASE'))
              ->where('table_type', 'BASE TABLE')
              ->select(['table_name'])
              ->get();

          foreach ($tables as $tableInfo) {
              if (Schema::hasColumn($tableInfo->table_name, 'created_by')) {
                  throw new Exception('The `created_by` column already exists in one of your tables. Please fix the conflict and try again. This migration has not been run.');
              }
          }

          foreach ($tables as $tableInfo) {
              Schema::table($tableInfo->table_name, function(Blueprint $table) use ($userIdFieldName, $userTableName)
              {
                  $table->integer('created_by')->unsigned()->nullable();
                  $table->foreign('created_by')->references($userIdFieldName)->on($userTableName)->onDelete('cascade');
              });
          }
      }

      public function down()
      {
          $tables = DB::table('information_schema.tables')
              ->where('table_schema', env('DB_DATABASE'))
              ->where('table_type', 'BASE TABLE')
              ->select(['table_name'])
              ->get();

          foreach ($tables as $tableInfo) {
              if (Schema::hasColumn($tableInfo->table_name, 'created_by')) {
                  Schema::table($tableInfo->table_name, function(Blueprint $table) use ($tableInfo)
                  {
                      $table->dropForeign($tableInfo->table_name . '_created_by_foreign');
                      $table->dropColumn('created_by');
                  });
              }
          }
      }
  }
```

### Policy
```php
<?php namespace App\Policies;

use App\MyModel;
use App\User;
use GeneaLabs\LaravelGovernor\Policies\LaravelGovernorPolicy;

class MyModelPolicy extends LaravelGovernorPolicy
{
    public function create(User $user, MyModel $myModel)
    {
        return $this->validatePermissions($user, 'create', 'myModel', $myModel->created_by);
    }

    public function edit(User $user, MyModel $myModel)
    {
        return $this->validatePermissions($user, 'edit', 'myModel', $myModel->created_by);
    }

    public function view(User $user, MyModel $myModel)
    {
        return $this->validatePermissions($user, 'view', 'myModel', $myModel->created_by);
    }

    public function inspect(User $user, MyModel $myModel)
    {
        return $this->validatePermissions($user, 'inspect', 'myModel', $myModel->created_by);
    }

    public function remove(User $user, MyModel $myModel)
    {
        return $this->validatePermissions($user, 'remove', 'myModel', $myModel->created_by);
    }
}
```

### Config
```php
<?php

return [
    'layoutView' => 'genealabs-laravel-governor::layout',
    'bladeContentSection' => 'content',
    'displayNameField' => 'name',
];
```
