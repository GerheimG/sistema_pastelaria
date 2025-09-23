<?php 
include("includes/db.php"); 
include("includes/header.php"); 
?>

<?php
// Monta a consulta SQL para buscar todos os registros da tabela "pasteis"
$sql = "SELECT * FROM pasteis";

// Executa a consulta no banco de dados e armazena o resultado na variável $resultado
$resultado = $conn->query($sql);
?>

<h1>Cardápio de Pastéis</h1>

<!-- Container principal para os cards de pastéis -->
<div class="cardapio">
    <?php
    // Verifica se a consulta retornou algum resultado
    if ($resultado->num_rows > 0) {
        // Loop que percorre cada linha (pastel) retornada pela consulta
        // $pastel recebe os dados de um pastel por vez (em formato de array associativo)
        while ($pastel = $resultado->fetch_assoc()) {
            ?>
            <!-- Estrutura visual de um único pastel (card) -->
            <div class="pastel">
                <!-- Imagem do pastel. O caminho da imagem vem do banco de dados -->
                <img src="image/<?php echo htmlspecialchars($pastel['imagem']); ?>" alt="<?php echo htmlspecialchars($pastel['nome']); ?>">

                <!-- Exibe o nome do pastel -->
                <h3><?php echo htmlspecialchars($pastel['nome']); ?></h3>

                <!-- Exibe o preço do pastel formatado (2 casas decimais, vírgula como separador decimal) -->
                <p class='preco'>Preço: R$ <?php echo number_format($pastel['preco'], 2, ',', '.'); ?></p>

                <!-- Formulário para adicionar o pastel ao carrinho -->
                <form method="post" action="carrinho.php">
                    <!-- Envia o ID do pastel via campo oculto (hidden) -->
                    <input type="hidden" name="id" value="<?php echo $pastel['id']; ?>">
                    
                    <!-- Botão para adicionar o pastel ao carrinho -->
                    <button type="submit" class="btn-add">Adicionar ao Carrinho</button>
                </form>
            </div>
            <?php
        }
    } else {
        // Caso nenhum pastel tenha sido encontrado no banco, exibe uma mensagem
        echo "<p>Nenhum pastel cadastrado ainda.</p>";
    }
    ?>
</div>

<?php 
// Inclui o rodapé da página
include("includes/footer.php"); 
?>

<?php
// Encerra a conexão com o banco de dados
$conn->close();
?>
