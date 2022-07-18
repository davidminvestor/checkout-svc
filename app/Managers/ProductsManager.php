<?php

declare(strict_types=1);

namespace App\Managers;

use App\Dto\ProductDto;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\ProductOutOfStockException;
use App\Exceptions\ServiceUnavailableException;
use App\Models\Cart;
use App\Models\CartProducts;
use App\Repositories\ProductsRepository;
use App\Services\ClientFactory;

class ProductsManager
{
    public function __construct(
        private readonly ClientFactory      $factory,
        private readonly ProductsRepository $productsRepository,
    ) {
    }

    /**
     * @throws ServiceUnavailableException
     * @throws ProductNotFoundException
     * @throws ProductOutOfStockException
     */
    public function getProduct(string $integration, string $externalId): ProductDto
    {
        $product = $this->productsRepository->getByExternalId($integration, $externalId);
        if ($product !== null) {
            $productDto = (new ProductDto())->makeFromModel($product);
        } else {
            $client     = $this->factory->getClient($integration);
            $productDto = $client->getProduct($externalId);

            $this->productsRepository->save($productDto);
        }

        return $productDto;
    }

    public function getProductAdded(Cart $cart, string $externalId): ?CartProducts
    {
        return $this->productsRepository->getProductAdded($cart, $externalId);
    }
}
