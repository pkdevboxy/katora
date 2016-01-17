# vaibhavpandeyvpz/katora
Minimal implementation of [container-interop/container-interop](https://github.com/container-interop/container-interop) package, with addition of service extensions & easy dependency fetch.

[![Build Status](https://img.shields.io/travis/vaibhavpandeyvpz/katora/master.svg?style=flat-square)](https://travis-ci.org/vaibhavpandeyvpz/katora)

Install
------
```bash
composer require vaibhavpandeyvpz/katora
```

Define Values
------
```php
/**
 * Setting static values can be done by calling Katora\Container::set(...)
 */
$container->set('config', [
    'db' => [
        'dsn' => 'mysql:host=localhost;dbname=katora',
        'username' => 'root',
        'password' => null,
        'options' => [ PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION ],
        'charset' => 'utf8'
    ]
]);
```

Singletons
------
```php
/**
 * Dependencies can be fetched using $this context.
 */
$container->singleton('pdo', function ()
{
    /** @var Katora\Container $this */
    $config = $this->get('config');
    return new PDO(
        $config['db']['dsn'],
        $config['db']['username'],
        $config['db']['password'],
        $config['db']['options']
    );
});
```

Extend Services
------
```php
/**
 * Created service i.e., PDO will be passed as arguments
 */
$container->extend('pdo', function (PDO $pdo)
{
    /** @var Katora\Container $this */
    $config = $this->get('config');
    $pdo->exec("SET NAMES {$config['db']['charset']}");
    // You must return the same or a new instance
    return $pdo;
});
```

Usage
------
```php
/**
 * Later in code, fetch the service by id
 *
 * @var PDO $pdo */
 */
$pdo = $container->get('pdo');
```

License
------
See [LICENSE.md](https://github.com/vaibhavpandeyvpz/katora/blob/master/LICENSE.md) file.
