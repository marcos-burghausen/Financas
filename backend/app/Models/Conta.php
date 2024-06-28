<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Conta extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'valor',
        'descricao',
        'tipo',
        'user_id',
        'icon'
    ];

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
