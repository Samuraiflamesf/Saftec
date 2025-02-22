<?php

namespace App\Models;

use Illuminate\Support\Str;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class StabilityConsultation extends Model
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
        'medicament',
        'order_number',
        'distribution_number',
        'observations',
        'file_monitor_temp',
        'user_create_id',
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
                'medicament',
                'order_number',
                'distribution_number',
                'observations',
                'user_create_id',
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
    }
    protected static function booted()
    {
        static::deleting(function (self $stabilityConsultation) {
            // Verifica se há arquivos anexados no campo `file_monitor_temp`
            if ($stabilityConsultation->file_monitor_temp) {
                Storage::disk('s3')->delete($stabilityConsultation->file_monitor_temp);
            }
        });
    }
}
