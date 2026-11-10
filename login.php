<!-- Formulário de login -->
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Entrar | Petit Pet</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>
    <div class="container">
        <h1>🐾 Petit Pet</h1>
        <p class="subtitulo">Entre para cuidar dos pets e acompanhar os atendimentos.</p>

        <?php
        if (isset($_GET['erro']) && $_GET['erro'] === 'login') {
            echo '<div class="mensagem-erro">E-mail ou senha inválidos. Confira seus dados e tente novamente.</div>';
        }
        ?>
        <form action="autenticar.php" method="POST">
            <div class="form-group">
                <label for="email">E-mail</label>
                <input type="email" id="email" name="email" placeholder="voce@email.com" required>
            </div>
            <div class="form-group">
                <label for="senha">Senha</label>
                <input type="password" id="senha" name="senha" placeholder="Digite sua senha" required>
            </div>
            <button type="submit" class="btn btn-block">Entrar</button>
        </form>

        <div class="nav-links">
            <p>Não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
        </div>
        <div class="dica-navegacao">
            <strong>Petit Pet:</strong> carinho e cuidado em cada visita.
        </div>

    </div>
</body>

</html>