# log-php

A simple PHP module for logging.

This provides some commonly-used logging functionality, like being able to relay LoggerInterface functionality between objects.

This is not and is not intended to be anything like a full-featured logging framework like (the excellent) Monolog. It mostly gets used to provide a default logging option for other libraries, and for catching and checking log messages during testing.

## Installation

You can require it directly with Composer:

```bash
composer require jdwx/log
```

Or download the source from GitHub: https://github.com/jdwx/log-php.git

## Requirements

This module requires PHP 8.2 or later.

## Usage

To use this module, you can instantiate one of the logger objects directly:

```php
$logger = new \JDWX\Log\StderrLogger();
$logger->info( 'Hello, world!' );
```

Or you can use the RelayLoggerTrait to provide a LoggerInterface on your object while only having to implement a single method:

```php
class MyClass implements \Psr\Log\LoggerInterface {
    use \JDWX\Log\RelayLoggerTrait;

    public function log( int $level, Stringable|string $message, 
                         array $context = [] ) : void {
        echo "Log message: {$message}\n";
    }

}

$x = new MyClass();
$x->info( 'Hello, world!' );
```

There are many unit tests for this module which provide additional examples of usage.

## Stability

This main module is considered stable and is used in production code. The test coverage is complete.

The Telemetry code is considered experimental and may change in future releases. (When it stabilizes, it will either be moved to a separate module or a major version increment will occur.)

## History

This module was refactored from [jdwx/app](https://github.com/jdwx/app) in January 2025 to better support web-based applications.

Rudimentary telemetry functionality was added in December 2025.