<?php

namespace Tests\Feature;

use App\Models\Conta;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase;

    protected $token;
    protected $user;
    protected $expensesData;
    protected $revenuesData;
    protected $walletsData;


    /**
     * Configura o ambiente de teste criando um usuário e autenticando-o.
     * Este método é executado antes de cada teste na classe.
     */
    protected function setUp(): void
    {
        parent::setUp();

        // Envia uma requisição POST para a rota de criação de usuário
        // Simula o envio de dados de um novo usuário através da API
        $response = $this->postJson('/api/create', [
            'name' => 'João',
            'email' => 'joao@example.com',
            'password' => 'Teste123@'
        ]);

        // Verifica se a resposta da API tem o status 200 (OK)
        // e se contém a mensagem de sucesso esperada
        $response->assertStatus(200)
            ->assertJson(['success' => 'Usuario cadastrado com sucesso.']);

        //ou pode ser feito separados
        $response->assertStatus(200); 

        // Validar a estrutura e o conteúdo da resposta JSON
        $response->assertJson([
            'success' => 'Usuario cadastrado com sucesso.'
        ]);

        // Verificar se o usuário foi realmente criado no banco de dados
        $this->assertDatabaseHas('users', [
            'name' => 'João',
            'email' => 'joao@example.com',
        ]);

        // Busca o usuário recém-criado no banco de dados
        $user = User::where('email', 'joao@example.com')->first();
        
        // Garante que o usuário foi realmente criado e existe no banco
        $this->assertNotNull($user);

        // Verifica as categorias de despesas padrão
        $categoriasDespesas = $user->categoriasDespesas;

        // Confirma se foram criadas exatamente 8 categorias de despesas
        $this->assertCount(8, $categoriasDespesas);

        // Verifica se a primeira e a última categoria estão corretas
        // Isso ajuda a garantir que a ordem e o conteúdo estão como esperado
        $this->assertEquals('Casa', $categoriasDespesas[0]['name']);
        $this->assertEquals('Outros', $categoriasDespesas[7]['name']);

        // Verifica as categorias de receitas
        // Mesmo que inicialmente vazio, o campo deve existir como um array
        $categoriasReceitas = $user->categoriasReceitas;
        $this->assertIsArray($categoriasReceitas);

        // Busca a conta padrão criada para o usuário
        $conta = Conta::where('user_id', $user->id)->first();

        // Garante que conta foi realmente criada
        $this->assertNotNull($conta);
        
        // Verifica se todos os campos da conta padrão estão corretos
        $this->assertEquals('Pessoal', $conta->name);
        $this->assertEquals('cash', $conta->icon);
        $this->assertEquals('Carteira de uso pessoal', $conta->descricao);
        $this->assertEquals('Pessoal', $conta->tipo);




        // Autentica o usuário recém-criado
        $response = $this->postJson('/api/auth', [
            'email' => 'joao@example.com',
            'password' => 'Teste123@'
        ]);

        $response->assertStatus(200);

        // Verifica a estrutura da resposta
        $response->assertJsonStructure([
            'token' => [
                'expires',
                'expires_in',
                'iat',
                'token',
                'token_type',
            ],
            'user' => [
                'categoriasDespesas',
                'categoriasReceitas',
                'created_at',
                'email',
                'email_verified_at',
                'facebook_id',
                'google_id',
                'id',
                'linkedin_id',
                'name',
                'updated_at'
            ],
            'userData' => [
                'expensesData' => [
                    'ExpensesAddTotalValueMonth',
                    'ExpensesGroupByMonth',
                    'ExpensesMonth',
                    'TotalByCategoryExpenses',
                    'ValuePayExpenses',
                    'ValuePendingExpenses',
                    'ValueTotalExpensesMonth',
                ],
                'revenuesData' => [
                    'RevenuesAddTotalValueMonth',
                    'RevenuesGroupByMonth',
                    'RevenuesMonth',
                    'ValuePendingRevenues',
                    'ValueReceivedRevenues',
                    'ValueTotalRevenuesMonth',
                ],
                'walletsData' => [
                    'wallets',
                    'walletsNames',
                ],
            ],
        ]);

        $responseData = $response->json();
        $this->token = $responseData['token'];
        $this->user = $responseData['user'];
        // $this->expensesData = $responseData['userData']['expensesData'];
        // $this->revenuesData = $responseData['userData']['revenuesData'];
        // $this->walletsData = $responseData['userData']['walletsData'];
        
        
        
        // Verifica se o token está presente e é uma string não vazia
        $this->assertNotEmpty($response->json('token.token'));
        $this->assertIsString($response->json('token.token'));

        // Verifica se as informações do usuário estão corretas
        $this->assertEquals($user->id, $response->json('user.id'));
        $this->assertEquals($user->name, $response->json('user.name'));
        $this->assertEquals($user->email, $response->json('user.email'));

    }


public function testRevenues()
{
    $receita = [
        'valor' => 10000,
        'date' => '2024/10/10',
        'descricao' => 'teste',
        'categoria' => 'Casa',
        'carteira' => 'Pessoal',
        'status' => 'AGUARDANDO'
    ];

    // cadastrando uma receita
    $response = $this->withHeaders([
        'Authorization' => 'Bearer ' . $this->token['token'],
    ])->postJson('/api/save-revenue', $receita);

    $response->assertStatus(200)
            ->assertJson([
        'success' => 'Receita cadastrada com sucesso'
    ]);

    $response->assertJsonStructure([
        'success',
        'revenuesData' => [
            'ValueReceivedRevenues',
            'ValuePendingRevenues',
            'ValueTotalRevenuesMonth',
            'RevenuesGroupByMonth',
            'RevenuesAddTotalValueMonth',
            'RevenuesMonth',
        ],
        'walletsData',
    ]);

        // Verifica se a despesa foi salva no banco de dados
        $this->assertDatabaseHas('revenues', [
            'valor' => 10000,
            'date' => '2024/10/10',
            'descricao' => 'teste',
            'categoria' => 'Casa',
            'carteira' => 'Pessoal',
            'status' => 'AGUARDANDO'
        ]);

        $responseData = $response->json();
        $this->revenuesData = $responseData['revenuesData']['RevenuesGroupByMonth']['Oct'][0];
        info(['linha' => __LINE__],$this->revenuesData);

        // Verifica se o saldo da conta
        $wallet = Conta::where('name', 'Pessoal')->where('user_id', $this->user['id'])->first();

        $this->assertEquals(0, $wallet->saldo, 'O saldo da conta não foi atualizado corretamente');

        //recbendo uma receita
        $response = $this->withHeaders([
            'Authorization' => 'Bearer ' . $this->token['token'],
        ])->postJson('/api/edit-revenue', $this->revenuesData);

        $response->assertStatus(200)
                ->assertJson([
            'success' => 'Receita recebida com sucesso'
        ]);

    }

    // public function testEditandoUmaReceita()
    // {
    //     $this->testLançandoUmaReceita();
    //     $wallet = Conta::where('name', 'Pessoal')->where('user_id', $this->user['id'])->first();
    //     info(['linha' => __LINE__],['request' => $wallet]);
        
    //     $receita = [
    //         'id'        => 2,
    //         'valor'     => 5000,
    //         'date'      => '2024/10/10',
    //         'descricao' => 'teste',
    //         'categoria' => 'Casa',
    //         'carteira'  => 'Pessoal',
    //         'status'    => 'RECEBIDA'
    //     ];
    //     $response = $this->withHeaders([
    //         'Authorization' => 'Bearer ' . $this->token['token'],
    //     ])->postJson('/api/edit-revenue', $receita);
    //     $wallet = Conta::where('name', 'Pessoal')->where('user_id', $this->user['id'])->first();
    //     info(['linha' => __LINE__],['request' => $wallet]);
    
    //     info($response->json());
    
    //     $response->assertStatus(200)
    //             ->assertJson([
    //         'success' => 'Receita cadastrada com sucesso'
    //     ]);

    // }

}
