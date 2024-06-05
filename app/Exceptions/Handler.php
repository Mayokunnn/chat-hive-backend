<?php

namespace App\Exceptions;

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
        if($exception instanceof NotFoundHttpException){
            return Response::error("Request Error: Resource not found", statusCode:404,);
        }
        
        if($exception instanceof HttpException){
            return Response::error(403, "Request Error: Forbidden error");
        }

        if($exception instanceof TypeError){
            // narrow the exception to capture only jwt errors
            if( str_contains($exception->getMessage() , 'BaseSigner.php ') ){
                return Response::error(401, "Authentication Error: Session expired, sign in");
            } else {
                Log::error("Syntax Error At: {$exception->getMessage()} {$exception->getFile()} on line {$exception->getLine()}");
                return Response::error(500, "Server Error: Invalid data type");
            }
           
        }

        if ($exception instanceof ParseError || $exception instanceof ErrorException) {
            Log::error("Syntax Error At: {$exception->getMessage()} {$exception->getFile()} on line {$exception->getLine()}");
            return Response::error(500, "Server Error: Check code syntax");
        }

        return parent::render($request, $exception);
    }
}
