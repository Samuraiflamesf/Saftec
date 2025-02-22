<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Card extends Model
{
    use HasFactory;

    protected $fillable = ['nome', 'descricao', 'imagem', 'link', 'tipo'];

    protected static function boot()
    {
        parent::boot();

        // Remover a imagem ao deletar o registro
        static::deleting(function ($card) {
            if ($card->imagem) {
                Storage::disk('s3')->delete($card->imagem);
            }
        });

        // Remover a imagem antiga ao editar/trocar
        static::updating(function ($card) {
            // Verifica se o campo "imagem" foi alterado
            if ($card->isDirty('imagem')) {
                $originalImage = $card->getOriginal('imagem');

                // Deleta a imagem antiga do MinIO (S3)
                if ($originalImage) {
                    Storage::disk('s3')->delete($originalImage);
                }
            }
        });
    }
}
