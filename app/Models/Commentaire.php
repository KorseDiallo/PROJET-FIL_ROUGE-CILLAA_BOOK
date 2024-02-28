<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static create(array $all)
 */
class Commentaire extends Model
{
    use HasFactory;
    protected  $fillable =
        [
            'nom',
            'email',
            'contenu',
            'article_id',
        ];
    public function article() {
        return $this->belongsTo(Article::class);
    }
}
