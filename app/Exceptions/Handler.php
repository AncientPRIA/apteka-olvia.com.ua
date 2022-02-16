<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        /*
    \Illuminate\Database\Eloquent\ModelNotFoundException::class,
    \Illuminate\Validation\ValidationException::class
        */
        $exception_class = get_class($exception);

        if(
            $exception_class !== "Symfony\Component\HttpKernel\Exception\NotFoundHttpException"
            && $exception_class !== "Illuminate\Auth\AuthenticationException"
            && $exception_class !== "Illuminate\Auth\Access\AuthorizationException"
            && $exception_class !== "Illuminate\Database\Eloquent\ModelNotFoundException"
            && $exception_class !== "Illuminate\Validation\ValidationException"
        ){
            //error_telegram("CODE: ".$exception->getCode()."\nMESSAGE: ".$exception->getMessage()."\nFILE: ".$exception->getFile()."\n".$exception->getTraceAsString());
        }

        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        return parent::render($request, $exception);
    }
}
