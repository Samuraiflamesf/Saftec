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

        static::deleting(function ($card) {
            if ($card->imagem) {
                Storage::disk('public')->delete($card->imagem);
            }
        });
    }
}
