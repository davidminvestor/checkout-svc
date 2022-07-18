<?php

declare(strict_types=1);

namespace App\Dto;

use App\Models\Product;
use Illuminate\Contracts\Support\Arrayable;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;

/**
 * @implements Arrayable<string, mixed>
 */
class ProductDto implements Arrayable
{
    private string $externalId;
    private string $integration;
    private string $name;
    private int $stock;
    private Money $price;

    public function getExternalId(): string
    {
        return $this->externalId;
    }

    public function setExternalId(string $externalId): self
    {
        $this->externalId = $externalId;

        return $this;
    }

    public function getIntegration(): string
    {
        return $this->integration;
    }

    public function setIntegration(string $integration): self
    {
        $this->integration = $integration;

        return $this;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function getStock(): int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getPriceAmount(): int
    {
        return (int)$this->price->getAmount();
    }

    public function setPrice(Money $price): self
    {
        $this->price = $price;

        return $this;
    }

    public function getPriceAmountFormatted(): string
    {
        return (new DecimalMoneyFormatter(new ISOCurrencies()))->format($this->price);
    }

    public function makeFromModel(Product $product): self
    {
        $this->externalId  = $product->external_id;
        $this->integration = $product->integration;
        $this->name        = $product->name;
        $this->stock       = $product->stock;
        $this->price       = Money::USD($product->price);

        return $this;
    }

    /**
     *
     * @return array<string, int|string>
     */
    public function toArrayFormatted(): array
    {
        return [
            'externalId'  => $this->externalId,
            'integration' => $this->integration,
            'name'        => $this->name,
            'stock'       => $this->stock,
            'price'       => $this->getPriceAmountFormatted()
        ];
    }

    public function toArray(): array
    {
        return [
            'external_id'  => $this->externalId,
            'integration' => $this->integration,
            'name'        => $this->name,
            'stock'       => $this->stock,
            'price'       => $this->getPriceAmount()
        ];
    }
}
