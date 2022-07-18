<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Enums\IntegrationsEnum;
use App\Http\Responses\ResponseFactoryJson;
use App\Managers\ProductsManager;
use Illuminate\Http\JsonResponse;
use Psr\Log\LoggerInterface;

class ProductsController extends Controller
{
    private const SIGNATURE_LOG = 'checkout-svc:products-controller';

    public function __construct(
        private readonly ProductsManager     $manager,
        private readonly ResponseFactoryJson $responseFactory,
        private readonly LoggerInterface     $logger
    ) {
    }

    public function getProduct(string $integration, string $externalId): JsonResponse
    {
        try {

            $rules = [
                'integration' => 'string|required|in:' . IntegrationsEnum::getString(),
                'externalId'  => 'string|max:32|required',
            ];
            $parameters = [
                'integration' => $integration,
                'externalId'  => $externalId
            ];
            $this->getValidationFactory()->make(
                $parameters,
                $rules
            )->validate();

            $product = $this->manager->getProduct($integration, $externalId);
            return $this->responseFactory->success($product->toArrayFormatted());
        } catch (\Throwable $th) {

            $this->logger->error(
                '[' . self::SIGNATURE_LOG . '] Unable to get product by id',
                [
                    'product_id' => $externalId,
                    'message'    => $th->getMessage(),
                    'exception'  => $th,
                ]
            );

            return $this->responseFactory->error($th);
        }
    }
}
