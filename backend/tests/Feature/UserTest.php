<?php

namespace Tests\Feature;

use App\Models\Conta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;
    /**
     * A basic feature test example.
     */
    public function test_creat_user(): void
    {
        
        try {
            DB::beginTransaction();
            $user = new User();
            $user->name = "João";
            $user->email = "joão@gmail.com";
            $user->categoriasDespesas = [
                ['name' => 'Casa',       'color' => 'cor__1', 'icon' => 'home-outline',           'edit' => false, 'typeCategory' => 'despesa'],
                ['name' => 'Transporte', 'color' => 'cor__2', 'icon' => 'car-estate',             'edit' => false, 'typeCategory' => 'despesa'],
                ['name' => 'Educação',   'color' => 'cor__3', 'icon' => 'account-school-outline', 'edit' => false, 'typeCategory' => 'despesa'],
                ['name' => 'Lazer',      'color' => 'cor__4', 'icon' => 'umbrella-beach-outline', 'edit' => false, 'typeCategory' => 'despesa'],
                ['name' => 'Vestuario',  'color' => 'cor__5', 'icon' => 'tshirt-crew-outline',    'edit' => false, 'typeCategory' => 'despesa'],
                ['name' => 'Viagem',     'color' => 'cor__6', 'icon' => 'airplane',               'edit' => false, 'typeCategory' => 'despesa'],
                ['name' => 'Saúde',      'color' => 'cor__7', 'icon' => 'medical-bag',            'edit' => false, 'typeCategory' => 'despesa'],
                ['name' => 'Outros',     'color' => 'cor__8', 'icon' => 'dots-horizontal',        'edit' => false, 'typeCategory' => 'despesa'],
            ];
            $user->categoriasReceitas = [
                ['name' => 'Salario',       'color' => 'cor__12', 'icon' => 'currency-usd',    'edit' => false, 'typeCategory' => 'receita'],
                ['name' => 'Investimentos', 'color' => 'cor__11', 'icon' => 'finance',         'edit' => false, 'typeCategory' => 'receita'],
                ['name' => 'Outros',        'color' => 'cor__7',  'icon' => 'dots-horizontal', 'edit' => false, 'typeCategory' => 'receita'],
            ];
            $user->password = password_hash(
                "Teste123@",
                PASSWORD_ARGON2I
            );
            $user->save();

            // Assertion 1: Verificar se o usuário foi criado
            $this->assertDatabaseHas('users', [
                'name' => 'João',
                'email' => 'joão@gmail.com'
            ]);

            $lastUser = User::latest('id')->first();

            // Assertion 2: Verificar se o último usuário criado é o correto
            $this->assertEquals('João', $lastUser->name);
            $this->assertEquals('joão@gmail.com', $lastUser->email);
                
            $carteira            = new Conta;
            $carteira->user_id   = $lastUser->id;
            $carteira->name      = "Pessoal";
            $carteira->icon      = "cash";
            $carteira->descricao = "Carteira de uso pessoal";
            $carteira->tipo      = "Pessoal";
            $saved               = $carteira->save();

            // Assertion 3: Verificar se a carteira foi criada
            $this->assertDatabaseHas('contas', [
                'user_id' => $lastUser->id,
                'name' => 'Pessoal',
                'icon' => 'cash',
                'descricao' => 'Carteira de uso pessoal',
                'tipo' => 'Pessoal'
            ]);

            // Assertion 4: Verificar se a operação de salvar a carteira foi bem-sucedida
            $this->assertTrue($saved);

            DB::commit();

            // Assertion 5: Verificar se as categorias foram salvas corretamente
            $this->assertCount(8, $lastUser->categoriasDespesas);
            $this->assertCount(3, $lastUser->categoriasReceitas);

        } catch (\Throwable $th) {
            DB::rollBack();
            $this->fail('An exception was thrown: ' . $th->getMessage());
        }
    }
}
