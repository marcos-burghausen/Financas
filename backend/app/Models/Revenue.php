<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Revenue extends Model
{
    use HasFactory;

    protected $primaryKey = 'id';
    protected $filable = [
        'valor',
        'date',
        'descricao',
        'categoria',
        'carteira',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
