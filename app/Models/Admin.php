<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Foundation\Auth\User as Authenticatable;
class Admin extends Authenticatable
{
    use HasFactory,HasApiTokens;
    protected $fillable = [
        'name',
        'email',
        'password',
        'profile_pic'
    ];
    public function getProfilePicAttribute($value)
    {
        if(!$value == null)
        {
            return asset(Storage::url($value));
        }
       return null;
    }
}
