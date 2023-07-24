<?php

namespace App\Exceptions;

use App\Interfaces\HttpCodeInterface;
use App\Traits\Utilities\ApiResponse;
use Fruitcake\Cors\CorsService;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Laravel\Passport\Exceptions\OAuthServerException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler implements HttpCodeInterface
{

    use ApiResponse;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
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
            //
        });
    }

    /**
     * @param $request
     * @param Throwable $e
     * @return JsonResponse|RedirectResponse
     * @throws Throwable
     */
    public function render($request, Throwable $e): JsonResponse|RedirectResponse
    {
        $response = $this->handleException($request, $e);
        app(CorsService::class)->addActualRequestHeaders($response, $request);
        return $response;
    }

    /**
     * @param $request
     * @param Throwable $exception
     * @return JsonResponse|RedirectResponse
     * @throws Throwable
     */
    public function handleException($request, Throwable $exception): JsonResponse|RedirectResponse
    {
        //dd(get_class($exception));
        switch ($exception)
        {
            case $exception instanceof ValidationException:
                return $this->convertValidationExceptionToResponse($exception, $request);
            case $exception instanceof ModelNotFoundException:
                $model = strtolower(class_basename($exception->getModel()));
                return $this->errorResponse("No hay ninguna instancia de {$model} con el ID proporcionado.", self::NOT_FOUND);
            case $exception instanceof AuthenticationException:
                return $this->unauthenticated($request, $exception);
            case $exception instanceof AuthorizationException:
                return $this->errorResponse($exception->getMessage(), self::FORBIDDEN);
            case $exception instanceof NotFoundHttpException:
                return $this->errorResponse('URL no encontrada.', self::NOT_FOUND);
            case $exception instanceof MethodNotAllowedHttpException:
                return $this->errorResponse($exception->getMessage(), self::METHOD_NOT_ALLOWED);
            case $exception instanceof HttpException:
                return $this->errorResponse($exception->getMessage(), $exception->getStatusCode());
            case $exception instanceof QueryException:
                $code = $exception->errorInfo[1];
                if ($code == 1451) {
                    return $this->errorResponse('El recurso no se puede eliminar de forma permanente porque está relacionado con algún otro.', self::CONFLICT);
                }
                break;
            case $exception instanceof TokenMismatchException:
                return redirect()->back()->withInput($request->input());
            case $exception instanceof OAuthServerException:
                return $this->errorResponse($exception->getMessage(), $exception->statusCode());
            default:
                return $this->errorResponse($exception->getMessage());
        }
        return $this->errorResponse($exception->getMessage());
    }

    /**
     * Convert an authentication exception into an unauthenticated response.
     *
     * @param Request $request
     * @param AuthenticationException $exception
     * @return JsonResponse
     */
    protected function unauthenticated($request, AuthenticationException $exception): JsonResponse
    {
        return $this->errorResponse("Usuario no autenticado.", self::UNAUTHORIZED);
    }

    /**
     * Create a response object from the given validation exception.
     *
     * @param ValidationException $e
     * @param Request $request
     * @return JsonResponse|RedirectResponse
     */
    protected function convertValidationExceptionToResponse(ValidationException $e, $request): JsonResponse|RedirectResponse
    {

//        if ($this->isFrontend($request)) {
//            return $request->ajax() ? response()->json($errors, self::UNPROCESSABLE_ENTITY) : redirect()
//                ->back()
//                ->withInput($request->input())
//                ->withErrors($errors);
//        }
//        return $this->errorResponse($errors, self::UNPROCESSABLE_ENTITY);
        if ($request->path() === 'api/order/received') {
            $errors = $e->validator->errors()->getMessages();
            Log::error(json_encode($errors));
            return response()->json([
                'mensaje' => "devolver",
                'desc' => 'Falta información mandatorio para completar el pago',
                'id' => 14
            ],HttpCodeInterface::BAD_REQUEST);
        } else {
            $errors = $e->validator->errors()->getMessages();
            return $this->errorResponse($errors, self::UNPROCESSABLE_ENTITY);
        }

    }

    private function isFrontend($request): bool
    {
        return $request->acceptsHtml() && collect($request->route()->middleware())->contains('web');
    }

}
