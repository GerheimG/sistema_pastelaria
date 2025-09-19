<?php 
// Inclui o arquivo de conexão com o banco de dados
include("includes/db.php"); 

// Inclui o cabeçalho da página
include("includes/header.php"); 
?>

<?php
// Consulta SQL para buscar todos os pastéis cadastrados no banco
$sql = "SELECT * FROM pasteis";

// Executa a consulta e armazena o resultado na variável $resultado
$resultado = $conn->query($sql);
?>

<h1>Cardápio de Pastéis</h1>

<!-- Container principal que vai abrigar todos os cards de pastéis -->
<div class="cardapio">
    <?php
    // Verifica se existem registros retornados do banco
    if ($resultado->num_rows > 0) {
        // Loop para percorrer todos os pastéis encontrados
        while ($pastel = $resultado->fetch_assoc()) { // entender melhor
            ?>
            <!-- Card individual de cada pastel -->
            <div class="pastel">
                <!-- Imagem do pastel (o nome do arquivo vem do banco de dados) -->
                <img src="image/<?php echo htmlspecialchars($pastel['imagem']); ?>" alt="<?php echo htmlspecialchars($pastel['nome']); ?>">

                <!-- Nome do pastel -->
                <h3><?php echo htmlspecialchars($pastel['nome']); ?></h3>

                <!-- Preço do pastel formatado com vírgula -->
                <p class='preco'>Preço: R$ <?php echo number_format($pastel['preco'], 2, ',', '.'); ?></p>

                <form method="post" action="carrinho.php">
                    <input type="hidden" name="id" value="<?php echo $pastel['id']; ?>">
                    <button type="submit" class="btn-carrinho">Adicionar ao Carrinho</button>
                </form>
            </div>
            <?php
        }
    } else {
        // Caso não haja nenhum pastel no banco, exibe uma mensagem
        echo "<p>Nenhum pastel cadastrado ainda.</p>";
    }
    ?>
</div>

<?php 
// Inclui o rodapé da página
include("includes/footer.php"); 
?>

<?php
// Fecha a conexão com o banco de dados
$conn->close();
?>
