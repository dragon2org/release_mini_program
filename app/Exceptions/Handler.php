<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Arr;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        UnprocessableEntityHttpException::class,
        WechatGatewayException::class,
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
     * @param Exception $exception
     * @return mixed|void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param \Illuminate\Http\Request $request
     * @param Exception $exception
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function render($request, Exception $exception)
    {
        if($exception instanceof ModelNotFoundException){
            throw new UnprocessableEntityHttpException($exception->getMessage(), $exception->getPrevious(), $exception->getCode());
        }
        return parent::render($request, $exception);
        if($exception instanceof ValidationException){
            return $this->exceptionToArray($exception, $exception->errors());
        }
        return parent::render($request, $exception);
    }

    /**
     * Convert a validation exception into a JSON response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Illuminate\Validation\ValidationException  $e
     * @return \Illuminate\Http\JsonResponse
     */
    protected function invalidJson($request, ValidationException $e)
    {
        $message = $e->errors() ? $e->errors() : $e->getMessage();
        $data = config('app.debug') ? [
            'message' => $message,
            'status' => 'F',
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->map(function ($trace) {
                return Arr::except($trace, ['args']);
            })->all(),
            'request_params' => [
                'header' => request()->header(),
                'uri' => request()->getUri(),
                'queryParams' => request()->query(),
                'body' => request()->getContent(),
            ]
        ] : [
            'message' => $message,
            'status' => 'F',
        ];

        return response()->json($data, $e->status);
    }

    /**
     * Convert the given exception to an array.
     *
     * @param  \Exception  $e
     * @return array
     */
    protected function convertExceptionToArray(Exception $e)
    {
        return config('app.debug') ? [
            'message' => $e->getMessage(),
            'status' => 'F',
            'exception' => get_class($e),
            'file' => $e->getFile(),
            'line' => $e->getLine(),
            'trace' => collect($e->getTrace())->map(function ($trace) {
                return Arr::except($trace, ['args']);
            })->all(),
            'request_params' => [
                'header' => request()->header(),
                'uri' => request()->getUri(),
                'queryParams' => request()->query(),
                'body' => request()->getContent(),
            ]
        ] : [
            'message' => $this->isHttpException($e) ? $e->getMessage() : 'Server Error',
            'status' => 'F',
        ];
    }
}
