<?php
include 'conexao.php';
include 'topo.php';

try {
    $stmt = $pdo->query("SELECT id, titulo, autor FROM livros ORDER BY titulo ASC");
    $livros = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Erro ao buscar livros: " . $e->getMessage());
}
?>

<div class="catalogo-container">
    <div class="secao-titulo">
        <h2>Recomendados para você</h2>
        <span class="linha-decorativa"></span>
    </div>
    
    <div class="grade-leia-parana">
        <?php foreach ($livros as $livro): 
            $caminho_capa = "capas/" . $livro['id'] . ".jpg";
            if (!file_exists($caminho_capa)) {
                $caminho_capa = "capas/padrao.jpg";
            }
        ?>
            <a href="detalhes.php?id=<?php echo $livro['id']; ?>" class="card-leia-parana">
                <div class="moldura-ebook">
                    <img src="<?php echo $caminho_capa; ?>" alt="Capa">
                </div>
                <div class="metadados-livro">
                    <h3><?php echo e($livro['titulo']); ?></h3>
                    <p><?php echo e($livro['autor']); ?></p>
                </div>
            </a> 
        <?php endforeach; ?>
    </div>
</div>

<?php include 'rodape.php'; ?>
