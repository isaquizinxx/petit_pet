<?php
// logout.php é responsável por encerrar a sessão do usuário, removendo todas as variáveis de sessão e destruindo o cookie de sessão. Ele garante que o usuário seja redirecionado para a página de login após o logout, prevenindo o acesso não autorizado às páginas protegidas.
session_start();
$_SESSION = array();

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 4200,
        $params["patch"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();

header("Cache-Contol: no-cache, no-store, must-revalidate");
header("Pragma: no-cache");
header("Expires: 0");

header("Location: login.php");
exit();