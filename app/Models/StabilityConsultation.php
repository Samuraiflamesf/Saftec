<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StabilityConsultation extends Model
{
    use HasFactory, SoftDeletes;
    protected $fillable = [
        'institution_name',
        'cnpj',
        'last_verification_at',
        'excursion_verification_at',
        'estimated_exposure_time',
        'returned_to_storage_at',
        'max_exposed_temperature',
        'min_exposed_temperature',
        'product_description',
        'manufacturer',
        'batch',
        'expiry_date',
        'quantity',
        'order_number',
        'distribution_number',
        'observations',
        'filled_by',
        'role',
        'record_date',
        'protocol_number',
    ];
    protected $casts = [
        'quantity' => 'array',
        'product_description' => 'array',

    ];

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'institution_name',
                'cnpj',
                'last_verification_at',
                'excursion_verification_at',
                'estimated_exposure_time',
                'returned_to_storage_at',
                'max_exposed_temperature',
                'min_exposed_temperature',
                'product_description',
                'manufacturer',
                'batch',
                'expiry_date',
                'quantity',
                'order_number',
                'distribution_number',
                'observations',
                'filled_by',
                'role',
                'record_date',
                'protocol_number',
            ]);
    }
}
