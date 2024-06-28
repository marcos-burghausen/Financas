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
        'categoriasDespesas',
        'categoriasReceitas',
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
        'categoriasDespesas' => 'array',
        'categoriasReceitas' => 'array',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($user) {
            // Preenche categorias padrão se estiverem vazias
            if (is_null($user->categoriasDespesas)) {
                $user->categoriasDespesas = null;
            }
            if (is_null($user->categoriasReceitas)) {
                $user->categoriasReceitas = null;
            }
        });
    }

    protected function setCategoriasDespesasAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['categoriasDespesas'] = json_encode([
                ['name' => 'Casa',       'color' => 'cor__1', 'icon' => 'home-outline',           'edit' => false, 'typeCategory' => 'despesa'],
                ['name' => 'Transporte', 'color' => 'cor__2', 'icon' => 'car-estate',             'edit' => false, 'typeCategory' => 'despesa'],
                ['name' => 'Educação',   'color' => 'cor__3', 'icon' => 'account-school-outline', 'edit' => false, 'typeCategory' => 'despesa'],
                ['name' => 'Lazer',      'color' => 'cor__4', 'icon' => 'umbrella-beach-outline', 'edit' => false, 'typeCategory' => 'despesa'],
                ['name' => 'Vestuario',  'color' => 'cor__5', 'icon' => 'tshirt-crew-outline',    'edit' => false, 'typeCategory' => 'despesa'],
                ['name' => 'Viagem',     'color' => 'cor__6', 'icon' => 'airplane',               'edit' => false, 'typeCategory' => 'despesa'],
                ['name' => 'Saúde',      'color' => 'cor__7', 'icon' => 'medical-bag',            'edit' => false, 'typeCategory' => 'despesa'],
                ['name' => 'Outros',     'color' => 'cor__8', 'icon' => 'dots-horizontal',        'edit' => false, 'typeCategory' => 'despesa'],
            ]);
        } else {
            $this->attributes['categoriasDespesas'] = is_array($value) ? json_encode($value) : $value;
        }
    }

    protected function setCategoriasReceitasAttribute($value)
    {
        if (empty($value)) {
            $this->attributes['categoriasReceitas'] = json_encode([
                ['name' => 'Salario',       'color' => 'cor__12', 'icon' => 'currency-usd',    'edit' => false, 'typeCategory' => 'receita'],
                ['name' => 'Investimentos', 'color' => 'cor__11', 'icon' => 'finance',         'edit' => false, 'typeCategory' => 'receita'],
                ['name' => 'Outros',        'color' => 'cor__7',  'icon' => 'dots-horizontal', 'edit' => false, 'typeCategory' => 'receita'],
            ]);
        } else {
            $this->attributes['categoriasReceitas'] = is_array($value) ? json_encode($value) : $value;
        }
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
