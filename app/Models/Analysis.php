<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Foundation\Auth\User;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Analysis extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'protocol_number',
        'institution_name',
        'cnpj',
        'last_verification_at',
        'excursion_verification_at',
        'estimated_exposure_time',
        'returned_to_storage_at',
        'max_exposed_temperature',
        'min_exposed_temperature',
        'local_exposure',
        'medicament',
        'order_number',
        'distribution_number',
        'boolean_unit',
        'observations',
        'file_monitor_temp',
        'user_create_id',
        'estabelecimento_id'
    ];
    protected $casts = [
        'medicament' => 'array',
    ];

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'protocol_number',
                'institution_name',
                'cnpj',
                'last_verification_at',
                'excursion_verification_at',
                'estimated_exposure_time',
                'returned_to_storage_at',
                'max_exposed_temperature',
                'min_exposed_temperature',
                'local_exposure',
                'medicament',
                'order_number',
                'distribution_number',
                'boolean_unit',
                'observations',
                'file_monitor_temp',
                'user_create_id',
                'estabelecimento_id'
            ]);
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }
}
