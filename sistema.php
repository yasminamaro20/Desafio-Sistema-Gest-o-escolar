<?php
session_start();

// ✅ Verifica se o usuário está logado como admin
if (!isset($_SESSION['logado']) || $_SESSION['tipo'] !== 'admin') {
    header("Location: index.php");
    exit;
}

// 🎯 Funções utilitárias
function calcularMedia(array $notas): float {
    return count($notas) > 0 ? array_sum($notas) / count($notas) : 0.0;
}

function situacaoPresenca(float $frequencia): string {
    return $frequencia >= 75.0 ? 'Presente' : 'Reprovado por Falta';
}

function statusAprovacao(float $media, float $frequencia): string {
    if ($frequencia < 75.0) return 'Reprovado por Falta';
    if ($media >= 7.0) return 'Aprovado';
    if ($media >= 5.0) return 'Recuperação';
    return 'Reprovado por Nota';
}

// 🏫 Dados de sessão
$nome_escola = $_SESSION['nome_escola'] ?? '';
$turma = $_SESSION['turma'] ?? [];
$relatorio_gerado = false;

// 📋 Processamento do formulário
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['processar_dados'])) {
    $nome_escola = trim($_POST['nome_escola'] ?? 'Escola Padrão');
    $_SESSION['nome_escola'] = $nome_escola;

    $nomes = $_POST['nome_aluno'] ?? [];
    $notas1 = $_POST['nota1'] ?? [];
    $notas2 = $_POST['nota2'] ?? [];
    $notas3 = $_POST['nota3'] ?? [];
    $frequencias = $_POST['frequencia'] ?? [];

    $nova_turma = [];
    foreach ($nomes as $i => $nome) {
        $nome = trim($nome);
        if ($nome === '') continue;

        $notas = [
            (float)($notas1[$i] ?? 0),
            (float)($notas2[$i] ?? 0),
            (float)($notas3[$i] ?? 0)
        ];
        $frequencia = (float)($frequencias[$i] ?? 0);

        $media = calcularMedia($notas);
        $nova_turma[] = [
            'nome_aluno' => $nome,
            'notas_aluno' => $notas,
            'frequencia' => $frequencia,
            'media' => $media,
            'presenca_status' => situacaoPresenca($frequencia),
            'status_final' => statusAprovacao($media, $frequencia)
        ];
    }

    $turma = $nova_turma;
    $_SESSION['turma'] = $turma;
    $relatorio_gerado = true;
}

// 🧍 Dados de exemplo
if (empty($turma) && !$relatorio_gerado) {
    $turma = [
        ['nome_aluno' => 'Aluno A', 'notas_aluno' => [8.5, 7.5, 9.0], 'frequencia' => 90.0],
        ['nome_aluno' => 'Aluno B', 'notas_aluno' => [5.0, 6.0, 5.5], 'frequencia' => 80.0],
        ['nome_aluno' => 'Aluno C', 'notas_aluno' => [7.0, 7.0, 6.5], 'frequencia' => 72.0],
    ];
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Sistema Escolar</title>
    <link rel="stylesheet" href="style.css">
</head>
<body class="sistema-body">
    <header>
        <div class="header-content">
            <h2>🏫 Painel do Administrador</h2>
            <nav>
                <a href="sistema.php" class="active">Início</a>
                <a href="logout.php">Sair</a>
            </nav>
        </div>
    </header>

    <main class="container">
        <section class="data-form">
            <h3>📋 Cadastro e Análise da Turma</h3>

            <form action="sistema.php" method="POST" class="form-turma">
                <div class="input-group">
                    <label for="nome_escola">Nome da Escola:</label>
                    <input type="text" id="nome_escola" name="nome_escola" 
                           value="<?php echo htmlspecialchars($nome_escola); ?>" required>
                </div>

                <div id="alunos-container">
                    <?php foreach ($turma as $i => $aluno): ?>
                        <fieldset class="aluno-fieldset">
                            <legend>Aluno <?php echo $i + 1; ?></legend>
                            <div class="input-row">
                                <label>Nome:</label>
                                <input type="text" name="nome_aluno[]" 
                                       value="<?php echo htmlspecialchars($aluno['nome_aluno']); ?>" required>
                            </div>
                            <?php for ($n = 1; $n <= 3; $n++): ?>
                                <div class="input-row">
                                    <label>Nota <?php echo $n; ?>:</label>
                                    <input type="number" step="0.1" min="0" max="10" 
                                           name="nota<?php echo $n; ?>[]" 
                                           value="<?php echo htmlspecialchars($aluno['notas_aluno'][$n-1] ?? 0); ?>" required>
                                </div>
                            <?php endfor; ?>
                            <div class="input-row">
                                <label>Frequência (%):</label>
                                <input type="number" step="0.1" min="0" max="100" 
                                       name="frequencia[]" value="<?php echo htmlspecialchars($aluno['frequencia']); ?>" required>
                            </div>
                        </fieldset>
                    <?php endforeach; ?>
                </div>

                <div class="button-group">
                    <button type="button" onclick="adicionarAluno()">➕ Adicionar Aluno</button>
                    <button type="submit" name="processar_dados" class="process-btn">
                        📊 Gerar Relatório
                    </button>
                </div>
            </form>
        </section>

        <?php if ($relatorio_gerado && !empty($turma)): ?>
            <hr>
            <section class="report-section">
                <h3>📈 Relatório da Turma - <?php echo htmlspecialchars($nome_escola); ?></h3>
                <table>
                    <thead>
                        <tr>
                            <th>Aluno</th>
                            <th>Média</th>
                            <th>Frequência</th>
                            <th>Presença</th>
                            <th>Status Final</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($turma as $aluno): 
                            $status_class = strtolower(str_replace(' ', '-', $aluno['status_final']));
                        ?>
                        <tr class="<?php echo $status_class; ?>">
                            <td><?php echo htmlspecialchars($aluno['nome_aluno']); ?></td>
                            <td><?php echo number_format($aluno['media'], 2); ?></td>
                            <td><?php echo number_format($aluno['frequencia'], 1) . '%'; ?></td>
                            <td><?php echo htmlspecialchars($aluno['presenca_status']); ?></td>
                            <td><strong><?php echo htmlspecialchars($aluno['status_final']); ?></strong></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </section>
        <?php elseif ($relatorio_gerado): ?>
            <p class="aviso">⚠️ Nenhum aluno válido foi inserido para gerar o relatório.</p>
        <?php endif; ?>
    </main>

    <script>
        // ➕ Função para adicionar campos de alunos dinamicamente
        function adicionarAluno() {
            const container = document.getElementById('alunos-container');
            const index = container.children.length + 1;

            const novoAluno = `
                <fieldset class="aluno-fieldset">
                    <legend>Aluno ${index}</legend>
                    <div class="input-row"><label>Nome:</label><input type="text" name="nome_aluno[]" required></div>
                    <div class="input-row"><label>Nota 1:</label><input type="number" step="0.1" min="0" max="10" name="nota1[]" required></div>
                    <div class="input-row"><label>Nota 2:</label><input type="number" step="0.1" min="0" max="10" name="nota2[]" required></div>
                    <div class="input-row"><label>Nota 3:</label><input type="number" step="0.1" min="0" max="10" name="nota3[]" required></div>
                    <div class="input-row"><label>Frequência (%):</label><input type="number" step="0.1" min="0" max="100" name="frequencia[]" required></div>
                </fieldset>`;
            container.insertAdjacentHTML('beforeend', novoAluno);
        }
    </script>
</body>
</html>
