<?php

namespace App\Exceptions;

use Throwable;
use App\Traits\ValidationErrorTrait;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Http\Exceptions\HttpResponseException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\Console\Exception\RuntimeException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Contracts\Filesystem\FileNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\Console\Exception\CommandNotFoundException;
use Illuminate\Foundation\Http\Exceptions\MaintenanceModeException;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    use ValidationErrorTrait;

    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        FileNotFoundException::class,
        CommandNotFoundException::class,
        AccessDeniedHttpException::class,
        ModelNotFoundException::class,
        MethodNotAllowedHttpException::class,
        RuntimeException::class,
        MaintenanceModeException::class,
        HttpException::class,
        ValidationException::class,
    ];

    protected $internalDontReport = [
        FileNotFoundException::class,
        AuthenticationException::class,
        AuthorizationException::class,
        HttpResponseException::class,
        ModelNotFoundException::class,
        TokenMismatchException::class,
        ValidationException::class,
        MethodNotAllowedHttpException::class,
        RuntimeException::class,
        MaintenanceModeException::class,
        HttpException::class,
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            if ($e instanceof AuthorizationException) {
                $error = ValidationException::withMessages([
                    'user' => ['You arent authorized to do it'],
                ]);

                $error->status(403);

                throw $error;
            }
        });
    }
}
