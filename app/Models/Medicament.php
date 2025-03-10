<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Medicament extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'name',
        'simpas',
        'observation',
    ];


    public function callCenters()
    {
        return $this->belongsToMany(CallCenter::class, 'call_center_medicament', 'medicament_id', 'call_center_id');
    }
}
