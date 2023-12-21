<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Category extends Model
{
    protected $guarded = ['id'];
    use HasFactory;
    public function getImageAttribute($value)
    {
        if(!$value == null)
        {
            return asset(Storage::url($value));
        }
        return null;
    }
}
