<?php

declare(strict_types=1);

namespace App\Managers;

use App\Dto\ProductDto;
use App\Exceptions\ProductMaxQuantityExceeded;
use App\Models\Cart;
use App\Models\CartProducts;
use App\Models\Product;
use App\Models\User;
use App\Repositories\CartsRepository;
use App\Repositories\ProductsRepository;
use Illuminate\Support\Collection;
use Money\Currencies\ISOCurrencies;
use Money\Formatter\DecimalMoneyFormatter;
use Money\Money;

class CartsManager
{
    public function __construct(
        private readonly CartsRepository    $cartsRepository,
        private readonly ProductsRepository $productsRepository,
    ) {
    }

    public function getIncompleteCart(User $user): ?Cart
    {
        return $this->cartsRepository->getIncompleteCartByUser($user);
    }

    public function getOrCreateCartByUser(User $user): Cart
    {
        return $this->cartsRepository->getOrCreateCartByUser($user);
    }

    public function getProducts(Cart $cart): Collection
    {
        $products  = $this->cartsRepository->getProducts($cart);
        $formatted = $products->map(function ($item) {
            $price         = Money::USD($item['price']);
            $item['price'] = (new DecimalMoneyFormatter(new ISOCurrencies()))->format($price);
            return  $item;
        });

        return $formatted;
    }

    public function getTotal(Cart $cart): Money
    {
        $products  = $this->cartsRepository->getProducts($cart);
        $total = Money::USD('0');
        foreach ($products as $product) {
            $subtotal = (Money::USD($product['price']))->multiply($product['quantity']);
            $total    = $total->add($subtotal);
        }

        return $total;
    }

    public function addProduct(Cart $cart, ProductDto $productDto, int $quantity): void
    {

        $productAdded = $this->productsRepository->getProductAdded($cart, $productDto->getExternalId());

        /** @var Product $product */
        $product  = $this->productsRepository->getByExternalId($productDto->getIntegration(), $productDto->getExternalId());
        $quantity = $quantity + $productAdded?->quantity;

        if ($productDto->getStock() < $quantity) {
            throw new ProductMaxQuantityExceeded('Quantity is greather than stock available');
        }

        $this->cartsRepository->addProduct($cart, $product, $quantity);
    }

    public function deleteProduct(CartProducts $productAdded): void
    {
        $this->productsRepository->deleteProductAdded($productAdded);
    }

    public function checkout(Cart $cart, Collection $products): void
    {
        $this->cartsRepository->checkout($cart);
        $this->productsRepository->updateProductsStock($products);
    }
}
