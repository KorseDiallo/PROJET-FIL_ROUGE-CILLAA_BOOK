<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @method static find(Invertissement $id)
 */
class Invertissement extends Model
{
    use HasFactory;

    protected $fillable = [
        'montant',
        'description',
        'status',
        'user_id',
        'projet_id'
    ];

    public function User()
    {
        return $this->belongsTo(User::class);
    }

    public function projet()
    {
        return $this->belongsTo(Projet::class);
    }
}
