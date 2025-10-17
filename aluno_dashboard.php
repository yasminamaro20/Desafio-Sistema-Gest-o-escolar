<?php
session_start();

// Verifica se o usuário está logado como aluno
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'aluno') {
    header("Location: index.php");
    exit;
}

$nome_aluno_logado = $_SESSION['nome'] ?? 'Aluno';
$usuario_logado = $_SESSION['usuario'];
$turma_dados = $_SESSION['turma'] ?? [];
$dados_aluno = null;

// Tenta encontrar os dados deste aluno na simulação de turma
foreach ($turma_dados as $aluno) {
    if ($aluno['nome_aluno'] === $nome_aluno_logado) {
        $dados_aluno = $aluno;
        break;
    }
}

// Fallback/Dados de exemplo se os dados não foram processados
if (!$dados_aluno) {
    $dados_aluno = [
        'nome_aluno' => $nome_aluno_logado,
        'notas_aluno' => [0, 0, 0],
        'frequencia' => 0.0,
        'media' => 0.0,
        'presenca_status' => 'Dados indisponíveis',
        'status_final' => 'Pendente',
        'status_class' => 'aviso'
    ];
} else {
    $dados_aluno['status_class'] = strtolower(str_replace(' ', '-', $dados_aluno['status_final']));
}

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema Escolar - Aluno</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="sistema-body">
    <header>
        <div class="header-content">
            <h2>Painel do Aluno</h2>
            <nav>
                <a href="aluno_dashboard.php">Meus Dados</a>
                <a href="logout.php">Sair</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="student-info">
            <h3>Bem-vindo(a), <?php echo htmlspecialchars($nome_aluno_logado); ?>!</h3>
            <p><strong>Usuário:</strong> <?php echo htmlspecialchars($usuario_logado); ?></p>
        </section>

        <hr>

        <section class="report-section">
            <h3>Desempenho Acadêmico</h3>
            <div class="card-grid">
                
                <div class="card">
                    <h4>Média Final</h4>
                    <p class="data-value"><?php echo number_format($dados_aluno['media'], 2); ?></p>
                </div>
                
                <div class="card">
                    <h4>Frequência</h4>
                    <p class="data-value"><?php echo number_format($dados_aluno['frequencia'], 1) . '%'; ?></p>
                </div>

                <div class="card status-card <?php echo $dados_aluno['status_class'] ?? ''; ?>">
                    <h4>Status Final</h4>
                    <p class="data-value status-text">
                        <strong><?php echo $dados_aluno['status_final']; ?></strong>
                    </p>
                </div>

            </div>

            <h4 style="margin-top: 30px;">Notas Detalhadas:</h4>
            <ul>
                <li>Nota 1: **<?php echo number_format($dados_aluno['notas_aluno'][0] ?? 0, 2); ?>**</li>
                <li>Nota 2: **<?php echo number_format($dados_aluno['notas_aluno'][1] ?? 0, 2); ?>**</li>
                <li>Nota 3: **<?php echo number_format($dados_aluno['notas_aluno'][2] ?? 0, 2); ?>**</li>
            </ul>

        </section>
    </main>
</body>
</html>