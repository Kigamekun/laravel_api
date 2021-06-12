<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class auth_token extends Model
{

    protected $table = 'auth_token';
    protected $fillable = ['user_id','token'];

    use HasFactory;
}
