<?php
session_start();

/* ============================================================
   VERIFICAÇÃO DE MÉTODO DE ENVIO
   ============================================================ */
if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !isset($_POST['entrar'])) {
    $_SESSION['mensagem_erro'] = "Acesso inválido! Por favor, use o formulário de login.";
    header("Location: index.php");
    exit;
}

/* ============================================================
   VALIDAÇÃO DOS CAMPOS DE LOGIN
   ============================================================ */
$usuario = trim($_POST['usuario'] ?? '');
$senha   = trim($_POST['senha'] ?? '');

if ($usuario === '' || $senha === '') {
    $_SESSION['mensagem_erro'] = "Por favor, preencha todos os campos.";
    header("Location: index.php");
    exit;
}

/* ============================================================
   CREDENCIAIS DO ADMINISTRADOR
   ============================================================ */
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', '12345');

$login_sucesso = false;
$tipo_usuario  = '';

/* ============================================================
   VERIFICA LOGIN COMO ADMINISTRADOR
   ============================================================ */
if ($usuario === ADMIN_USER && $senha === ADMIN_PASS) {
    $_SESSION['logado']  = true;
    $_SESSION['usuario'] = $usuario;
    $_SESSION['tipo']    = 'admin';
    $login_sucesso       = true;
    $tipo_usuario        = 'admin';
}

/* ============================================================
   VERIFICA LOGIN COMO ALUNO
   ============================================================ */
else {
    $alunos_db = $_SESSION['alunos_db'] ?? [];

    if (isset($alunos_db[$usuario]) && $alunos_db[$usuario]['senha'] === $senha) {
        $_SESSION['logado']  = true;
        $_SESSION['usuario'] = $usuario;
        $_SESSION['nome']    = $alunos_db[$usuario]['nome'];
        $_SESSION['tipo']    = 'aluno';
        $login_sucesso       = true;
        $tipo_usuario        = 'aluno';
    }
}

/* ============================================================
   REDIRECIONAMENTO CONFORME O TIPO DE USUÁRIO
   ============================================================ */
if ($login_sucesso) {
    if ($tipo_usuario === 'admin') {
        header("Location: sistema.php");
    } else {
        header("Location: aluno_dashboard.php");
    }
    exit;
}

/* ============================================================
   SE O LOGIN FALHAR
   ============================================================ */
$_SESSION['mensagem_erro'] = "Usuário ou senha incorretos. Tente novamente.";
header("Location: index.php");
exit;
?>
