<?php

namespace App\Models;

use App\Models\User;
use App\Models\CallCenter;
use App\Models\StabilityConsultation;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estabelecimento extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'cnes',
        'nome',
        'macrorregiao',
    ];

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function stabilityConsultations(): HasMany
    {
        return $this->hasMany(StabilityConsultation::class);
    }

    public function callCenters(): HasMany
    {
        return $this->hasMany(CallCenter::class);
    }
}
