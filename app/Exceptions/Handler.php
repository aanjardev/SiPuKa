<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Symfony\Component\ErrorHandler\Error\FatalError;
use Illuminate\Http\Request;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [

    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [

    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {

        });

        $this->renderable(function (FatalError $e, Request $request) {
            if (str_contains($e->getMessage(), 'Maximum execution time')) {
                try {
                    return response()->view('errors.timeout', [], 503);
                } catch (\Exception $viewException) {
                    return response()->make('Server timeout - Please refresh the page', 503);
                }
            }
        });

        $this->renderable(function (AuthenticationException $e, Request $request) {
            try {
                return response()->view('errors.403', [], 403);
            } catch (\Exception $viewException) {
                return response()->make('Access denied - Please login', 403);
            }
        });

        $this->renderable(function (ValidationException $e, Request $request) {
            if ($request->expectsJson()) {
                return response()->json([
                    'message' => 'Data tidak valid',
                    'errors' => $e->errors()
                ], 422);
            }

            return redirect()->back()
                ->withErrors($e->errors())
                ->withInput();
        });

        $this->renderable(function (HttpException $e, Request $request) {
            $statusCode = $e->getStatusCode();
            $viewMap = [
                404 => 'errors.404',
                403 => 'errors.403',
                500 => 'errors.500',
                503 => 'errors.503',
                429 => 'errors.429',
            ];

            $view = $viewMap[$statusCode] ?? 'errors.generic';
            
            try {
                return response()->view($view, [
                    'exception' => $e,
                    'statusCode' => $statusCode
                ], $statusCode);
            } catch (\Exception $viewException) {

                return response()->make("Error {$statusCode} - Something went wrong", $statusCode);
            }
        });
    }
}
