<!-- Formulário de cadastro de usuário -->
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Criar conta | Petit Pet</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div class="container">
        <h1>🐾 Faça parte da Petit Pet</h1>
        <p class="subtitulo">Crie sua conta para acompanhar o cuidado com seus pets.</p>
        <?php
            if(isset($_GET['erro']) && $_GET['erro'] === 'email') {
                echo '<div class="mensagem-erro">Este e-mail já está cadastrado. Faça login para continuar.</div>';
            }
        ?>

        <form action="salvar_usuario.php" method="post">
            <div class="form-group">
                <label for="nome">Seu nome</label>
                <input type="text" id="nome" name="nome" placeholder="Como podemos chamar você?" required>
            </div>
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="voce@email.com" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
            </div>
            <button type="submit" class="btn btn-block">Cadastrar</button>
        </form>
        <div class="nav-links">
            <p>Já tem conta? <a href="login.php">Fazer Login</a></p>
        </div>
        <div class="dica-navegacao">
            <strong>Petit Pet:</strong> um lugar especial para cuidar dos seus melhores amigos.
        </div>
    </div>
</body>
</html>