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
                    'name' => 'Casa',
                    'color' => 'cor__1',
                    'icon' => 'home-outline',
                    'type_category' => 'despesa',
                    'edit' => false,
                    'subcategories' => [
                        ['name' => 'Aluguel', 'edit' => false],
                        ['name' => 'Contas de Luz', 'edit' => false],
                        ['name' => 'Contas de Água', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Transporte',
                    'color' => 'cor__2',
                    'icon' => 'car-estate',
                    'edit' => false,
                    'type_category' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Combustível', 'edit' => false],
                        ['name' => 'Manutenção', 'edit' => false],
                        ['name' => 'Transporte Público', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Educação',
                    'color' => 'cor__3',
                    'icon' => 'account-school-outline',
                    'edit' => false,
                    'type_category' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Mensalidade Escolar', 'edit' => false],
                        ['name' => 'Materiais Didáticos', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Lazer',
                    'color' => 'cor__4',
                    'icon' => 'umbrella-beach-outline',
                    'edit' => false,
                    'type_category' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Cinema', 'edit' => false],
                        ['name' => 'Restaurantes', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Vestuário',
                    'color' => 'cor__5',
                    'icon' => 'tshirt-crew-outline',
                    'edit' => false,
                    'type_category' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Roupas', 'edit' => false],
                        ['name' => 'Calçados', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Viagem',
                    'color' => 'cor__6',
                    'icon' => 'airplane',
                    'edit' => false,
                    'type_category' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Passagens', 'edit' => false],
                        ['name' => 'Hospedagem', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Saúde',
                    'color' => 'cor__7',
                    'icon' => 'medical-bag',
                    'edit' => false,
                    'type_category' => 'despesa',
                    'subcategories' => [
                        ['name' => 'Medicamentos', 'edit' => false],
                        ['name' => 'Consultas', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Outros',
                    'color' => 'cor__8',
                    'icon' => 'dots-horizontal',
                    'edit' => false,
                    'type_category' => 'despesa',
                    'subcategories' => [],
                ],
                [
                    'name' => 'Salário',
                    'color' => 'cor__12',
                    'icon' => 'currency-usd',
                    'edit' => false,
                    'type_category' => 'receita',
                    'subcategories' => [
                        ['name' => 'Salário Mensal', 'edit' => false],
                        ['name' => 'Bônus', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Investimentos',
                    'color' => 'cor__11',
                    'icon' => 'finance',
                    'edit' => false,
                    'type_category' => 'receita',
                    'subcategories' => [
                        ['name' => 'Dividendos', 'edit' => false],
                        ['name' => 'Juros', 'edit' => false],
                    ],
                ],
                [
                    'name' => 'Outros',
                    'color' => 'cor__7',
                    'icon' => 'dots-horizontal',
                    'edit' => false,
                    'type_category' => 'receita',
                    'subcategories' => [],
                ],
            ];

            foreach ($defaultCategories as $categoryData) {
                $category = $user->categories()->create([
                    'name' => $categoryData['name'],
                    'color' => $categoryData['color'],
                    'icon' => $categoryData['icon'],
                    'type_category' => $categoryData['type_category'],
                    'edit' => $categoryData['edit'],
                ]);

                foreach ($categoryData['subcategories'] as $subcategoryData) {
                    $category->subcategories()->create([
                        'user_id' => $user->id,
                        'name' => $subcategoryData['name'],
                        'edit' => $subcategoryData['edit'],
                    ]);
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
