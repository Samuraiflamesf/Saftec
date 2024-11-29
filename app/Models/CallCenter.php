<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

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
    ];
    protected $casts = [
        'attachments' => 'array',
        'medicamentos' => 'array',

    ];
    // Referencias
    public function user()
    {
        return $this->belongsTo(User::class, 'author_id');
    }
}
