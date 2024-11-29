<?php

namespace App\Models;

use Illuminate\Support\Str;
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
        'user_create_id',
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
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }
    public static function boot()
    {
        parent::boot();

        // Gerar número de protocolo automaticamente
        static::creating(function ($model) {
            do {
                $protocolNumber = now()->format('Ymd') . strtoupper(Str::random(6));
            } while (self::where('protocol_number', $protocolNumber)->exists());

            $model->protocol_number = $protocolNumber;
        });
        static::saving(function ($model) {
            if ($model->returned_to_storage_at && $model->last_verification_at) {
                $difference = $model->returned_to_storage_at->diffInMinutes($model->last_verification_at);
                $model->estimated_exposure_time = $difference;
            }
        });
    }
}
