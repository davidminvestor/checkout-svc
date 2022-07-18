<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\ProductDto;
use App\Mappers\MapperAbstract;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Cache\RedisStore;

abstract class ClientAbstract
{
    protected MapperAbstract $mapper;
    protected Guzzle $guzzle;
    protected string $rapidapiKey;

    public function __construct(
        MapperAbstract $mapper,
        Guzzle         $guzzle,
        string         $rapidapiKey
    ) {
        $this->mapper      = $mapper;
        $this->guzzle      = $guzzle;
        $this->rapidapiKey = $rapidapiKey;
    }

    abstract public function getProduct(string $productId): ProductDto;
}
