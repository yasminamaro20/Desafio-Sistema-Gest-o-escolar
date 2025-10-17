<?php
session_start();

// === Definições do administrador ===
define('ADMIN_USER', 'admin');
define('ADMIN_PASS', '12345');

// === Base simulada de alunos ===
$alunos_db = [
    'aluno1' => ['senha' => 'aluno123', 'nome' => 'Maria Silva'],
    'aluno2' => ['senha' => 'aluno456', 'nome' => 'João Souza']
];

// Salva o array de alunos na sessão para uso posterior
$_SESSION['alunos_db'] = $alunos_db;

// Mensagem de erro (se houver)
$mensagem_erro = $_SESSION['mensagem_erro'] ?? '';
unset($_SESSION['mensagem_erro']); // Limpa após exibir
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Login</title>
    <link rel="stylesheet" href="style.css">
    <style>
        /* ======== Estilo Moderno ======== */
        body.login-body {
            font-family: "Segoe UI", Arial, sans-serif;
            margin: 0;
            background: linear-gradient(135deg, #004e92, #000428);
            color: #fff;
            height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .login-container {
            background: #fff;
            color: #333;
            width: 90%;
            max-width: 420px;
            padding: 30px 25px;
            border-radius: 12px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.3);
            text-align: center;
            animation: fadeIn 0.6s ease-in-out;
        }

        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(-15px); }
            to { opacity: 1; transform: translateY(0); }
        }

        .login-container h1 {
            margin-bottom: 15px;
            color: #003366;
        }

        .role-info {
            font-size: 0.9em;
            background: #f0f4ff;
            border-left: 4px solid #003366;
            padding: 8px;
            border-radius: 6px;
            text-align: left;
            margin-bottom: 20px;
        }

        .erro-mensagem {
            color: #d8000c;
            background: #ffbaba;
            padding: 8px;
            border-radius: 6px;
            margin-bottom: 15px;
            font-weight: bold;
        }

        .login-form {
            display: flex;
            flex-direction: column;
            gap: 15px;
        }

        .input-group {
            text-align: left;
        }

        label {
            font-weight: bold;
            font-size: 0.95em;
        }

        input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 15px;
            margin-top: 5px;
        }

        button {
            background: #003366;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: 0.3s;
        }

        button:hover {
            background: #0055aa;
        }
    </style>
</head>
<body class="login-body">
    <div class="login-container">
        <h1>🔐 Acesso ao Sistema Escolar</h1>

        <p class="role-info">
            <strong>Administrador:</strong> usuário <b><?= ADMIN_USER; ?></b> | senha <b><?= ADMIN_PASS; ?></b><br>
            <strong>Aluno Exemplo:</strong> usuário <b>aluno1</b> | senha <b>aluno123</b>
        </p>

        <?php if ($mensagem_erro): ?>
            <p class="erro-mensagem"><?= htmlspecialchars($mensagem_erro); ?></p>
        <?php endif; ?>

        <form action="processa_login.php" method="POST" class="login-form">
            <div class="input-group">
                <label for="usuario">Usuário:</label>
                <input type="text" id="usuario" name="usuario" required>
            </div>
            <div class="input-group">
                <label for="senha">Senha:</label>
                <input type="password" id="senha" name="senha" required>
            </div>
            <button type="submit" name="entrar">Entrar</button>
        </form>
    </div>
</body>
</html>
