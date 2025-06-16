<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware; // Make sure this 'use' statement is present

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) { // <<< ADD OR MODIFY THIS SECTION
        // Register your route middleware aliases here
        $middleware->alias([
            'auth'       => \Illuminate\Auth\Middleware\Authenticate::class, // Example of existing alias
            'guest'      => \Illuminate\Auth\Middleware\RedirectIfAuthenticated::class, // Example
            'verified'   => \Illuminate\Auth\Middleware\EnsureEmailIsVerified::class, // Breeze might add this definition here or expect it.
            // You might see other default aliases here depending on your setup.

            'admin'      => \App\Http\Middleware\CheckAdminRole::class, // << ADD THIS LINE FOR YOUR MIDDLEWARE
        ]);

        // You can also register global middleware, or append to groups like 'web' or 'api'
        // For example, if Breeze or another package added middleware to the 'web' group:
        // $middleware->web(append: [
        //     \App\Http\Middleware\EncryptCookies::class, // This is just an example of syntax
        // ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        // ...
    })->create();