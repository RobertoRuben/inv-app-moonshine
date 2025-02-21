<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCategory extends Model
{
    //
    use HasFactory;
    protected $fillable = [
        'name',
        'description',
    ];

    public $timestamps = false;

    public function assets()
    {
        return $this->hasMany(\App\Models\Asset::class, 'asset_category_id');
    }

}
