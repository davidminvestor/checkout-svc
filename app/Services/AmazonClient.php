<?php

declare(strict_types=1);

namespace App\Services;

use App\Dto\ProductDto;
use App\Exceptions\ProductNotFoundException;
use App\Exceptions\ProductOutOfStockException;
use App\Exceptions\ServiceUnavailableException;
use App\Mappers\AmazonMapper;
use GuzzleHttp\Client as Guzzle;

class AmazonClient extends ClientAbstract
{
    private const DEFAULT_COUNTRY     = 'US';

    public function __construct(
        AmazonMapper $mapper,
        Guzzle       $guzzle,
        string       $rapidapiKey,
        private readonly string $host,
        private readonly string $apiKey
    ) {
        parent::__construct($mapper, $guzzle, $rapidapiKey);
    }

    /**
     * @param array<string, mixed> $body
     * @return array<string, mixed>
     */
    private function generateHeadersRequest(array $body): array
    {
        return [
            'headers' => [
                'Content-Type'    => 'application/json',
                'X-RapidAPI-Key'  => $this->rapidapiKey,
                'X-RapidAPI-Host' => $this->host
            ],
            'body' => (string) json_encode($body)
        ];
    }

    /**
     * @throws ServiceUnavailableException
     * @throws ProductNotFoundException
     * @throws ProductOutOfStockException
     */
    public function getProduct(string $productId): ProductDto
    {
        $uri  = sprintf(
            'https://%s/products/%s?api_key=%s',
            $this->host,
            $productId,
            $this->apiKey
        );

        $body = [
            'country' => self::DEFAULT_COUNTRY,
            'asin'    => $productId
        ];

        try {
            $response     = $this->guzzle->get($uri, $this->generateHeadersRequest($body));
            $jsonResponse = $response->getBody()->getContents();
        } catch (\Throwable $th) {
            throw new ServiceUnavailableException('Amazon client is not available, please check later', 422, $th);
        }

        /** @var array<string, mixed> $responseData */
        $responseData = json_decode($jsonResponse, true);
        return  $this->mapper->mapProduct($responseData);
    }
}
