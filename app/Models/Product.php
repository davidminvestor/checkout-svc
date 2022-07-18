<?php

namespace App\Models;

use App\Dto\ProductDto;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int        $id            [int(10)]
 * @property string     $name          [varchar(255)]
 * @property string     $integration   [varchar(255)]
 * @property string     $external_id   [varchar(255)]
 * @property int        $stock         [int(10), default=0]
 * @property string     $currency_code [varchar(3), default="USD"]
 * @property int        $price         [int(10), default=0]
 * @property Carbon     $created_at    [timestamp, default="0000-00-00 00:00:00"]
 * @property Carbon     $updated_at    [timestamp, default="0000-00-00 00:00:00"]
 */

class Product extends Model
{
    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'integration',
        'name',
        'external_id',
        'stock',
        'price'
    ];

    public function makeFromDto(ProductDto $productDto): self
    {
        $this->integration = $productDto->getIntegration();
        $this->name        = $productDto->getName();
        $this->external_id = $productDto->getExternalId();
        $this->stock       = $productDto->getStock();
        $this->price       = $productDto->getPriceAmount();

        return $this;
    }
}
