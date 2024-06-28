<?php

namespace App\Exceptions;

use App\Traits\ResponseService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Validation\ValidationException;
use Laravel\Lumen\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Throwable;
use ErrorException;
use Illuminate\Support\Facades\Log;
use ParseError;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use TypeError;
use App\Traits\Response;
use Illuminate\Http\Exceptions\ThrottleRequestsException;
use Symfony\Component\HttpKernel\Exception\TooManyRequestsHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Throwable  $exception
     * @return void
     *
     * @throws \Exception
     */
    public function report(Throwable $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Throwable  $exception
     * @return \Illuminate\Http\Response|\Illuminate\Http\JsonResponse
     *
     * @throws \Throwable
     */
    public function render($request, Throwable $exception)
    {
        if ($exception instanceof NotFoundHttpException) {
            Log::error("Syntax Error At: {$exception->getMessage()} {$exception->getFile()} on line {$exception->getLine()}");
            return ResponseService::error("Request Error: Resource not found", [$exception->getMessage()], 404,);
        }

        if ($exception instanceof ThrottleRequestsException || $exception instanceof TooManyRequestsHttpException) {
            return ResponseService::error("Request Error: Too Many Attempts", [], 429);
        }

        if ($exception instanceof HttpException) {
            Log::error("Syntax Error At: {$exception->getMessage()} {$exception->getFile()} on line {$exception->getLine()}");
            return ResponseService::error("Request Error: Forbidden error", [$exception->getMessage()], 403);
        }

        if ($exception instanceof AuthorizationException) {
            Log::error("Syntax Error At: {$exception->getMessage()} {$exception->getFile()} on line {$exception->getLine()}");
            return ResponseService::error("Authorization Error: You are not authorized", [$exception->getMessage()], 401);
        }

        if ($exception instanceof TypeError) {
            // narrow the exception to capture only jwt errors
            if (str_contains($exception->getMessage(), 'BaseSigner.php ')) {
                Log::error("Syntax Error At: {$exception->getMessage()} {$exception->getFile()} on line {$exception->getLine()}");
                return ResponseService::error("Authentication Error: Session expired, sign in", [$exception->getMessage()], 401);
            } else {
                Log::error("Syntax Error At: {$exception->getMessage()} {$exception->getFile()} on line {$exception->getLine()}");
                return ResponseService::error("Server Error: Invalid data type", [$exception->getMessage()], 500);
            }
        }

        if ($exception instanceof ParseError || $exception instanceof ErrorException) {
            Log::error("Syntax Error At: {$exception->getMessage()} {$exception->getFile()} on line {$exception->getLine()}");
            return ResponseService::error("Server Error: Check code syntax", [$exception->getMessage()], 500);
        }

        return parent::render($request, $exception);
    }
}
