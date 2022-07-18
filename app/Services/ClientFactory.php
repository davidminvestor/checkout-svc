<?php

declare(strict_types=1);

namespace App\Services;

use App\Exceptions\ProductsClientException;
use App\Exceptions\InvalidIntegrationException;

class ClientFactory
{
    /**
     * @throws ProductsClientException
     * @throws InvalidIntegrationException
     */
    public function getClient(string $integration): ClientAbstract
    {
        return app(ClientAbstract::class, [
            'integration' => $integration,
        ]);
    }
}
