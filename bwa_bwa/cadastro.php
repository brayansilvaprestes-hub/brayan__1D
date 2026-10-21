<?php
include 'conexao.php'; // Carrega sua conexão PDO e a função e()

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $email = $_POST['email'];
    // Criptografa a senha antes de salvar no banco
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT); 

    try {
        // Verifica se o e-mail já existe utilizando Prepared Statements do PDO
        $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        
        if ($stmt->fetch()) {
            $erro_mensagem = "Este e-mail já está cadastrado!";
        } else {
            // Insere o novo usuário com segurança
            $stmt_insere = $pdo->prepare("INSERT INTO usuarios (nome, email, senha) VALUES (?, ?, ?)");
            if ($stmt_insere->execute([$nome, $email, $senha])) {
                header("Location: login.php?sucesso=1");
                exit();
            } else {
                $erro_mensagem = "Erro ao cadastrar usuário.";
            }
        }
    } catch (PDOException $e) {
        $erro_mensagem = "Erro no banco de dados: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Criar Conta</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <form method="POST" action="">
        <h2>Criar Conta</h2>
        
        <?php if(isset($erro_mensagem)): ?>
            <p style="color: red;"><?php echo e($erro_mensagem); ?></p>
        <?php endif; ?>

        <input type="text" name="nome" placeholder="Nome Completo" required><br>
        <input type="email" name="email" placeholder="E-mail" required><br>
        <input type="password" name="senha" placeholder="Senha" required><br>
        <button type="submit">Cadastrar</button>
        <p>Já tem uma conta? <a href="login.php">Faça login</a></p>
    </form>
</body>
</html>
