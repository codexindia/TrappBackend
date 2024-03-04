<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Playlist extends Model
{
    use HasFactory;
    protected $guarded = ['id'];
    public function getThumbnailAttribute($value)
    {
        if(!$value == null)
        {
            return asset(Storage::url($value));
        }
        return null;
    }
    public function Videos()
    {
        return $this->hasMany(UploadedVideos::class, 'playlist_id', 'id');
    }
}
