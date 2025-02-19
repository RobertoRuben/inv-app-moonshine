<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    //
    use HasFactory;

    protected $fillable=[
        'bar_code',
        'code',
        'name',
        'category_id',
        'label',
        'acquisition_date',
        'status',
        'quantity',
        'image',
        'department_id',
        'brand_id',
    ];

    protected $casts = [
        'acquisition_date' => 'date',
        'status'          => 'boolean',
    ];

    protected static function booted()
    {
        static::creating(function ($asset) {
            if (empty($asset->acquisition_date)) {
                $asset->acquisition_date = now()->toDateString();
            }
        });
    }

    public function setBarCodeAttribute($value)
    {
        $this->attributes['bar_code'] = str_pad($value, 13, '0', STR_PAD_LEFT);
    }

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function department()
    {
        return $this->belongsTo(Department::class, 'department_id');
    }

    public function brand()
    {
        return $this->belongsTo(Brand::class, 'brand_id');
    }
}
