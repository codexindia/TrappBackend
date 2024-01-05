<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'phone',
        'country_code',
        'profile_pic'
    ];
    public function messages()
    {
        return $this->hasMany(Message::class);
    }    
    public function UserBlocked()
    {
        return $this->hasOne(UserBand::class, 'user_id', 'id');
    }
    public function getProfilePicAttribute($value)
    {
        if(!$value == null)
        {
            return asset(Storage::url($value));
        }
       return null;
    }
}
