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
        'setor',
        'demandante',
        'dado_sigiloso',
        'unidade',
        'resp_aquisicao',
        'dispensation_date',
        'response_date',
        'medicamentos',
        'observation',
        'mirror_file',
        'attachments',
        'author_id',
        'created_by',
        'estabelecimento_id',
    ];
    protected $casts = [
        'attachments' => 'array',
        'medicamentos' => 'array',

    ];

    use LogsActivity;

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly($this->fillable);
    }

    // Referencias
    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'author_id');
    }
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
    public function estabelecimento()
    {
        return $this->belongsTo(Estabelecimento::class, 'estabelecimento_id');
    }
    protected static function booted()
    {
        static::deleting(function ($callCenter) {
            // Excluir o arquivo do espelho, se existir
            if ($callCenter->mirror_file && Storage::disk('s3')->exists($callCenter->mirror_file)) {
                Storage::disk('s3')->delete($callCenter->mirror_file);
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
