<?php
namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cart extends Model
{
use HasFactory;

protected $fillable = ['user_id']; // Add user_id to the fillable properties

public function user()
{
return $this->belongsTo(User::class);
}

public function products()
{
return $this->belongsToMany(Product::class)->withPivot('quantity');
}
}
