# Desafio SESAB - Backend

Backend do projeto **Desafio SESAB**, desenvolvido em **Laravel 12** com **PostgreSQL**, estruturado no padrão **MVC** com **Repository Pattern**.\
Fornece API RESTful desacoplada com autenticação via **Bearer Token** e documentação via **L5-Swagger**.

---

## Primeiros passos

1. **Clonar o repositório**

```bash
git clone https://github.com/JimmyStarling/desafio-sesab-backend.git
cd desafio-sesab-backend
```

2. **Instalar dependências**

```bash
composer install
```

3. **Configurar **``

- Banco PostgreSQL: `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`
- Chave da aplicação:

```bash
php artisan key:generate
```

4. **Executar migrations e seeders**

```bash
php artisan migrate --seed
```

- Cria todas as tabelas e o usuário padrão:
  - Email: `roo@root.com`
  - Senha: `root`
- Esse usuário obtém o **token Bearer** e pode criar administradores, gestores e usuários padrão.

---

## Documentação da API

Após iniciar o servidor:

```bash
php artisan serve
```

- Acesse `http://localhost:8000/api/documentation` para a documentação completa.

---

## Perfis e permissões

- **Administrador (id=1)**: total controle; pode criar/atualizar/deletar qualquer usuário e atribuir qualquer perfil.

- **Gestor (id=2)**: cria usuários padrão ou gestores; não pode criar administradores.

- **Usuário Padrão (id=3)**: cria ou atualiza apenas seus próprios dados; sem acesso administrativo.

- **Usuário não autenticado**: qualquer registro novo recebe automaticamente `profile_id = 3` (Usuário Padrão).

---

## Estrutura do projeto

- **Models**: entidades (`User`, `Address`, `Profile`)

- **Controllers**: recebem requisições e delegam aos repositórios

- **Repositories**: encapsulam lógica de acesso a dados

- **Requests**: validação e autorização

- **Routes**: protegidas via `auth:sanctum` ou `auth:api`

- Relações **Many-to-Many** entre usuários e endereços via `address_user`.

---

## Autenticação

- Obtenha token Bearer:

```bash
POST /api/login
{
    "email": "roo@root.com",
    "password": "root"
}
```

- Inclua no header das requisições protegidas:

```
Authorization: Bearer <token>
```

---

## Considerações

- **Segurança**: validação de `profile_id` para evitar escalonamento de privilégios.
- **Boas práticas**: Repository Pattern, documentação Swagger, seeders para teste rápido.
- **Manutenção**: código segue PSR-12 e Laravel Best Practices; integração fácil com frontend desacoplado.

---

## Executando localmente

```bash
php artisan serve
```

- API disponível em `http://localhost:8000/api`.
- Use o token do usuário `roo@root.com` para criar e gerenciar usuários e endereços.

---