<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;
class Creator extends Authenticatable
{
    use HasFactory,HasApiTokens;
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_pic'
    ];
}