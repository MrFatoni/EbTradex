<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        JobException::class,
        UnauthorizedException::class
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
     * @param  \Exception $exception
     * @return void
     * @throws Exception
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Exception $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        if ($exception instanceof TokenMismatchException) {
            if ($request->ajax()) {
                return response()->json([
                    'dismiss' => __('Session expired due to inactivity. Please reload page'),
                ]);
            } else {
                return redirect()->back()->with(['dismiss' => __('Session expired due to inactivity. Please try again')]);
            }
        } elseif ($exception instanceof UnauthorizedException) {
            if ($request->is('api/*')) {
                return response()->json([
                    SERVICE_RESPONSE_STATUS => 'auth',
                    SERVICE_RESPONSE_MESSAGE => title_case(str_replace('_', ' ', $exception->getMessage()))
                ], $exception->getCode());
            } else {
                return response()->view('errors.' . $exception->getMessage(), [], 401);
            }
        } elseif (env('APP_ENV') == 'production' && !$exception instanceof ValidationException) {
            return response()->view('errors.404');
        }

        return parent::render($request, $exception);
    }
}
