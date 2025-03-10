<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class medicament extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'simpas',
        'observation',
    ];
}
