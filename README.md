# vaibhavpandeyvpz/katora
Minimal implementation of [container-interop/container-interop](https://github.com/container-interop/container-interop) package, with addition of service extensions & declarative dependency resolution.

[![Build Status](https://img.shields.io/travis/vaibhavpandeyvpz/katora/master.svg?style=flat-square)](https://travis-ci.org/vaibhavpandeyvpz/katora)

Install
------
```bash
composer require vaibhavpandeyvpz/katora
```

Testing
------
``` bash
vendor/bin/phpunit
```

Usage
------
```php
<?php

require_once __DIR__ . '/vendor/autoload.php';

$container = new Katora\Container();

/**
 * Setting static values can be done in any way i.e., \ArrayAccess or method call
 */
// $container->add('config', array(
$container['config'] =  array(
    'db' => array(
        'dsn' => 'mysql:host=localhost;dbname=katora',
        'username' => 'root',
        'password' => null,
        'options' => array(
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ),
        'charset' => 'utf8'
    )
);

/**
 * Declared dependencies will be passed as arguments to callback.
 */
// $container->factory('pdo', function (/** Dependencies */ array $config)
$container->singleton('pdo', function (/** Dependencies */ array $config)
{
    return new PDO(
        $config['db']['dsn'],
        $config['db']['username'],
        $config['db']['password'],
        $config['db']['options']
    );
}, /** Dependencies */ 'config');

/**
 * First argument passed to callback will be created
 * service i.e., PDO then come the dependencies (optional)
 */
$container->extend('pdo', function (PDO $pdo, /** Dependencies */ array $config)
{
    $pdo->exec("SET NAMES {$config['db']['charset']}");
    // You must return the same or a new instance
    return $pdo;
}, /** Dependencies */ 'config');

/**
 * Later in code, fetch the service by name
 */
 /** @var PDO $pdo */
$pdo = $container['pdo'];
```

License
------
See [LICENSE.md](https://github.com/vaibhavpandeyvpz/katora/blob/master/LICENSE.md) file.
