# 🐾 Petit Pet

Projeto escolar em **PHP + MySQL** para cadastro e acompanhamento de pets, organizado em duas áreas simples:

- **Área dos clientes** — página inicial, cadastro, login, cadastro de pets, consulta e edição dos próprios pets.
- **Área administrativa** — login restrito, painel resumido, gestão de clientes e de pets (criar, consultar, editar e excluir).

O visual preserva a identidade original da Petit Pet: rosa bebê `#ffceea`, azul bebê `#99d6ff`, off-white rosado `#fff2f2`, fonte **Cherry Bomb One** nos títulos e **PT Serif** nos textos, com o logotipo 🐾 Petit Pet.

---

## Funcionalidades

### Área dos clientes

- Página inicial com apresentação, pets em destaque e resumo.
- Listagem pública de pets com busca e filtro por situação.
- Página de detalhes do pet.
- Cadastro de cliente com login automático após a criação da conta.
- Login e logout com sessão segura.
- Painel do cliente com seus pets, atalhos e resumo.
- Cadastro de pet vinculado à conta do cliente (tutor preenchido automaticamente).
- Edição e exclusão apenas dos próprios pets.
- O telefone do tutor não aparece publicamente; somente a administração e o próprio tutor visualizam.

### Área administrativa

- Login separado em `login.php?area=admin`, aceitando somente contas com perfil `admin`.
- Painel com números simples: total de pets, disponíveis, em atendimento e clientes.
- Lista de pets com busca por nome, espécie, raça, tutor, conta ou situação.
- Cadastro, edição e exclusão de pets.
- Lista de clientes com busca por nome ou e-mail.
- Cadastro, edição e exclusão de contas de clientes.
- Ao excluir um cliente, os pets continuam cadastrados com os dados do tutor; apenas o vínculo com a conta é removido.

---

## Tecnologias

- **PHP 8+** (testado em PHP 8.5)
- **MySQL 8+**
- HTML5, CSS3 e JavaScript sem frameworks
- Fontes servidas localmente em `assets/fonts/` (sem depender do Google Fonts)

---

## Como executar

### 1. Requisitos

- PHP com extensões `mysqli` e `session`
- MySQL 8 em execução
- Um servidor local (XAMPP, Laragon, WAMP ou o servidor embutido do PHP)

### 2. Configuração do banco

As credenciais padrão ficam em `config/config.php`:

```php
'db_host' => 'localhost',
'db_usuario' => 'root',
'db_senha' => 'root',
'db_nome' => 'biblioteca',
```

Se o seu ambiente for diferente, copie `config/config.local.example.php` para `config/config.local.php` e ajuste os valores. Esse arquivo não é versionado.

Também é possível usar variáveis de ambiente: `PETIT_DB_HOST`, `PETIT_DB_USUARIO`, `PETIT_DB_SENHA`, `PETIT_DB_NOME` e `PETIT_DEBUG`.

### 3. Criar a estrutura

**Instalação nova** (banco vazio):

```bash
mysql -u root -p --default-character-set=utf8mb4 -e "source database/schema.sql"
```

**Banco que já possui a estrutura antiga** (tabelas `usuarios` e `pets` sem as colunas novas):

```bash
mysql -u root -p --default-character-set=utf8mb4 -e "source database/migrate_areas.sql"
```

> A migração deve ser executada **uma única vez**. Ela adiciona `usuarios.telefone`, `usuarios.perfil`, `usuarios.criado_em`, `pets.usuario_id` e a chave estrangeira correspondente.

### 4. Dados de demonstração (opcional)

```bash
mysql -u root -p --default-character-set=utf8mb4 -e "source database/seed.sql"
```

O seed não duplica os pets se for executado novamente.

### 5. Iniciar o servidor

Na pasta do projeto:

```bash
php -S 127.0.0.1:8010
```

Acesse: <http://127.0.0.1:8010>

Também funciona normalmente dentro do `htdocs` do XAMPP.

---

## Contas de demonstração

| Área | E-mail | Senha |
| --- | --- | --- |
| Cliente | `cliente@petitpet.local` | `Cliente@123` |
| Administração | `admin@petitpet.local` | `Admin@123` |

> **Importante:** troque essas senhas antes de publicar o projeto. As contas são apenas para a apresentação escolar.

A cliente de demonstração **Ana Souza** já possui os pets **Mel** e **Luna** vinculados à conta.

---

## Estrutura de arquivos

```text
petit_pet/
├── index.php                 Página inicial
├── pets.php                  Listagem pública de pets
├── pet.php                   Detalhes do pet
├── login.php                 Login de cliente e administração
├── cadastro.php              Cadastro de cliente
├── logout.php                Encerramento da sessão
├── painel.php                Painel do cliente
├── cadastrar_pet.php         Cadastro de pet (cliente ou admin)
├── editar_pet.php            Edição de pet (cliente ou admin)
├── excluir_pet.php           Exclusão de pet (somente POST)
├── admin.php                 Painel administrativo
├── admin_pets.php            Lista e busca de pets
├── admin_clientes.php        Lista, cadastro, edição e exclusão de clientes
├── conexao.php               Conexão única com o MySQL
├── config/
│   ├── config.php            Configuração central
│   └── config.local.example.php
├── includes/
│   ├── bootstrap.php         Inicialização, sessão e proteção de acesso
│   ├── funcoes.php           Helpers, mensagens, CSRF e validações
│   ├── clientes.php          Consultas da tabela usuarios
│   ├── pets.php              Consultas da tabela pets
│   ├── form_pet.php          Formulário compartilhado de pet
│   ├── cartao_pet.php        Cartão de pet reutilizável
│   └── layout/
│       ├── header.php        Cabeçalho e navegação
│       └── footer.php        Rodapé
├── assets/
│   ├── css/style.css         Estilos do site
│   ├── css/fontes.css        Declaração das fontes locais
│   ├── fonts/                Cherry Bomb One e PT Serif
│   ├── js/app.js             Confirmações e interações leves
│   └── favicon.svg
├── database/
│   ├── schema.sql            Estrutura para instalação nova
│   ├── migrate_areas.sql     Migração para a versão com duas áreas
│   └── seed.sql              Dados de demonstração
└── README.md
```

---

## Funcionamento das permissões

- `usuarios.perfil` define o tipo de conta: `cliente` ou `admin`.
- Páginas administrativas usam `exigir_admin()`; clientes são redirecionados ao próprio painel.
- O cadastro de cliente sempre cria uma conta com perfil `cliente`.
- O login administrativo recusa contas de cliente.
- Clientes só editam e excluem pets vinculados à própria conta (`pets.usuario_id`).
- A administração pode editar qualquer pet, definir a situação e manter dados de tutor livres.
- Ao criar um pet pela área do cliente, o nome e o telefone do tutor vêm da conta; a situação inicia como **Disponível**.

---

## Segurança implementada

- Consultas preparadas em todas as operações com o banco.
- Escape de HTML na exibição de dados.
- Tokens CSRF em formulários de escrita.
- Senhas com `password_hash()` / `password_verify()`.
- Sessão com cookie `HttpOnly`, `SameSite=Lax` e regeneração de ID no login.
- Bloqueio temporário após cinco tentativas de login inválidas.
- Cabeçalhos de segurança básicos (`nosniff`, `SAMEORIGIN`, `Referrer-Policy`).
- Exclusões somente por `POST`.
- Telefone do tutor protegido na área pública.

---

## Observações para a apresentação

- Não há gráficos, integrações externas nem funcionalidades simuladas: todos os cadastros, consultas, edições e exclusões usam o banco de dados real.
- O banco mantém o nome `biblioteca` por compatibilidade com o ambiente escolar original.
- Contas antigas de teste podem aparecer na lista de clientes; elas podem ser removidas pela área administrativa.
