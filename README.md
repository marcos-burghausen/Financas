<h1 align="center">
    <img width="200px" src="./frontend/src/assets/img/3.png" />
</h1>

<h2>
    Sobre
</h2>
O Mr Finanças é uma aplicação desenvolvida para auxiliar na gestão eficiente e organizada das finanças individuais. Com uma interface intuitiva esta ferramenta proporciona aos usuários uma visão abrangente de suas receitas, despesas e metas financeiras.

## Índice
- <a href="#funcionalidades-do-projeto">Funcionalidades do Projeto</a>
- <a href="#layout">Layout</a>
- <a href="#como-rodar-este-projeto?">Como rodar este projeto?</a>
- <a href="#ferramentas">Ferramentas</a>
- <a href="#próximos-passos">Próximos passos</a>

## Funcionalidades do Projeto

- [x] Cadastro de usuário
- [x] Login
- [x] Cadastro de receitas e despesas
- [x] Visualização de receitas e despesas do mes corrente
- [x] 

## Layout

![tela de cadastro](./frontend/src/assets/readme/cadastro.png)
![tela de login](./frontend/src/assets/readme/login.png)
![tela de dashboard](./frontend/src/assets/readme/dashboard.png)
![tela de receitas](./frontend/src/assets/readme/receitas.png)
![tela de despesas](./frontend/src/assets/readme/despesas.png)
![tela de categorias](./frontend/src/assets/readme/categorias.png)

## Como rodar este projeto?

### Será necessario ter instalado em sua máquina o docker compose

```bash
# Clone este repositório
$ git clone https://github.com/marcos-burghausen/Financas.git

# Acesse a pasta do prjeto no seu terminal
$ cd Finacas

# Inicie os containers
$ docker compose up -d

# Acesse o bash do backend
$ docker exec -it Mr_backend bash

# Instale as dependencias
$ composer install
```
### Renomeie o arquivo .env.example na pasta backend
### Substitua as linhas 11 a 16 pelo código a seguir
```bash
DB_CONNECTION=mysql
DB_HOST=mysql
DB_PORT=3306
DB_DATABASE=Mr_database
DB_USERNAME=user
DB_PASSWORD=user
```
```bash
# Gere o códgo jwt que aplicação usara para fazer a autenticação do usuario
php artisan jwt:secret
```

### Acesse a aplicação em (http://localhost:4081)

## Ferramentas
1. [Laravel 10](https://laravel.com/)
2. [Vue.js 3](https://vuejs.org/)
3. [Typescript](https://www.typescriptlang.org/)
3. [jwt-auth](https://jwt-auth.readthedocs.io/en/develop/)

## Próximos Passos

- [ ] Funcionalidade de despesas para cartão de crédito