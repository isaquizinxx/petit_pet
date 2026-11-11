# 🐾 Petit Pet

Sistema web de cadastro e acompanhamento de pets, feito em **PHP + MySQL**, com interface responsiva e foco em organização, cuidado e simplicidade.

O projeto começou como um exercício de CRUD e foi modernizado: a estrutura antiga de biblioteca (`livros`, `titulo`, `autor`) foi migrada para uma modelagem própria de pets, sem descartar os dados existentes.

---

## ✨ Funcionalidades

- Cadastro de usuários com senha criptografada (`password_hash` / `password_verify`).
- Login, logout e sessão protegida.
- Painel com resumo real: total de pets, disponíveis, em atendimento e tutores.
- Cadastro completo de pets:
  - nome, espécie, raça, sexo, data de nascimento e porte;
  - tutor e telefone;
  - observações;
  - situação (disponível ou em atendimento).
- Listagem com busca por nome, espécie, raça ou tutor.
- Filtro por situação.
- Ordenação alfabética e paginação.
- Edição e exclusão com confirmação.
- Mensagens de feedback (flash) após cada operação.
- Layout responsivo para computador, tablet e celular.
- Acessibilidade básica: navegação por teclado, foco visível, textos alternativos e labels.

---

## 🧰 Tecnologias

| Camada | Tecnologia |
|---|---|
| Back-end | PHP 8.1+ (testado em PHP 8.5.3) |
| Banco | MySQL 8.0 / MariaDB compatível |
| Driver | `mysqli` com consultas preparadas |
| Front-end | HTML5, CSS3 e JavaScript puro |
| Fontes | Locais (`assets/fonts`), sem dependência de internet |
| Versionamento | Git |

Sem frameworks, sem Composer e sem APIs externas obrigatórias.

---

## 📁 Estrutura

```
petit_pet/
├── index.php                  Página inicial pública
├── login.php                  Formulário de login
├── cadastro.php               Formulário de cadastro de usuário
├── painel.php                 Painel principal
├── animais_cadastrados.php    Listagem, busca, filtro e paginação
├── cadastrar_animal.php       Formulário de novo pet
├── editar_animal.php          Formulário de edição
├── autenticar.php             Processa o login
├── salvar_usuario.php         Processa o cadastro de usuário
├── salvar_animal.php          Processa o cadastro de pet
├── atualizar_animal.php       Processa a edição
├── excluir_animal.php         Processa a exclusão
├── logout.php                 Encerra a sessão
├── conexao.php                Conexão única com o MySQL
├── verificar_sessao.php       Compatibilidade: exige login
├── config/
│   ├── config.php             Configuração central
│   ├── config.local.php       Credenciais locais (não versionado)
│   └── config.local.example.php
├── includes/
│   ├── bootstrap.php          Inicialização comum
│   ├── helpers.php            Funções utilitárias
│   ├── sessao.php             Sessão e cookies seguros
│   ├── auth.php               Autenticação e bloqueio por tentativas
│   ├── csrf.php               Proteção CSRF
│   ├── flash.php              Mensagens de feedback
│   ├── validacao.php          Validação de usuário e pet
│   ├── pets.php               Consultas e regras de pets
│   ├── form_pet.php           Formulário compartilhado
│   └── layout/
│       ├── header.php
│       └── footer.php
├── assets/
│   ├── css/
│   │   ├── fontes.css         Fontes locais (@font-face)
│   │   └── style.css          Estilos da interface
│   ├── fonts/                 Arquivos .woff2 (Cherry Bomb One e Nunito Sans)
│   ├── favicon.svg            Ícone da aba do navegador
│   └── js/app.js
├── database/
│   ├── schema.sql             Instalação nova
│   ├── migrate_pets.sql       Migração da estrutura antiga
│   └── seed.sql               Dados de demonstração (opcional)
└── README.md
```

---

## 🚀 Como executar localmente

### 1. Requisitos

- PHP 8.1 ou superior com a extensão `mysqli`.
- MySQL 8.0 (ou MariaDB) em execução.
- Um terminal (PowerShell, CMD ou terminal do VS Code).

Para conferir o PHP:

```powershell
php -v
php -m | Select-String mysqli
```

### 2. Banco de dados

**Banco novo (instalação limpa):**

```powershell
$mysql = "C:\Program Files\MySQL\MySQL Server 8.0\bin\mysql.exe"
& $mysql -u root -p --default-character-set=utf8mb4 -e "source database/schema.sql"
```

**Banco que já existe com a estrutura antiga (`usuarios` e `livros`):**

```powershell
& $mysql -u root -p --default-character-set=utf8mb4 -e "source database/migrate_pets.sql"
```

A migração copia os registros de `livros` para `pets` e mantém a tabela antiga intacta como backup.

**Dados de demonstração (opcional):**

```powershell
& $mysql -u root -p --default-character-set=utf8mb4 -e "source database/seed.sql"
```

> **Acentuação:** execute os comandos de dentro da pasta do projeto e sempre com `--default-character-set=utf8mb4`.
> No PowerShell, evite `Get-Content arquivo.sql | mysql`: o pipe pode converter os acentos para `?` e corromper os dados.
> No CMD, o redirecionamento funciona normalmente: `mysql -u root -p --default-character-set=utf8mb4 < database\schema.sql`.

> A seed cria a conta `demo@petitpet.local` com a senha `Petit@123` e alguns pets de exemplo. Use apenas em ambiente local/escolar.

### 3. Configuração

Copie o exemplo e ajuste as credenciais:

```powershell
Copy-Item config/config.local.example.php config/config.local.php
```

Edite `config/config.local.php` se o seu MySQL usar outro usuário, senha ou nome de banco.

Também é possível usar variáveis de ambiente:

| Variável | Padrão |
|---|---|
| `PETIT_DB_HOST` | `localhost` |
| `PETIT_DB_USUARIO` | `root` |
| `PETIT_DB_SENHA` | `root` |
| `PETIT_DB_NOME` | `biblioteca` |
| `PETIT_DEBUG` | `0` |

Exemplo no PowerShell:

```powershell
$env:PETIT_DB_SENHA = "sua_senha"
$env:PETIT_DEBUG = "1"
php -S localhost:8000
```

### 4. Subir o servidor

```powershell
cd "caminho\para\petit_pet"
php -S localhost:8000
```

Abra no navegador:

**http://localhost:8000/**

Também funciona em Apache/XAMPP, desde que a pasta esteja dentro de `htdocs` e o MySQL esteja ativo.

---

## 🔑 Conta de demonstração

Se você executou `database/seed.sql`:

- **E-mail:** `demo@petitpet.local`
- **Senha:** `Petit@123`

Troque ou remova essa conta antes de publicar o sistema.

---

## 🔒 Segurança implementada

- Consultas preparadas em todas as operações com dados de usuário.
- CSRF em login, cadastro, edição e exclusão.
- Token CSRF estável por sessão (não quebra ao usar duas abas).
- Validação e normalização de e-mail.
- Política mínima de senha (8 a 72 caracteres) e confirmação no cadastro.
- `session_regenerate_id()` após o login.
- Cookies de sessão com `HttpOnly`, `SameSite=Lax` e `Secure` quando em HTTPS.
- Modo estrito de sessão (`session.use_strict_mode`) e uso exclusivo de cookies.
- Bloqueio temporário após 5 tentativas de login inválidas (por sessão).
- Cabeçalhos de segurança: `X-Content-Type-Options`, `X-Frame-Options`, `Referrer-Policy` e `Permissions-Policy`.
- Escape de saída com `htmlspecialchars` em todos os dados exibidos.
- Cabeçalhos de cache desativados em páginas autenticadas.
- Erros do banco registrados no log, sem expor detalhes ao usuário.
- Credenciais locais fora do controle de versão.

> Para produção, ainda é recomendado: HTTPS, política de senha mais forte, recuperação de senha por e-mail, limitação de tentativas por IP e revisão de permissões/usuários do MySQL.

---

## 🧪 Testes realizados

- `php -l` em todos os arquivos PHP (sem erros de sintaxe).
- 26 testes HTTP automatizados cobrindo:
  - acesso às páginas públicas e redirecionamento das páginas protegidas;
  - cadastro de usuário, validação, e-mail duplicado e senhas diferentes;
  - login com credenciais válidas e inválidas;
  - tentativa de login com payload de injeção SQL;
  - requisições POST sem token CSRF (login e exclusão);
  - cadastro, validação de data futura, busca, filtro, edição e exclusão de pet;
  - paginação e limite de página fora do intervalo;
  - logout e invalidação da sessão.
- Layout verificado no Microsoft Edge em 1440×900 (desktop), 768×1024 (tablet) e 390×844 (celular), sem overflow horizontal; a tabela se transforma em cartões com rótulos no celular.
- Importação do banco validada com acentuação UTF-8 (`Siamês`, `Demonstração`, `Observação`).

---

## ⚠️ Observações e limitações

- **Dados compartilhados:** todos os usuários logados veem os mesmos pets. Isso é intencional no escopo de demonstração escolar. Para uso real, adicione vínculo `pet -> tutor/usuário` e papéis de acesso.
- **Nome do banco:** `biblioteca` é herança do projeto original. Pode ser renomeado em `config/config.local.php` e nos scripts SQL.
- **Tabela `livros`:** permanece no banco como backup após a migração; o sistema não a utiliza mais.
- **Fontes:** incluídas localmente em `assets/fonts`, não exigem internet.
- **Envio de e-mail:** não implementado (não há serviço/configuração externa no projeto).
- **Upload de fotos:** não implementado.
- **Senhas existentes:** as contas antigas continuam válidas; não é possível recuperar senhas em texto puro.

---

## 🗺️ Próximos passos sugeridos

1. Vínculo entre pet e tutor/usuário, com perfis de acesso.
2. Upload de foto do pet.
3. Recuperação e alteração de senha.
4. Histórico de atendimentos e observações com data.
5. Relatórios e exportação.
6. Testes automatizados e deploy com HTTPS.

---

## 📄 Licença

Projeto acadêmico/demonstrativo. Use e adapte livremente para fins de estudo.
