# Points

A basic points implementation for gamification system in laravel

## Installation
Install package via composer

```bash
composer require "webbingbrasil/laravel-points=1.0.0"
```

Next, if you are using Laravel prior to 5.5, register the service provider in the providers array of your config/app.php configuration file:

```php
WebbingBrasil\Points\Providers\PointServiceProvider::class,
```

To get started, you'll need to publish the vendor assets and migrate:

```php
php artisan vendor:publish --provider="WebbingBrasil\Points\Providers\PointServiceProvider" && php artisan migrate
```

## Usage
Add our `HasPoints` trait to your model.
        
```php
<?php namespace App\Models;

use WebbingBrasil\Points\Traits\HasPoints;

class User extends Model
{
    use HasPoints;

    // ...
}
?>
```

