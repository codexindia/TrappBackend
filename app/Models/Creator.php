<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;
class Creator extends Authenticatable
{
    use HasFactory,HasApiTokens;
    protected $guarded = ['id'];
    protected $hidden = ['password'];
    public function getChannelLogoAttribute($value)
    {
        if(!$value == null)
        {
            return asset(Storage::url($value));
        }
        $arr = array(
            '0' => 'https://ui-avatars.com/api/?background=0D8ABC&name='
            .$this->attributes['first_name'].'+'
            .$this->attributes['last_name'].'&color=white',
            '1' => 'https://ui-avatars.com/api/?background=4BE87A&name='
            .$this->attributes['first_name'].'+'
            .$this->attributes['last_name'].'&color=white'
        );
        return $arr[rand(0,1)];
    }
}
