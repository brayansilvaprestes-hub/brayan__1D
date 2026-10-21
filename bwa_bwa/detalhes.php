<?php
include 'conexao.php';
include 'topo.php';

$id_livro = filter_input(INPUT_GET, 'id', FILTER_SANITIZE_NUMBER_INT);
$id_usuario = $_SESSION['usuario_id'];
$mensagem = '';

if (!$id_livro) {
    header("Location: index.php");
    exit();
}

// LÓGICA 1: Processar Empréstimo (Ação do Botão)
if (isset($_POST['acao_emprestar'])) {
    try {
        $data_atual = date('Y-m-d');
        // Calcula exatamente 30 dias à frente
        $data_devolucao = date('Y-m-d', strtotime('+30 days'));

        $stmt = $pdo->prepare("INSERT INTO emprestimos (id_livro, id_usuario, data_emprestimo, data_devolucao, status) VALUES (?, ?, ?, ?, 'Ativo')");
        $stmt->execute([$id_livro, $id_usuario, $data_atual, $data_devolucao]);
        $mensagem = "<p style='color:green;'>Empréstimo realizado! Devolução até: " . date('d/m/Y', strtotime($data_devolucao)) . "</p>";
    } catch (PDOException $e) {
        $mensagem = "<p style='color:red;'>Erro ao emprestar: " . $e->getMessage() . "</p>";
    }
}

// LÓGICA 2: Processar Devolução (Ação do Botão)
if (isset($_POST['acao_devolver'])) {
    try {
        $stmt = $pdo->prepare("UPDATE emprestimos SET status = 'Devolvido' WHERE id_livro = ? AND id_usuario = ? AND status = 'Ativo'");
        $stmt->execute([$id_livro, $id_usuario]);
        $mensagem = "<p style='color:green;'>Livro devolvido com sucesso!</p>";
    } catch (PDOException $e) {
        $mensagem = "<p style='color:red;'>Erro ao devolver: " . $e->getMessage() . "</p>";
    }
}

// Busca informações do livro selecionado
$stmt_livro = $pdo->prepare("SELECT * FROM livros WHERE id = ?");
$stmt_livro->execute([$id_livro]);
$livro = $stmt_livro->fetch();

if (!$livro) {
    die("Livro não encontrado.");
}

// Verifica o status do empréstimo atual deste livro
$stmt_status = $pdo->prepare("SELECT * FROM emprestimos WHERE id_livro = ? AND status = 'Ativo'");
$stmt_status->execute([$id_livro]);
$emprestimo_atual = $stmt_status->fetch();

$caminho_capa = "capas/" . $livro['id'] . ".jpg";
if (!file_exists($caminho_capa)) {
    $caminho_capa = "capas/padrao.jpg";
}
?>

<div class="detalhes-container">
    <?php echo $mensagem; ?>
    
    <div class="detalhes-bloco">
        <div class="detalhes-capa">
            <img src="<?php echo $caminho_capa; ?>" alt="Capa">
        </div>
        
        <div class="detalhes-info">
            <h2><?php echo e($livro['titulo']); ?></h2>
            <p><strong>Autor:</strong> <?php echo e($livro['autor']); ?></p>
            
            <div class="acoes-box">
                <?php if (!$emprestimo_atual): ?>
                    <!-- Disponível para empréstimo -->
                    <form method="POST" action="">
                        <button type="submit" name="acao_emprestar" class="btn-emprestar">Pegar Emprestado (30 dias)</button>
                    </form>
                <?php else: ?>
                    <!-- Livro já está emprestado -->
                    <p class="alerta-indisponivel">
                        Status: <strong>Indisponível</strong> <br>
                        Prazo de devolução: <?php echo date('d/m/Y', strtotime($emprestimo_atual['data_devolucao'])); ?>
                    </p>
                    
                    <?php if ($emprestimo_atual['id_usuario'] == $id_usuario): ?>
                        <!-- O botão devolver só aparece para quem pegou o livro -->
                        <form method="POST" action="">
                            <button type="submit" name="acao_devolver" class="btn-devolver">Devolver Livro</button>
                        </form>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            
            <br>
            <a href="index.php" class="btn-voltar">← Voltar ao Acervo</a>
        </div>
    </div>
</div>

<?php include 'rodape.php'; ?>
