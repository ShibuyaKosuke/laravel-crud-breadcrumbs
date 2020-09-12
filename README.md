# laravel-crud-breadcrumbs

Breadcrumbs package for Laravel7+

inspired by [dwightwatson/breadcrumbs](https://github.com/dwightwatson/breadcrumbs)

## Install

```
composer require shibuyakosuke/laravel-crud-breadcrumbs
```

## Publish assets

```
php artisan vendor:publish --tag=breadcrumbs
```

## Usage

Create a new file at routes/breadcrumbs.php to define your breadcrumbs. By default the package will work with named routes which works with resourceful routing. However, you're also free to define routes by the controller action/pair.

```php
use App\Models\User;

Breadcrumbs::for('home', function ($trail) {
    $trail->add('Home', route('home'));
});

Breadcrumbs::for('users.index', function ($trail) {
    $trail->parent('home');
    $trail->add('Users', route('users.index'));
});

Breadcrumbs::for('users.show', function ($trail, User $user) {
    $trail->parent('users.index');
    $trail->add($user->name, route('users.show', $user));
});

Breadcrumbs::for('users.edit', function ($trail, User $user) {
    $trail->parent('users.show', $user);
    $trail->add('Edit', route('users.edit', $user));
});
```

## Rendering the breadcrumbs

In your view file, you simply need to call the render() method wherever you want your breadcrumbs to appear. It's that easy. If there are no breadcrumbs for the current route, then nothing will be returned.

```
{{ Breadcrumbs::render() }}
```

You don't need to escape the content of the breadcrumbs, it's already wrapped in an instance of Illuminate\Support\HtmlString so Laravel knows just how to use it.
