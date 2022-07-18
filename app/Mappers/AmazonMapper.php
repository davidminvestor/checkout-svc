<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Dto\ProductDto;
use App\Enums\IntegrationsEnum;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\ProductOutOfStockException;
use Money\Money;

class AmazonMapper extends MapperAbstract
{
    private const OUT_OF_STOCK_LABEL = 'unavailable';
    private const AVAILABLE_BY_INVITATION_LABEL = 'Available by invitation';
    private const PRODUCT_NOT_EXISTS = 'StatusCodeError';

    /**
     * @param array<string, mixed> $response
     * @return ProductDto
     * @throws ProductNotFoundException
     * @throws ProductOutOfStockException
     */
    public function mapProduct(array $response): ProductDto
    {
        if (isset($response['statusCode']) || $response['name'] === self::PRODUCT_NOT_EXISTS) {
            throw new ProductNotFoundException('Product doesnt exist');
        }

        if (
            str_contains($response['availability_status'], self::OUT_OF_STOCK_LABEL) ||
            str_contains($response['availability_status'], self::AVAILABLE_BY_INVITATION_LABEL) ||
            $response['pricing'] === '' ||
            empty($response['availability_status'])
        ) {
            throw new ProductOutOfStockException('Product is out of stock: ' . $response['availability_status']);
        }

        $price = $this->mapPrice($response['pricing']);

        return (new ProductDto())
            ->setExternalId($response['product_information']['ASIN'])
            ->setName($response['name'])
            ->setStock($response['product_information']['availability_quantity'] ?? 1000)
            ->setPrice($price)
            ->setIntegration(IntegrationsEnum::AMAZON->value);
    }

    private function mapPrice(string $price): Money
    {
        /** @var int|numeric-string $priceFormatted */
        $priceFormatted = preg_replace('/\$|\ |\./', '', $price);
        return Money::USD($priceFormatted);
    }
}
