<?php 

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProductPdf extends Model
{
    protected $fillable = ['product_id', 'title', 'file_path'];

    public function product()
    {
        return $this->belongsTo(Product::class);
    }

    public function getUrlAttribute()
    {
        return Storage::disk('r2')->url($this->file_path);
    }
}