<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Bloqueia o acesso se não houver ID na sessão
if (!isset($_SESSION['usuario_id'])) {
    header("Location: login.php");
    exit();
}
?>
<!-- Avisa ao navegador que este é um documento HTML moderno -->
<!DOCTYPE html>

<!-- Abre o HTML e define o idioma da página como Português do Brasil -->
<html lang="pt-BR">
<head>
  <!-- Define o padrão de caracteres UTF-8 para aceitar acentos e emojis -->
  <meta charset="UTF-8">
  
  <!-- Faz o site se ajustar automaticamente ao tamanho da tela do celular -->
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <!-- Define o título que aparece na aba do navegador -->
  <title>Biblioteca da Escola</title>
  
  <!-- Conecta o arquivo CSS que dá o visual e as cores do site -->
  <link rel="stylesheet" href="estilo.css">
</head>
<body>

<!-- Cria o cabeçalho fixo no topo da página -->
<header style="position: relative;">
  <h1>📚 Biblioteca da Escola</h1>
  
  <!-- Bloco de identificação do usuário logado -->
  <div class="usuario-logado" style="position: absolute; top: 10px; right: 20px; font-size: 0.9em; background: rgba(255,255,255,0.1); padding: 5px 10px; border-radius: 4px;">
      <span>Olá, <strong><?php echo e($_SESSION['usuario_nome']); ?></strong>!</span>
      <a href="perfil.php" style="margin-left: 15px; color: #fff; text-decoration: underline;">Editar Perfil</a> 
      <a href="logout.php" style="margin-left: 15px; color: #ff6b6b; font-weight: bold; text-decoration: none;">Sair</a>
  </div>

  <!-- Menu de navegação com os links para mudar de página -->
  <nav>
    <a href="index.php">Livros</a>
    <a href="novo.php">Novo livro</a>
    <a href="emprestimos.php">Empréstimos</a>
  </nav>
</header>
  <!-- Bloco de identificação do usuário logado com foto -->
  <div class="usuario-logado" style="position: absolute; top: 15px; right: 40px; font-size: 0.85em; background: rgba(255, 255, 255, 0.1); padding: 6px 12px; border-radius: 4px; display: flex; align-items: center; gap: 10px;">
      <?php 
        $foto_sessao = isset($_SESSION['usuario_foto']) ? $_SESSION['usuario_foto'] : 'padrao.png';
        $caminho_topo = "fotos_perfil/" . $foto_sessao;
        if (!file_exists($caminho_topo)) { $caminho_topo = "fotos_perfil/padrao.png"; }
      ?>
      <img src="<?php echo $caminho_topo; ?>" alt="Avatar" style="width: 28px; height: 28px; border-radius: 50%; object-fit: cover; border: 1px solid #fff;">
      <span>Olá, <strong><?php echo e($_SESSION['usuario_nome']); ?></strong>!</span>
      <a href="perfil.php" style="color: #fff; text-decoration: underline;">Editar Perfil</a> 
      <a href="logout.php" style="color: #ff6b6b; font-weight: bold; text-decoration: none;">Sair</a>
  </div>
