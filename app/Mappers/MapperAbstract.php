<?php

declare(strict_types=1);

namespace App\Mappers;

use App\Dto\ProductDto;

abstract class MapperAbstract
{
    /**
     * @param array<string, mixed> $response
     * @return ProductDto
     */
    abstract public function mapProduct(array $response): ProductDto;
}
