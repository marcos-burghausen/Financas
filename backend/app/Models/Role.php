<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    protected $fillable = [
        'name',
        'display_name',
        'description',
        'permissions',
    ];

    protected $casts = [
        'permissions' => 'array',
    ];

    /**
     * Usuários que possuem esta role
     */
    public function users()
    {
        return $this->belongsToMany(User::class, 'role_user')
            ->withTimestamps();
    }

    /**
     * Verifica se a role possui uma permissão específica
     */
    public function hasPermission(string $permission): bool
    {
        if (!$this->permissions) {
            return false;
        }

        return in_array($permission, $this->permissions);
    }

    /**
     * Constantes para os nomes das roles
     */
    public const USER = 'USER';
    public const TRADER = 'TRADER';
    public const USER_TRADER = 'USER_TRADER';
    public const ADMIN = 'ADMIN';
    public const FULL = 'FULL';
}
