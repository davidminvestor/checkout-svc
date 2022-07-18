<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int        $id            [int(10)]
 * @property int        $cartId        [int(10)]
 * @property int        $productId     [int(10)]
 * @property int        $quantity      [int(10)]
 * @property Carbon     $created_at    [timestamp, default="0000-00-00 00:00:00"]
 * @property Carbon     $updated_at    [timestamp, default="0000-00-00 00:00:00"]
 */

class CartProducts extends Model
{
    protected $table = 'cart_products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'cart_id',
        'product_id',
        'quantity'
    ];
}
