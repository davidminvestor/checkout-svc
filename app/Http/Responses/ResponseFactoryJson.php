<?php

declare(strict_types=1);

namespace App\Http\Responses;

use App\Exceptions\InvalidCartException;
use App\Exceptions\InvalidIntegrationException;
use App\Exceptions\InvalidParamsException;
use App\Exceptions\ProductMaxQuantityExceeded;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\ProductOutOfStockException;
use App\Exceptions\ProductsClientException;
use App\Exceptions\ServiceUnavailableException;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\UnauthorizedException;
use Illuminate\Validation\ValidationException;

final class ResponseFactoryJson
{

    /**
     * @param array<int|string, mixed> $data
     * @param int                      $responseCode
     * @return JsonResponse
     */
    public function success(array $data, int $responseCode = 200): JsonResponse
    {
        $response = empty($data) ? [] : ['data' => $data];
        return new JsonResponse($response, $responseCode);
    }

    public function error(\Throwable $th): JsonResponse
    {
        $exception = get_class($th);

        switch ($exception) {
            case ValidationException::class:
            case InvalidIntegrationException::class:
            case InvalidParamsException::class:
            case ProductMaxQuantityExceeded::class:
            case InvalidCartException::class:
                $responseCode = 400;
                $messageCode  = ResponseCodesEnum::REQUEST_VALIDATION_ERROR;
                break;
            case UnauthorizedException::class:
                $responseCode = 401;
                $messageCode  = ResponseCodesEnum::UNAUTHORIZED;
                break;
            case ProductNotFoundException::class:
            case ProductsClientException::class:
            case ProductOutOfStockException::class:
                $responseCode = 404;
                $messageCode  = ResponseCodesEnum::NOT_FOUND;
                break;
            case ServiceUnavailableException::class:
                $responseCode = 422;
                $messageCode  = ResponseCodesEnum::UNPROCESSABLE_ENTITY;
                break;
            default:
                $responseCode = 500;
                $messageCode  = ResponseCodesEnum::INTERNAL_ERROR;
                break;
        }

        $errors = $th instanceof ValidationException ? $th->validator->errors()->all() : [$th->getMessage()];
        if ($responseCode === 500) {
            $errors = ['Internal Error, please check later'];
        }

        return new JsonResponse(
            [
                "error" => [
                    "message" => $messageCode,
                    "code"    => $responseCode,
                    "errors"  => $errors
                ]
            ],
            $responseCode
        );
    }
}
