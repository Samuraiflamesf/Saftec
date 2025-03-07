<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Card extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'image_path',
        'url',
        'type',
    ];

    protected static function boot()
    {
        parent::boot();

        // Remover a imagem ao deletar o registro
        static::deleting(function ($card) {
            if ($card->image_path) {
                Storage::disk('s3')->delete($card->image_path);
            }
        });

        // Remover a imagem antiga ao editar/trocar
        static::updating(function ($card) {
            // Verifica se o campo "image_path" foi alterado
            if ($card->isDirty('image_path')) {
                $originalImage = $card->getOriginal('image_path');

                // Deleta a imagem antiga do MinIO (S3)
                if ($originalImage) {
                    Storage::disk('s3')->delete($originalImage);
                }
            }
        });
    }
}
