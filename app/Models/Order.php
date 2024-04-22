<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\OrderItem;


class Order extends Model
{
    protected $fillable = ['user_id', 'status', 'items'];

    // Define the user relationship
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
