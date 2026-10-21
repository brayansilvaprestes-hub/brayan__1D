<?php
include 'conexao.php'; // Carrega o PDO e a função e()
include 'topo.php';    // Garante que o usuário está logado e puxa o topo do site

$id_usuario = $_SESSION['usuario_id'];
$sucesso_mensagem = '';
$erro_mensagem = '';

// 1. Busca os dados atuais do usuário (incluindo a foto)
try {
    $stmt = $pdo->prepare("SELECT nome, email, foto FROM usuarios WHERE id = ?");
    $stmt->execute([$id_usuario]);
    $usuario = $stmt->fetch();
} catch (PDOException $e) {
    $erro_mensagem = "Erro ao buscar dados: " . $e->getMessage();
}

// 2. Processa a atualização dos dados quando o formulário é enviado
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $novo_nome = trim($_POST['nome']);
    $nova_senha = $_POST['senha'];
    $nome_foto_final = $usuario['foto']; // Mantém a atual caso não mude

    // Validação do envio da foto de perfil
    if (isset($_FILES['foto_upload']) && $_FILES['foto_upload']['error'] == UPLOAD_ERR_OK) {
        $arquivo_tmp = $_FILES['foto_upload']['tmp_name'];
        $nome_original = $_FILES['foto_upload']['name'];
        $extensao = strtolower(pathinfo($nome_original, PATHINFO_EXTENSION));

        // Permite apenas formatos de imagem comuns
        $extesoes_permitidas = ['jpg', 'jpeg', 'png'];

        if (in_array($extensao, $extesoes_permitidas)) {
            // Cria um nome único usando o ID do usuário para a imagem
            $nome_foto_final = "usuario_" . $id_usuario . "." . $extensao;
            $destino = "fotos_perfil/" . $nome_foto_final;

            // Move o arquivo temporário para a nossa pasta definitiva
            if (!move_uploaded_file($arquivo_tmp, $destino)) {
                $erro_mensagem = "Falha ao salvar o arquivo de imagem no servidor.";
            }
        } else {
            $erro_mensagem = "Formato de arquivo inválido! Envie apenas imagens JPG, JPEG ou PNG.";
        }
    }

    if (empty($novo_nome)) {
        $erro_mensagem = "O nome não pode ficar vazio.";
    } elseif (empty($erro_mensagem)) {
        try {
            // Monta o SQL dinâmico dependendo se alterou a senha ou não
            if (!empty($nova_senha)) {
                $senha_criptografada = password_hash($nova_senha, PASSWORD_DEFAULT);
                $stmt_update = $pdo->prepare("UPDATE usuarios SET nome = ?, senha = ?, foto = ? WHERE id = ?");
                $stmt_update->execute([$novo_nome, $senha_criptografada, $nome_foto_final, $id_usuario]);
            } else {
                $stmt_update = $pdo->prepare("UPDATE usuarios SET nome = ?, foto = ? WHERE id = ?");
                $stmt_update->execute([$novo_nome, $nome_foto_final, $id_usuario]);
            }

            // Atualiza os dados da sessão imediatamente
            $_SESSION['usuario_nome'] = $novo_nome;
            $_SESSION['usuario_foto'] = $nome_foto_final;
            
            $sucesso_mensagem = "Perfil atualizado com sucesso!";
            
            // Atualiza a variável local para recarregar as informações na tela
            $usuario['nome'] = $novo_nome;
            $usuario['foto'] = $nome_foto_final;

        } catch (PDOException $e) {
            $erro_mensagem = "Erro ao atualizar dados: " . $e->getMessage();
        }
    }
}

// Garante o caminho correto para exibir a miniatura na página
$caminho_foto = "fotos_perfil/" . ($usuario['foto'] ? $usuario['foto'] : 'padrao.png');
if (!file_exists($caminho_foto)) {
    $caminho_foto = "fotos_perfil/padrao.png";
}
?>

<div class="container-perfil">
    <h2>Editar Perfil</h2>

    <?php if(!empty($sucesso_mensagem)): ?>
        <p style="color: green; text-align: center;"><?php echo e($sucesso_mensagem); ?></p>
    <?php endif; ?>

    <?php if(!empty($erro_mensagem)): ?>
        <p style="color: red; text-align: center;"><?php echo e($erro_mensagem); ?></p>
    <?php endif; ?>

    <!-- Atributo enctype="multipart/form-data" é obrigatório para enviar arquivos -->
    <form method="POST" action="" enctype="multipart/form-data">
        
        <!-- Preview da Foto Atual -->
        <div style="text-align: center; margin-bottom: 20px;">
            <img src="<?php echo $caminho_foto; ?>" alt="Foto de Perfil" style="width: 100px; height: 100px; border-radius: 50%; object-fit: cover; border: 2px solid #1a237e; box-shadow: 0 2px 5px rgba(0,0,0,0.15);">
        </div>

        <label for="foto_upload">Alterar Foto de Perfil:</label><br>
        <input type="file" id="foto_upload" name="foto_upload" accept="image/*" style="margin: 8px 0 16px 0;"><br><br>

        <label for="email">E-mail (Não pode ser alterado):</label><br>
        <input type="email" id="email" value="<?php echo e($usuario['email']); ?>" disabled style="background-color: #eee;"><br><br>

        <label for="nome">Nome de Usuário:</label><br>
        <input type="text" id="nome" name="nome" value="<?php echo e($usuario['nome']); ?>" required><br><br>

        <label for="senha">Nova Senha (Deixe em branco para não alterar):</label><br>
        <input type="password" id="senha" name="senha" placeholder="Digite a nova senha"><br><br>

        <button type="submit">Salvar Alterações</button>
    </form>

    <br>
    <a href="index.php" class="btn-voltar">← Voltar para o Painel</a>
</div>

<?php include 'rodape.php'; ?>
