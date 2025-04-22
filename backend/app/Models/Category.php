<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Category extends Model
{
    use HasFactory;
    protected $primaryKey = 'id';

    protected $fillable = ['user_id', 'name', 'color', 'icon', 'type', 'edit'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }
}
