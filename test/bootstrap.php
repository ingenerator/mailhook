<?php

declare(strict_types=1);

\error_reporting(E_ALL);
\ini_set('display_errors', 1);

// Currently phpunit's default error handling doesn't properly catch warnings / errors from data providers
// https://github.com/sebastianbergmann/phpunit/issues/2449
\set_error_handler(
    function ($severity, $message, $file, $line): true {
        if (error_reporting() & $severity) {
            throw new ErrorException($message, 0, $severity, $file, $line);
        }

        // This error has been silenced locally, ignore it
        return true;
    },
);
