<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'categorias',
        'facebook_id',
        'google_id',
        'linkedin_id',
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
                    'edit'  => false,
                    'type'  => 'ambas',
                    'subcategories' => [
                        ['name' => 'Outros', 'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'ambas', 'edit' => false],
                    ],
                ],
                [
                    'name'  => 'Alimentação',
                    'color' => 'cor__8',
                    'icon'  => 'mdi-silverware-variant',
                    'edit'  => false,
                    'type'  => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros', 'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'despesa', 'edit' => false],
                        ['name' => 'Almoço', 'color' => 'cor__8', 'icon' => 'mdi-food-fork-drink', 'type' => 'despesa', 'edit' => false],
                        ['name' => 'Lanche', 'color' => 'cor__8', 'icon' => 'mdi-food',            'type' => 'despesa', 'edit' => false],
                        ['name' => 'Café',   'color' => 'cor__8', 'icon' => 'mdi-coffee',          'type' => 'despesa', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Carro',
                    'color' => 'cor__2',
                    'icon' => 'mdi-car-estate',
                    'edit' => false,
                    'type' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros',         'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'despesa', 'edit' => false],
                        ['name' => 'Combustível',    'color' => 'cor__1', 'icon' => 'mdi-gas-station',     'type' => 'despesa', 'edit' => false],
                        ['name' => 'Estacionamento', 'color' => 'cor__1', 'icon' => 'mdi-parking',         'type' => 'despesa', 'edit' => false],
                        ['name' => 'Manutenção',     'color' => 'cor__1', 'icon' => 'mdi-wrench',          'type' => 'despesa', 'edit' => false],
                        ['name' => 'Seguros',        'color' => 'cor__1', 'icon' => 'mdi-security',        'type' => 'despesa', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Educação',
                    'color' => 'cor__3',
                    'icon' => 'mdi-account-school',
                    'edit' => false,
                    'type' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros',              'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal',        'type' => 'despesa', 'edit' => false],
                        ['name' => 'Mensalidade ',        'color' => 'cor__1', 'icon' => 'mdi-cash',                   'type' => 'despesa', 'edit' => false],
                        ['name' => 'Materiais Didáticos', 'color' => 'cor__1', 'icon' => 'mdi-book-open-page-variant', 'type' => 'despesa', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Familia',
                    'color' => 'cor__4',
                    'icon' => 'mdi-human-male-female-child',
                    'edit' => false,
                    'type' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros', 'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'despesa', 'edit' => false],
                        ['name' => 'Pet',    'color' => 'cor__1', 'icon' => 'mdi-paw',             'type' => 'despesa', 'edit' => false],
                        ['name' => 'Filhos', 'color' => 'cor__1', 'icon' => 'mdi-human-child',     'type' => 'despesa', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Investimentos',
                    'color' => 'cor__4',
                    'icon' => 'mdi-finance',
                    'edit' => false,
                    'type' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros', 'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal',        'type' => 'despesa', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Lazer',
                    'color' => 'cor__4',
                    'icon' => 'mdi-umbrella-beach',
                    'edit' => false,
                    'type' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros',  'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'despesa', 'edit' => false],
                        ['name' => 'Cinema',  'color' => 'cor__1', 'icon' => 'mdi-movie',           'type' => 'despesa', 'edit' => false],
                        ['name' => 'Parque',  'color' => 'cor__1', 'icon' => 'mdi-tree',            'type' => 'despesa', 'edit' => false],
                        ['name' => 'Teatro',  'color' => 'cor__1', 'icon' => 'mdi-drama-masks',     'type' => 'despesa', 'edit' => false],
                        ['name' => 'Viagens', 'color' => 'cor__1', 'icon' => 'mdi-airplane',        'type' => 'despesa',  'edit' => false],
                    ],
                ],
                [
                    'name' => 'Moradia',
                    'color' => 'cor__1',
                    'icon' => 'mdi-home-outline',
                    'type' => 'despesa',
                    'edit' => false,
                    'subcategories' => [
                        ['name' => 'Outros',         'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal',     'type' => 'despesa', 'edit' => false],
                        ['name' => 'Fixas',          'color' => 'cor__8', 'icon' => 'mdi-cash-multiple',       'type' => 'despesa', 'edit' => false],
                        ['name' => 'Limpeza',        'color' => 'cor__8', 'icon' => 'mdi-sahpe',               'type' => 'despesa', 'edit' => false],
                        ['name' => 'Mercado',        'color' => 'cor__8', 'icon' => 'mdi-cart',                'type' => 'despesa', 'edit' => false],
                        ['name' => 'Moveis/eletro',  'color' => 'cor__8', 'icon' => 'mdi-sahpe',               'type' => 'despesa', 'edit' => false],
                        ['name' => 'Aluguel',        'color' => 'cor__1', 'icon' => 'mdi-home-outline',        'type' => 'despesa', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Pagamentos',
                    'color' => 'cor__1',
                    'icon' => 'mdi-currency-usd',
                    'type' => 'despesa',
                    'edit' => false,
                    'subcategories' => [
                        ['name' => 'Outros',      'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'despesa', 'edit' => false],
                        ['name' => 'Empréstimos', 'color' => 'cor__8', 'icon' => 'mdi-currency-usd',    'type' => 'despesa', 'edit' => false],
                        ['name' => 'Taxas',       'color' => 'cor__8', 'icon' => 'mdi-percent',         'type' => 'despesa', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Saúde',
                    'color' => 'cor__7',
                    'icon' => 'mdi-medical-bag',
                    'edit' => false,
                    'type' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros',       'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'despesa', 'edit' => false],
                        ['name' => 'Academia',     'color' => 'cor__8', 'icon' => 'mdi-dumbbell',        'type' => 'despesa', 'edit' => false],
                        ['name' => 'Consultas',    'color' => 'cor__1', 'icon' => 'mdi-stethoscope',     'type' => 'despesa', 'edit' => false],
                        ['name' => 'Medicamentos', 'color' => 'cor__1', 'icon' => 'mdi-home-outline',    'type' => 'despesa', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Serviços',
                    'color' => 'cor__7',
                    'icon' => 'mdi-shape',
                    'edit' => false,
                    'type' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros',   'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'despesa', 'edit' => false],
                        ['name' => 'Musica',   'color' => 'cor__8', 'icon' => 'mdi-music',           'type' => 'despesa', 'edit' => false],
                        ['name' => 'Telefone', 'color' => 'cor__1', 'icon' => 'mdi-phone',           'type' => 'despesa', 'edit' => false],
                        ['name' => 'Internet', 'color' => 'cor__1', 'icon' => 'mdi-web',             'type' => 'despesa', 'edit' => false],
                        ['name' => 'Stream',   'color' => 'cor__1', 'icon' => 'mdi-view-stream',     'type' => 'despesa', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Transporte',
                    'color' => 'cor__2',
                    'icon' => 'mdi-bus',
                    'edit' => false,
                    'type' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros', 'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'despesa', 'edit' => false],
                        ['name' => 'Avião',  'color' => 'cor__1', 'icon' => 'mdi-airplane',        'type' => 'despesa', 'edit' => false],
                        ['name' => 'Metro',  'color' => 'cor__1', 'icon' => 'mdi-subway-variant',  'type' => 'despesa', 'edit' => false],
                        ['name' => 'Taxi',   'color' => 'cor__1', 'icon' => 'mdi-taxi',            'type' => 'despesa', 'edit' => false],
                        ['name' => 'Uber',   'color' => 'cor__1', 'icon' => 'mdi-car',             'type' => 'despesa', 'edit' => false],
                        ['name' => 'Ônibus', 'color' => 'cor__1', 'icon' => 'mdi-bus',             'type' => 'despesa', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Vestuário',
                    'color' => 'cor__5',
                    'icon' => 'mdi-tshirt-crew-outline',
                    'edit' => false,
                    'type' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Outros',   'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'despesa', 'edit' => false],
                        ['name' => 'Roupas',   'color' => 'cor__1', 'icon' => 'mdi-tshirt-crew',     'type' => 'despesa', 'edit' => false],
                        ['name' => 'Calçados', 'color' => 'cor__1', 'icon' => 'shoe-forma',          'type' => 'despesa', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Benefícios',
                    'color' => 'cor__12',
                    'icon' => 'mdi-gift',
                    'edit' => false,
                    'type' => 'receita',
                    'subcategories' => [
                        ['name' => 'Outros',       'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal',    'type' => 'receita', 'edit' => false],
                        ['name' => 'Alimentação',  'color' => 'cor__8', 'icon' => 'mdi-silverware-variant', 'type' => 'receita', 'edit' => false],
                        ['name' => 'Graduação',    'color' => 'cor__8', 'icon' => 'mdi-account-school',     'type' => 'receita', 'edit' => false],
                        ['name' => 'Refeição',     'color' => 'cor__8', 'icon' => 'mdi-food-fork-drink',    'type' => 'receita', 'edit' => false],
                        ['name' => 'Teletrabalho', 'color' => 'cor__8', 'icon' => 'mdi-lan-pending',        'type' => 'receita', 'edit' => false],
                        ['name' => 'Transporte',   'color' => 'cor__8', 'icon' => 'mdi-bus',                'type' => 'receita', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Comissão',
                    'color' => 'cor__12',
                    'icon' => 'mdi-account-cash',
                    'edit' => false,
                    'type' => 'receita',
                    'subcategories' => [
                        ['name' => 'Outros', 'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'receita', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Fixa mensal',
                    'color' => 'cor__12',
                    'icon' => 'mdi-calendar-check',
                    'edit' => false,
                    'type' => 'receita',
                    'subcategories' => [
                        ['name' => 'Outros', 'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'receita', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Pagamentos',
                    'color' => 'cor__1',
                    'icon' => 'mdi-currency-usd',
                    'type' => 'receita',
                    'edit' => false,
                    'subcategories' => [
                        ['name' => 'Outros',      'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'receita', 'edit' => false],
                        ['name' => 'Empréstimos', 'color' => 'cor__8', 'icon' => 'mdi-currency-usd',    'type' => 'receita', 'edit' => false],
                        ['name' => 'Taxas',       'color' => 'cor__8', 'icon' => 'mdi-percent',         'type' => 'receita', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Rendimentos',
                    'color' => 'cor__12',
                    'icon' => 'mdi-chart-bar',
                    'edit' => false,
                    'type' => 'receita',
                    'subcategories' => [
                        ['name' => 'Outros',   'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'receita', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Salário',
                    'color' => 'cor__12',
                    'icon' => 'mdi-currency-usd',
                    'edit' => false,
                    'type' => 'receita',
                    'subcategories' => [
                        ['name' => 'Outros',    'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'receita', 'edit' => false],
                        ['name' => 'Pagamento', 'color' => 'cor__8', 'icon' => 'mdi-currency-usd',    'type' => 'receita', 'edit' => false],
                        ['name' => 'vale',      'color' => 'cor__8', 'icon' => 'mdi-currency-usd',    'type' => 'receita', 'edit' => false],
                    ],
                ],

                [
                    'name' => 'Serviços',
                    'color' => 'cor__12',
                    'icon' => 'mdi-wrench',
                    'edit' => false,
                    'type' => 'receita',
                    'subcategories' => [
                        ['name' => 'Outros',   'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'receita', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Vendas',
                    'color' => 'cor__12',
                    'icon' => 'mdi-sale',
                    'edit' => false,
                    'type' => 'receita',
                    'subcategories' => [
                        ['name' => 'Outros',   'color' => 'cor__8', 'icon' => 'mdi-dots-horizontal', 'type' => 'receita', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Carteira',
                    'color' => 'cor__12',
                    'icon' => 'mdi-wallet',
                    'edit' => false,
                    'type' => 'contas',
                ],
                [
                    'name' => 'Conta Corrente',
                    'color' => 'cor__12',
                    'icon' => 'mdi-bank',
                    'edit' => false,
                    'type' => 'contas',
                ],
                [
                    'name' => 'Investimentos',
                    'color' => 'cor__12',
                    'icon' => 'mdi-bank',
                    'edit' => false,
                    'type' => 'contas',
                ],
                [
                    'name' => 'Outras',
                    'color' => 'cor__12',
                    'icon' => 'mdi-sahpe',
                    'edit' => false,
                    'type' => 'contas',
                ],
            ];

            foreach ($defaultCategories as $categoryData) {
                $category = $user->categories()->create([
                    'name'  => $categoryData['name'],
                    'color' => $categoryData['color'],
                    'icon'  => $categoryData['icon'],
                    'type'  => $categoryData['type'],
                    'edit'  => $categoryData['edit'],
                ]);

                if (isset($categoryData['subcategories'])) {
                    foreach ($categoryData['subcategories'] as $subcategoryData) {
                        $category->subcategories()->create([
                            'user_id' => $user->id,
                            'name'    => $subcategoryData['name'],
                            'color'   => $subcategoryData['color'],
                            'icon'    => $subcategoryData['icon'],
                            'type'    => $subcategoryData['type'],
                            'edit'    => $subcategoryData['edit'],
                        ]);
                    }
                }
            }
        });
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function subcategories()
    {
        return $this->hasMany(Subcategory::class);
    }


    public function expenses()
    {
        //hasMany (tem muitos)
        return $this->hasMany(Expense::class);
    }

    public function revenues()
    {
        //hasMany (tem muitos)
        return $this->hasMany(Revenue::class);
    }

    public function contas()
    {
        return $this->hasMany(Conta::class);
    }

    public function calculateTotalBalance()
    {
        $totalRevenues = $this->revenues()->sum('amount');
        $totalExpenses = $this->expenses()->sum('amount');
        return $totalRevenues - $totalExpenses;
    }

    public function calculateTotalCreditCard()
    {
        return $this->expenses()
            ->where('payment_method', 'credit_card')
            ->sum('amount');
    }

    // Rest omitted for brevity

    /**
     * Get the identifier that will be stored in the subject claim of the JWT.
     *
     * @return mixed
     */
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }
}
