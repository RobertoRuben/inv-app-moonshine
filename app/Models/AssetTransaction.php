<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetTransaction extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'transaction_type_id',
        'transaction_date',
        'details',
    ];

    protected $casts = [
        'transaction_date' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function transactionType()
    {
        return $this->belongsTo(TransactionType::class);
    }

}
