<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Dto\ProductDto;
use App\Models\Cart;
use App\Models\CartProducts;
use App\Models\Product;
use Illuminate\Support\Collection;

class ProductsRepository
{
    private const UPSERT_PRODUCT_KEYS = ['integration', 'external_id'];

    /**
     * @codeCoverageIgnore
     */
    public function save(ProductDto $productDto): void
    {
        Product::upsert($productDto->toArray(), self::UPSERT_PRODUCT_KEYS);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getByExternalId(string $integration, string $externalId): ?Product
    {
        return Product::query()
            ->where('integration', $integration)
            ->where('external_id', $externalId)
            ->first();
    }

    /**
     * @codeCoverageIgnore
     */
    public function getProductAdded(Cart $cart, string $externalId): ?CartProducts
    {
        return CartProducts::query()
            ->select('cart_products.*')
            ->join('products', 'products.id', '=', 'product_id')
            ->where('cart_id', $cart->id)
            ->where('products.external_id', $externalId)
            ->first();
    }

    /**
     * @codeCoverageIgnore
     */
    public function deleteProductAdded(CartProducts $productAdded): void
    {
        $productAdded->delete();
    }

    /**
     * @codeCoverageIgnore
     */
    public function updateProductsStock(Collection $products): void
    {
        foreach ($products as $product) {
            /** @var Product $productToUpdate */
            $productToUpdate = Product::query()->find($product['id']);
            $productToUpdate->stock = $productToUpdate->stock - $product['quantity'];
            $productToUpdate->save();
        }
    }
}
