<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class StickersList extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function getStickerSrcAttribute($value)
    {
        if(!$value == null)
        {
            return asset(Storage::url($value));
        }
       return null;
    }
}
