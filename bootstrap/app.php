<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        api: __DIR__.'/../routes/api.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {

        // Registrar los alias de Spatie
        $middleware->alias([
            'role' => \Spatie\Permission\Middleware\RoleMiddleware::class,
            'permission' => \Spatie\Permission\Middleware\PermissionMiddleware::class,
            'role_or_permission' => \Spatie\Permission\Middleware\RoleOrPermissionMiddleware::class,
        ]);

    })
    ->withExceptions(function (Exceptions $exceptions): void {

        // Manejo de sesiones expiradas y tokens CSRF inválidos.
        $exceptions->respond(function (
            Response $response,
            \Throwable $exception,
            Request $request
        ) {

            if ($response->getStatusCode() === 419) {

                $mensaje = 'Tu sesión o formulario ha expirado. Vuelve a intentarlo.';

                // Mantener respuestas JSON para las API internas.
                if (
                    $request->expectsJson() ||
                    $request->is('api/*') ||
                    $request->is('api-interno/*')
                ) {
                    return response()->json([
                        'message' => $mensaje,
                        'session_expired' => true,
                    ], 419);
                }

                // Recuperar el formulario de acceso.
                if ($request->is('login', 'logout')) {
                    return redirect()
                        ->route('login')
                        ->with('session_expired', $mensaje);
                }
            }

            return $response;
        });

    })->create();
