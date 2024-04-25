<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Clown extends Model
{
    use HasFactory;

    public $fillable = [
      'name',
      'email',
      'rating',
      'status',
      'description'
    ];
}
