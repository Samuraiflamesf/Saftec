<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Spatie\Activitylog\Traits\LogsActivity;
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
        'resp_resposta',
        'file_espelho',
        'attachments',
        'obs',
        'date_dispensacao',
        'date_resposta',
        'author_id',
        'user_create_id',
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
                'resp_resposta',
                'file_espelho',
                'attachments',
                'obs',
                'date_dispensacao',
                'date_resposta',
                'author_id',

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

    protected static function booted()
    {
        static::deleting(function ($callCenter) {
            // Excluir o arquivo do espelho, se existir
            if ($callCenter->file_espelho && Storage::disk('public')->exists($callCenter->file_espelho)) {
                Storage::disk('public')->delete($callCenter->file_espelho);
            }

            // Excluir anexos múltiplos, se existirem
            if ($callCenter->attachments) {
                foreach ($callCenter->attachments as $attachment) {
                    if (Storage::disk('public')->exists($attachment)) {
                        Storage::disk('public')->delete($attachment);
                    }
                }
            }
        });
    }
}
