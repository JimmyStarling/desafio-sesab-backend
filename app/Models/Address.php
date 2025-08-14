<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

/**
 * @OA\Schema(
 *     schema="Address",
 *     type="object",
 *     title="Address",
 *     required={"street","city","state","zip"},
 *     @OA\Property(property="street", type="string"),
 *     @OA\Property(property="city", type="string"),
 *     @OA\Property(property="state", type="string"),
 *     @OA\Property(property="zip", type="string")
 * )
 */
class Address extends Model
{
    use HasFactory;

    protected $fillable = ['street', 'city', 'state', 'zip'];

    public function users()
    {
        return $this->belongsToMany(User::class, 'address_user', 'address_id', 'user_id')->withTimestamps();
    }
}
