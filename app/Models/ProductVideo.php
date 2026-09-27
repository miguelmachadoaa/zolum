<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ProductVideo extends Model
{
    protected $fillable = ['product_id', 'youtube_url', 'video_id', 'title'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    // Helper para obtener el thumbnail nativo de YouTube
    public function getThumbnailUrlAttribute()
    {
        return "https://img.youtube.com/vi/{$this->video_id}/hqdefault.jpg";
    }

    // Helper para obtener URL de embed limpia
    public function getEmbedUrlAttribute()
    {
        return "https://www.youtube.com/embed/{$this->video_id}?autoplay=1&rel=0";
    }
}
