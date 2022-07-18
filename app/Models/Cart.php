<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @property int        $id            [int(10)]
 * @property string     $email         [varchar(255)]
 * @property bool       $is_completed  [boolean, default=false]
 * @property Carbon     $created_at    [timestamp, default="0000-00-00 00:00:00"]
 * @property Carbon     $updated_at    [timestamp, default="0000-00-00 00:00:00"]
 */

class Cart extends Model
{
    protected $table = 'carts';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'email',
    ];
}
