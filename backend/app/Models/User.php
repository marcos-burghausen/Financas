<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $primaryKey = 'id';
    protected $fillable = [
        'name',
        'email',
        "email_verified_at",
        'password',
        'type_user',
        'facebookId',
        'googleId',
        'linkedinId',
        'phone',
        'cpf',
        'date_of_birth',
        'profession',
        'bio',
        'avatar_url',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at'  => 'datetime',
        'password'           => 'hashed',
    ];

    /**
     * A relação principal e única para todos os lançamentos do usuário.
     */
    public function lancamentos()
    {
        return $this->hasMany(Lancamento::class);
    }

    /**
     * RELAÇÃO FILTRADA: Retorna a query para os lançamentos do tipo 'Receita'.
     */
    public function revenues()
    {
        return $this->hasMany(Lancamento::class)->where('tipo_lancamento', 'Receita');
    }

    /**
     * RELAÇÃO FILTRADA: Retorna a query para os lançamentos do tipo 'Despesa'.
     */
    public function expenses()
    {
        return $this->hasMany(Lancamento::class)->where('tipo_lancamento', 'Despesa');
    }

    public function contas()
    {
        return $this->hasMany(Conta::class);
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }

    /**
     * Relacionamento com configurações de notificação
     */
    public function notificationSettings()
    {
        return $this->hasOne(UserNotificationSettings::class);
    }

    /**
     * Obter ou criar configurações de notificação
     */
    public function getOrCreateNotificationSettings()
    {
        return $this->notificationSettings()->firstOrCreate(
            ['user_id' => $this->id]
        );
    }

    /**
     * Roles (perfis) do usuário
     */
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_user')
            ->withTimestamps();
    }

    /**
     * Verifica se o usuário possui uma role específica
     */
    public function hasRole(string $roleName): bool
    {
        return $this->roles()->where('name', $roleName)->exists();
    }

    /**
     * Verifica se o usuário possui qualquer uma das roles fornecidas
     */
    public function hasAnyRole(array $roleNames): bool
    {
        return $this->roles()->whereIn('name', $roleNames)->exists();
    }

    /**
     * Verifica se o usuário possui todas as roles fornecidas
     */
    public function hasAllRoles(array $roleNames): bool
    {
        $userRoles = $this->roles()->pluck('name')->toArray();
        return count(array_intersect($roleNames, $userRoles)) === count($roleNames);
    }

    /**
     * Verifica se o usuário possui uma permissão específica
     */
    public function hasPermission(string $permission): bool
    {
        foreach ($this->roles as $role) {
            if ($role->hasPermission($permission)) {
                return true;
            }
        }
        return false;
    }

    /**
     * Atribui uma ou mais roles ao usuário
     */
    public function assignRole(...$roles): void
    {
        $roleIds = collect($roles)->map(function ($role) {
            if (is_string($role)) {
                return Role::where('name', $role)->firstOrFail()->id;
            }
            return is_object($role) ? $role->id : $role;
        });

        $this->roles()->syncWithoutDetaching($roleIds);
    }

    /**
     * Remove uma ou mais roles do usuário
     */
    public function removeRole(...$roles): void
    {
        $roleIds = collect($roles)->map(function ($role) {
            if (is_string($role)) {
                return Role::where('name', $role)->firstOrFail()->id;
            }
            return is_object($role) ? $role->id : $role;
        });

        $this->roles()->detach($roleIds);
    }

    /**
     * Sincroniza as roles do usuário (remove antigas e adiciona novas)
     */
    public function syncRoles(...$roles): void
    {
        $roleIds = collect($roles)->map(function ($role) {
            if (is_string($role)) {
                return Role::where('name', $role)->firstOrFail()->id;
            }
            return is_object($role) ? $role->id : $role;
        });

        $this->roles()->sync($roleIds);
    }

    /**
     * Verifica se o usuário é admin ou tem acesso completo
     */
    public function isAdmin(): bool
    {
        return $this->hasAnyRole([Role::ADMIN, Role::FULL]);
    }

    protected static function boot()
    {
        parent::boot();

        static::created(function ($user) {
            // Default categories and subcategories
            $defaultCategories = [
                [
                    'name'  => 'Outros',
                    'color' => 'cor__8',
                    'icon'  => 'mdi-dots-horizontal',
                    'editable'  => false,
                    'type'  => 'ambas',
                    'subcategories' => [
                        ['name' => 'Outros', 'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'ambas', 'editable' => false],
                    ],
                ],
                [
                    'name'  => 'Alimentação',
                    'color' => 'cor__8',
                    'icon'  => 'mdi-silverware-variant',
                    'editable'  => false,
                    'type'  => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros', 'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'despesa', 'editable' => false],
                        ['name' => 'Almoço', 'color' => 'cor__8', 'icon' => 'mdi-food-fork-drink', 'type' => 'despesa', 'editable' => false],
                        ['name' => 'Lanche', 'color' => 'cor__8', 'icon' => 'mdi-food',            'type' => 'despesa', 'editable' => false],
                        ['name' => 'Café',   'color' => 'cor__8', 'icon' => 'mdi-coffee',          'type' => 'despesa', 'editable' => false],
                    ],
                ],
                [
                    'name' => 'Carro',
                    'color' => 'cor__2',
                    'icon' => 'mdi-car-estate',
                    'editable' => false,
                    'type' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros',         'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'despesa', 'editable' => false],
                        ['name' => 'Combustível',    'color' => 'cor__1', 'icon' => 'mdi-gas-station',     'type' => 'despesa', 'editable' => false],
                        ['name' => 'Estacionamento', 'color' => 'cor__1', 'icon' => 'mdi-parking',         'type' => 'despesa', 'editable' => false],
                        ['name' => 'Manutenção',     'color' => 'cor__1', 'icon' => 'mdi-wrench',          'type' => 'despesa', 'editable' => false],
                        ['name' => 'Seguros',        'color' => 'cor__1', 'icon' => 'mdi-security',        'type' => 'despesa', 'editable' => false],
                    ],
                ],
                [
                    'name' => 'Educação',
                    'color' => 'cor__3',
                    'icon' => 'mdi-account-school',
                    'editable' => false,
                    'type' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros',              'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal',        'type' => 'despesa', 'editable' => false],
                        ['name' => 'Mensalidade ',        'color' => 'cor__1', 'icon' => 'mdi-cash',                   'type' => 'despesa', 'editable' => false],
                        ['name' => 'Materiais Didáticos', 'color' => 'cor__1', 'icon' => 'mdi-book-open-page-variant', 'type' => 'despesa', 'editable' => false],
                    ],
                ],
                [
                    'name' => 'Familia',
                    'color' => 'cor__4',
                    'icon' => 'mdi-human-male-female-child',
                    'editable' => false,
                    'type' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros', 'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'despesa', 'editable' => false],
                        ['name' => 'Pet',    'color' => 'cor__1', 'icon' => 'mdi-paw',             'type' => 'despesa', 'editable' => false],
                        ['name' => 'Filhos', 'color' => 'cor__1', 'icon' => 'mdi-human-child',     'type' => 'despesa', 'editable' => false],
                    ],
                ],
                [
                    'name' => 'Investimentos',
                    'color' => 'cor__4',
                    'icon' => 'mdi-finance',
                    'editable' => false,
                    'type' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros', 'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal',        'type' => 'despesa', 'editable' => false],
                    ],
                ],
                [
                    'name' => 'Lazer',
                    'color' => 'cor__4',
                    'icon' => 'mdi-umbrella-beach',
                    'editable' => false,
                    'type' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros',  'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'despesa', 'editable' => false],
                        ['name' => 'Cinema',  'color' => 'cor__1', 'icon' => 'mdi-movie',           'type' => 'despesa', 'editable' => false],
                        ['name' => 'Parque',  'color' => 'cor__1', 'icon' => 'mdi-tree',            'type' => 'despesa', 'editable' => false],
                        ['name' => 'Teatro',  'color' => 'cor__1', 'icon' => 'mdi-drama-masks',     'type' => 'despesa', 'editable' => false],
                        ['name' => 'Viagens', 'color' => 'cor__1', 'icon' => 'mdi-airplane',        'type' => 'despesa',  'editable' => false],
                    ],
                ],
                [
                    'name' => 'Moradia',
                    'color' => 'cor__1',
                    'icon' => 'mdi-home-outline',
                    'type' => 'despesa',
                    'editable' => false,
                    'subcategories' => [
                        ['name' => 'Outros',         'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal',     'type' => 'despesa', 'editable' => false],
                        ['name' => 'Fixas',          'color' => 'cor__8', 'icon' => 'mdi-cash-multiple',       'type' => 'despesa', 'editable' => false],
                        ['name' => 'Limpeza',        'color' => 'cor__8', 'icon' => 'mdi-sahpe',               'type' => 'despesa', 'editable' => false],
                        ['name' => 'Mercado',        'color' => 'cor__8', 'icon' => 'mdi-cart',                'type' => 'despesa', 'editable' => false],
                        ['name' => 'Moveis/eletro',  'color' => 'cor__8', 'icon' => 'mdi-sahpe',               'type' => 'despesa', 'editable' => false],
                        ['name' => 'Aluguel',        'color' => 'cor__1', 'icon' => 'mdi-home-outline',        'type' => 'despesa', 'editable' => false],
                    ],
                ],
                [
                    'name' => 'Pagamentos',
                    'color' => 'cor__1',
                    'icon' => 'mdi-currency-usd',
                    'type' => 'despesa',
                    'editable' => false,
                    'subcategories' => [
                        ['name' => 'Outros',      'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'despesa', 'editable' => false],
                        ['name' => 'Empréstimos', 'color' => 'cor__8', 'icon' => 'mdi-currency-usd',    'type' => 'despesa', 'editable' => false],
                        ['name' => 'Taxas',       'color' => 'cor__8', 'icon' => 'mdi-percent',         'type' => 'despesa', 'editable' => false],
                    ],
                ],
                [
                    'name' => 'Saúde',
                    'color' => 'cor__7',
                    'icon' => 'mdi-medical-bag',
                    'editable' => false,
                    'type' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros',       'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'despesa', 'editable' => false],
                        ['name' => 'Academia',     'color' => 'cor__8', 'icon' => 'mdi-dumbbell',        'type' => 'despesa', 'editable' => false],
                        ['name' => 'Consultas',    'color' => 'cor__1', 'icon' => 'mdi-stethoscope',     'type' => 'despesa', 'editable' => false],
                        ['name' => 'Medicamentos', 'color' => 'cor__1', 'icon' => 'mdi-home-outline',    'type' => 'despesa', 'editable' => false],
                    ],
                ],
                [
                    'name' => 'Serviços',
                    'color' => 'cor__7',
                    'icon' => 'mdi-shape',
                    'editable' => false,
                    'type' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros',   'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'despesa', 'editable' => false],
                        ['name' => 'Musica',   'color' => 'cor__8', 'icon' => 'mdi-music',           'type' => 'despesa', 'editable' => false],
                        ['name' => 'Telefone', 'color' => 'cor__1', 'icon' => 'mdi-phone',           'type' => 'despesa', 'editable' => false],
                        ['name' => 'Internet', 'color' => 'cor__1', 'icon' => 'mdi-web',             'type' => 'despesa', 'editable' => false],
                        ['name' => 'Stream',   'color' => 'cor__1', 'icon' => 'mdi-view-stream',     'type' => 'despesa', 'editable' => false],
                    ],
                ],
                [
                    'name' => 'Transporte',
                    'color' => 'cor__2',
                    'icon' => 'mdi-bus',
                    'editable' => false,
                    'type' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros', 'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'despesa', 'editable' => false],
                        ['name' => 'Avião',  'color' => 'cor__1', 'icon' => 'mdi-airplane',        'type' => 'despesa', 'editable' => false],
                        ['name' => 'Metro',  'color' => 'cor__1', 'icon' => 'mdi-subway-variant',  'type' => 'despesa', 'editable' => false],
                        ['name' => 'Taxi',   'color' => 'cor__1', 'icon' => 'mdi-taxi',            'type' => 'despesa', 'editable' => false],
                        ['name' => 'Uber',   'color' => 'cor__1', 'icon' => 'mdi-car',             'type' => 'despesa', 'editable' => false],
                        ['name' => 'Ônibus', 'color' => 'cor__1', 'icon' => 'mdi-bus',             'type' => 'despesa', 'editable' => false],
                    ],
                ],
                [
                    'name' => 'Vestuário',
                    'color' => 'cor__5',
                    'icon' => 'mdi-tshirt-crew-outline',
                    'editable' => false,
                    'type' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros',   'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'despesa', 'editable' => false],
                        ['name' => 'Roupas',   'color' => 'cor__1', 'icon' => 'mdi-tshirt-crew',     'type' => 'despesa', 'editable' => false],
                        ['name' => 'Calçados', 'color' => 'cor__1', 'icon' => 'shoe-forma',          'type' => 'despesa', 'editable' => false],
                    ],
                ],
                [
                    'name' => 'Benefícios',
                    'color' => 'cor__12',
                    'icon' => 'mdi-gift',
                    'editable' => false,
                    'type' => 'receita',
                    'subcategories' => [
                        ['name' => 'Outros',       'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal',    'type' => 'receita', 'editable' => false],
                        ['name' => 'Alimentação',  'color' => 'cor__8', 'icon' => 'mdi-silverware-variant', 'type' => 'receita', 'editable' => false],
                        ['name' => 'Graduação',    'color' => 'cor__8', 'icon' => 'mdi-account-school',     'type' => 'receita', 'editable' => false],
                        ['name' => 'Refeição',     'color' => 'cor__8', 'icon' => 'mdi-food-fork-drink',    'type' => 'receita', 'editable' => false],
                        ['name' => 'Teletrabalho', 'color' => 'cor__8', 'icon' => 'mdi-lan-pending',        'type' => 'receita', 'editable' => false],
                        ['name' => 'Transporte',   'color' => 'cor__8', 'icon' => 'mdi-bus',                'type' => 'receita', 'editable' => false],
                    ],
                ],
                [
                    'name' => 'Comissão',
                    'color' => 'cor__12',
                    'icon' => 'mdi-account-cash',
                    'editable' => false,
                    'type' => 'receita',
                    'subcategories' => [
                        ['name' => 'Outros', 'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'receita', 'editable' => false],
                    ],
                ],
                [
                    'name' => 'Fixa mensal',
                    'color' => 'cor__12',
                    'icon' => 'mdi-calendar-check',
                    'editable' => false,
                    'type' => 'receita',
                    'subcategories' => [
                        ['name' => 'Outros', 'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'receita', 'editable' => false],
                    ],
                ],
                [
                    'name' => 'Pagamentos',
                    'color' => 'cor__1',
                    'icon' => 'mdi-currency-usd',
                    'type' => 'receita',
                    'editable' => false,
                    'subcategories' => [
                        ['name' => 'Outros',      'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'receita', 'editable' => false],
                        ['name' => 'Empréstimos', 'color' => 'cor__8', 'icon' => 'mdi-currency-usd',    'type' => 'receita', 'editable' => false],
                        ['name' => 'Taxas',       'color' => 'cor__8', 'icon' => 'mdi-percent',         'type' => 'receita', 'editable' => false],
                    ],
                ],
                [
                    'name' => 'Rendimentos',
                    'color' => 'cor__12',
                    'icon' => 'mdi-chart-bar',
                    'editable' => false,
                    'type' => 'receita',
                    'subcategories' => [
                        ['name' => 'Outros',   'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'receita', 'editable' => false],
                    ],
                ],
                [
                    'name' => 'Salário',
                    'color' => 'cor__12',
                    'icon' => 'mdi-currency-usd',
                    'editable' => false,
                    'type' => 'receita',
                    'subcategories' => [
                        ['name' => 'Outros',    'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'receita', 'editable' => false],
                        ['name' => 'Pagamento', 'color' => 'cor__8', 'icon' => 'mdi-currency-usd',    'type' => 'receita', 'editable' => false],
                        ['name' => 'vale',      'color' => 'cor__8', 'icon' => 'mdi-currency-usd',    'type' => 'receita', 'editable' => false],
                    ],
                ],

                [
                    'name' => 'Serviços',
                    'color' => 'cor__12',
                    'icon' => 'mdi-wrench',
                    'editable' => false,
                    'type' => 'receita',
                    'subcategories' => [
                        ['name' => 'Outros',   'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'receita', 'editable' => false],
                    ],
                ],
                [
                    'name' => 'Vendas',
                    'color' => 'cor__12',
                    'icon' => 'mdi-sale',
                    'editable' => false,
                    'type' => 'receita',
                    'subcategories' => [
                        ['name' => 'Outros',   'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'receita', 'editable' => false],
                    ],
                ],
                [
                    'name' => 'Carteira',
                    'color' => 'cor__12',
                    'icon' => 'mdi-wallet',
                    'editable' => false,
                    'type' => 'contas',
                ],
                [
                    'name' => 'Conta Corrente',
                    'color' => 'cor__12',
                    'icon' => 'mdi-bank',
                    'editable' => false,
                    'type' => 'contas',
                ],
                [
                    'name' => 'Investimentos',
                    'color' => 'cor__12',
                    'icon' => 'mdi-bank',
                    'editable' => false,
                    'type' => 'contas',
                ],
                [
                    'name' => 'Outras',
                    'color' => 'cor__12',
                    'icon' => 'mdi-sahpe',
                    'editable' => false,
                    'type' => 'contas',
                ],
            ];

            foreach ($defaultCategories as $categoryData) {
                $category = $user->categories()->create([
                    'name'  => $categoryData['name'],
                    'color' => $categoryData['color'],
                    'icon'  => $categoryData['icon'],
                    'type'  => $categoryData['type'],
                    'editable'  => $categoryData['editable'],
                ]);

                if (isset($categoryData['subcategories'])) {
                    foreach ($categoryData['subcategories'] as $subcategoryData) {
                        $category->subcategories()->create([
                            'user_id' => $user->id,
                            'name'    => $subcategoryData['name'],
                            'color'   => $subcategoryData['color'],
                            'icon'    => $subcategoryData['icon'],
                            'type'    => $subcategoryData['type'],
                            'editable'    => $subcategoryData['editable'],
                        ]);
                    }
                }
            }
        });
    }
}

    // public function calculateTotalBalance()
    // {
    //     $totalRevenues = $this->revenues()->sum('amount');
    //     $totalExpenses = $this->expenses()->sum('amount');
    //     return $totalRevenues - $totalExpenses;
    // }

    // public function calculateTotalCreditableCard()
    // {
    //     return $this->expenses()
    //         ->where('payment_method', 'creditable_card')
    //         ->sum('amount');
    // }

    

    // Rest omitted for brevity
