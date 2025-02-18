<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Employee extends Model
{
    //
    use HasFactory;

    protected $fillable = [
        'dni',
        'names',
        'paternal_surname',
        'maternal_surname',
        'department_id',
        'position_id',
        'email',
        'phone'
    ];

    public $timestamps = false;

    public function department(): BelongsTo
    {
        return $this->belongsTo(Department::class);
    }

    public function position(): BelongsTo
    {
        return $this->belongsTo(Position::class);
    }


}
