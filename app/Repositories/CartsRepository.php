<?php

declare(strict_types=1);

namespace App\Repositories;

use App\Models\Cart;
use App\Models\CartProducts;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;

class CartsRepository
{
    /**
     * @codeCoverageIgnore
     */
    public function save(Cart $cart): void
    {
        $cart->saveOrFail();
    }

    /**
     * @codeCoverageIgnore
     */
    public function checkout(Cart $cart): void
    {
        $cart->is_completed = true;
        $cart->save();
    }

    /**
     * @codeCoverageIgnore
     */
    public function getOrCreateCartByUser(User $user): Cart
    {
        return Cart::query()->firstOrCreate([
            'email'        => $user->email,
            'is_completed' => false
        ]);
    }

    /**
     * @codeCoverageIgnore
     */
    public function getIncompleteCartByUser(User $user): ?Cart
    {
        return Cart::query()
            ->where('email', $user->email)
            ->where('is_completed', false)
            ->first();
    }

    public function addProduct(Cart $cart, Product $product, int $quantity): void
    {
        CartProducts::updateOrCreate(
            ['cart_id'    => $cart->id, 'product_id' => $product->id,],
            ['quantity'   => $quantity]
        );
    }

    /**
     * @codeCoverageIgnore
     */
    public function getProducts(Cart $cart): Collection
    {
        return CartProducts::query()
            ->select('products.id', 'products.name', 'products.external_id', 'products.price', 'products.currency_code', 'cart_products.quantity')
            ->join('products', 'products.id', '=', 'product_id')
            ->where('cart_id', $cart->id)
            ->get();
    }
}
