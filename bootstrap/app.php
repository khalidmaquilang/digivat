<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Infinitypaul\Idempotency\Middleware\EnsureIdempotency;

return Application::configure(basePath: dirname(__DIR__))
    ->withProviders()
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        channels: __DIR__.'/../routes/channels.php',
        health: '/up',
        then: function (): void {
            $featuresPath = app_path('Features');
            $iterator = new RecursiveIteratorIterator(new RecursiveDirectoryIterator($featuresPath));

            foreach ($iterator as $file) {
                if (
                    $file->isFile()
                    && $file->getFilename() === 'api.php'
                ) {
                    $api_routes = $file->getPathname();

                    $middlewares = [];

                    if (app()->isProduction()) {
                        $middlewares[] = EnsureIdempotency::class;
                    }

                    Route::prefix('api/v1')
                        ->name('api.')
                        ->middleware($middlewares)
                        ->group($api_routes);
                }
            }
        }
    )
    ->withMiddleware(function (Middleware $middleware) {
        //
    })
    ->withExceptions(function (Exceptions $exceptions) {
        $exceptions->shouldRenderJsonWhen(function (Request $request, Throwable $e) {
            if ($request->is('api/*')) {
                return true;
            }

            return $request->expectsJson();
        });
    })
    ->create();
