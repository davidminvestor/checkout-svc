<?php

declare(strict_types=1);

namespace App\Providers;

use App\Enums\IntegrationsEnum;
use App\Exceptions\InvalidIntegrationException;
use App\Exceptions\ProductsClientException;
use App\Mappers\AmazonMapper;
use App\Services\AmazonClient;
use App\Services\ClientAbstract;
use GuzzleHttp\Client as Guzzle;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class VendorsServiceProvider extends ServiceProvider
{
    /**
     * Register any vendor related service.
     *
     * @throws ProductsClientException
     * @throws InvalidIntegrationException
     */
    public function register(): void
    {
        $this->app->bind(ClientAbstract::class, function (Application $app, array $params): ClientAbstract {

            if (!isset($params['integration'])) {
                throw new ProductsClientException('Integration client was not provided');
            }

            switch ($params['integration']) {
                case IntegrationsEnum::AMAZON->value:
                    return $this->getAmazonClient($app);
                default:
                    throw new InvalidIntegrationException('Client not found');
            }
        });
    }

    private function getAmazonClient(Application $app): AmazonClient
    {
        $rapidapi     = (string)config('services.rapidapi.key');
        $host         = (string)config('services.amazon.host');
        $amazonApiKey = (string)config('services.amazon.api_key');

        return new AmazonClient(
            $app->make(AmazonMapper::class),
            $app->make(Guzzle::class),
            $rapidapi,
            $host,
            $amazonApiKey
        );
    }
}
