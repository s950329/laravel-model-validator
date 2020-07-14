<h1 align="center"> Laravel Model Validator </h1>

An Eloquent way to validate Eloquent Models Value

## Installing

```shell
composer require leochien/laravel-model-validator
```

## Configuration

##### With Configuration File (Optional)

> Registering the service provider will give you access to the `php artisan model:validator {model}` command as well as allow you to publish the configuration file.  Registering the service provider is not required and only needed if you want to change the default namespace or use the artisan command

(Laravel 5.5 below) After installing the Model Validator library, register the `ModelValidator\ServiceProvider::class` in your `config/app.php` configuration file:

```php
'providers' => [
    // Other service providers...

    ModelValidator\ServiceProvider::class,
],
```

Copy the package config to your local config with the publish command:

```bash
php artisan vendor:publish --provider="ModelValidator\ServiceProvider"
```

In the `config/modelvalidator.php` config file.  Set the namespace your model validators will reside in:

```php
'namespace' => "App\\ModelValidators\\",
```

## Usage

### Basic

- The `getData` and `getRules` methods are necessary
- You can optionally add `getMessage` and `getAttributes` methods for custom message and attribute name.(see [Laravel Validation Document](https://laravel.com/docs/5.1/validation))

First you would use artisan console to create an Validator

```shell
php artisan model:validator Post
```

It will create an `PostValidator` in your Validators folder (default `app/ModelValidators`)

Then You would use the following methods:

```php
<?php

namespace App\ModelValidators;

use ModelValidator\ModelValidator;
use App\Post;

class SubmitPostValidator extends ModelValidator
{
    protected $post;

        public function __construct(Post $post)
        {
            $this->post = $post;
        }

        protected function getData()
        {
            return $this->post->with('user')->toArray();
        }

        protected function getRules()
        {
            return [
                'content' => 'required|string|min:100',
                'user.email_verified' => 'required|accepted'
            ];
        }
}
```

Then just use the Validator where ever you want:

```php

class PostController extends Controller

...

public function submit(Post $post)
{
    (new SubmitPostValidator($post))->validate();
}

...

```

If there are validation errors, it will throw `ValidationException` and return `422` status code, just like `$this->validate` method in controller

### Using Validatable Trait

You can implement the `ModelValidator\Vaidatable` trait on any Eloquent model:

```php
<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use ModelValidator\Validatable;

class Post extends Model
{
    use Validatable;
}
```

This gives you access to the validate() method that accepts an validator:

```php
use App\ModelValidators\SubmitPostValidator;

class UserController extends Controller
{
    public function submit(Post $post)
    {
        $post->validate(SubmitPostValidator::class);
        
        ...
    }
}
```
 
It's shorter and more clear.

## Contributing

You can contribute in one of three ways:

1. File bug reports using the [issue tracker](https://github.com/leochien/laravel-model-validator/issues).
2. Answer questions or fix bugs on the [issue tracker](https://github.com/leochien/laravel-model-validator/issues).
3. Contribute new features or update the wiki.

_The code contribution process is not very formal. You just need to make sure that you follow the PSR-0, PSR-1, and PSR-2 coding guidelines. Any new code contributions must be accompanied by unit tests where applicable._

## License

MIT