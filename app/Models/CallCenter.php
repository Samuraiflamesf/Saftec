<?php

namespace App\Models;

use App\Models\User;
use App\Models\Estabelecimento;
use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class CallCenter extends Model
{
    use HasFactory;

    protected $fillable = [
        'protocolo',
        'demandante',
        'setor',
        'unidade',
        'medicamentos',
        'resp_aquisicao',
        'dado_sigiloso',
        'file_espelho',
        'attachments',
        'obs',
        'date_dispensacao',
        'date_resposta',
        'author_id',
        'user_create_id',
        'estabelecimento_id'
    ];
    protected $casts = [
        'attachments' => 'array',
        'medicamentos' => 'array',

    ];

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly([
                'protocolo',
                'demandante',
                'setor',
                'unidade',
                'medicamentos',
                'resp_aquisicao',
                'dado_sigiloso',
                'file_espelho',
                'attachments',
                'obs',
                'date_dispensacao',
                'date_resposta',
                'author_id',
                'estabelecimento_id'
            ]);
    }

    // Referencias
    public function user()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'user_create_id');
    }
    public function estabelecimento(): BelongsTo
    {
        return $this->belongsTo(Estabelecimento::class);
    }
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    protected static function booted()
    {
        static::deleting(function ($callCenter) {
            // Excluir o arquivo do espelho, se existir
            if ($callCenter->file_espelho && Storage::disk('s3')->exists($callCenter->file_espelho)) {
                Storage::disk('s3')->delete($callCenter->file_espelho);
            }

            // Excluir anexos múltiplos, se existirem
            if ($callCenter->attachments) {
                foreach ($callCenter->attachments as $attachment) {
                    if (Storage::disk('s3')->exists($attachment)) {
                        Storage::disk('s3')->delete($attachment);
                    }
                }
            }
        });
    }
    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (auth()->check()) {
                $model->estabelecimento_id = auth()->user()->estabelecimento_id;
            }
        });
    }
}
