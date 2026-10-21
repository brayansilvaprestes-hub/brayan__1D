<?php
include 'conexao.php'; // Carrega sua conexão PDO e a função e()
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $senha = $_POST['senha'];

    try {
        // Busca o usuário pelo e-mail
        $stmt = $pdo->prepare("SELECT id, nome, senha FROM usuarios WHERE email = ?");
        $stmt->execute([$email]);
        $usuario = $stmt->fetch();

        if ($usuario) {
            // Verifica se a senha digitada é compatível com o hash criptografado
            if (password_verify($senha, $usuario['senha'])) {
                $_SESSION['usuario_id'] = $usuario['id'];
                $_SESSION['usuario_nome'] = $usuario['nome'];
                
                header("Location: index.php"); // Envia para a página inicial da sua biblioteca
                exit();
            } else {
                $erro_mensagem = "Senha incorreta!";
            }
        } else {
            $erro_mensagem = "E-mail não encontrado!";
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
    <title>Login</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <form method="POST" action="">
        <h2>Login</h2>
        
        <?php if(isset($_GET['sucesso'])): ?>
            <p style="color: green;">Cadastro realizado com sucesso! Faça seu login.</p>
        <?php endif; ?>

        <?php if(isset($erro_mensagem)): ?>
            <p style="color: red;"><?php echo e($erro_mensagem); ?></p>
        <?php endif; ?>

        <input type="email" name="email" placeholder="E-mail" required><br>
        <input type="password" name="senha" placeholder="Senha" required><br>
        <button type="submit">Entrar</button>
        <p>Não tem conta? <a href="cadastro.php">Crie uma aqui</a></p>
    </form>
</body>
</html>
