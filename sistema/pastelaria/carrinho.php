<?php 
// Inicia a sessão para poder usar as variáveis de $_SESSION
session_start(); 
include("includes/db.php");
include("includes/header.php");  
// ====== REMOVER ITEM DO CARRINHO ======
// Verifica se foi enviado um formulário para remover 1 unidade de um item
if (isset($_POST['remover_id'])) {
    $idRemover = intval($_POST['remover_id']); // Converte o ID recebido para inteiro

    // Verifica se o item existe no carrinho
    if (isset($_SESSION['carrinho'][$idRemover])) {
        // Diminui a quantidade do item em 1
        $_SESSION['carrinho'][$idRemover]['quantidade'] -= 1;

        // Se a quantidade for menor ou igual a zero, remove o item do carrinho
        if ($_SESSION['carrinho'][$idRemover]['quantidade'] <= 0) {
            unset($_SESSION['carrinho'][$idRemover]);
        }
    }
}
// ====== ADICIONAR ITEM NO CARRINHO ======
// Verifica se foi enviado um formulário com o ID do pastel
if (isset($_POST['id'])) {
    $id = intval($_POST["id"]); // Converte o ID para inteiro

    // Consulta o pastel no banco de dados pelo ID
    $sql = "SELECT * FROM pasteis WHERE id = $id";
    $resultado = $conn->query($sql);

    // Se o pastel existir no banco
    if ($resultado->num_rows > 0) {
        $pastel = $resultado->fetch_assoc(); // Pega os dados do pastel como array associativo

        // Inicializa o carrinho se ele ainda não existir na sessão
        if (!isset($_SESSION["carrinho"])) {
            $_SESSION["carrinho"] = [];
        }

        $id = $pastel['id']; // Garante o uso do ID correto
        // Se o item já estiver no carrinho, aumenta a quantidade
        if (isset($_SESSION['carrinho'][$id])) {
            $_SESSION['carrinho'][$id]['quantidade'] += 1;
        } else {
            // Se não estiver, adiciona o item com quantidade 1
            $_SESSION['carrinho'][$id] = [
                'nome' => $pastel['nome'],
                'preco' => $pastel['preco'],
                'quantidade' => 1
            ];
        }
    }
}
?>
<!-- ===== CONTEÚDO DO CARRINHO ===== -->
<div class="carrinho">
    <?php
    // Verifica se o carrinho tem itens
    if (!empty($_SESSION['carrinho'])) {
        $soma = 0; // Total geral do carrinho

        // Percorre cada item do carrinho
        foreach ($_SESSION['carrinho'] as $id => $pastel) {
            // Calcula o total daquele pastel (preço x quantidade)
            $precoTotal = $pastel['preco'] * $pastel['quantidade'];
            $soma += $precoTotal; // Soma ao total geral

            // Mostra o card do item no carrinho
            echo '<div class="item-carrinho">';
            echo '  <div>';
            echo '    <div class="item-nome">' . $pastel['nome'] . '</div>';
            echo '    <div class="item-info">Quantidade: ' . $pastel['quantidade'] . '</div>';

            // Formulário para remover 1 unidade do item
            echo '<form method="POST" style="margin-top: 5px;">';
            echo '  <input type="hidden" name="remover_id" value="' . $id . '">';
            echo '  <button type="submit" class="btn-carrinho" onclick="return confirm(\'Deseja remover este item?\')">Excluir item</button>';
            echo '</form>';

            echo '  </div>';

            // Mostra o preço total daquele item
            echo '  <div class="item-info">R$ ' . number_format($precoTotal, 2, ',', '.') . '</div>';
            echo '</div>';
        }

        // Mostra o total geral do carrinho
        echo '<div class="total-carrinho">Valor total: R$ ' . number_format($soma, 2, ',', '.') . '</div>';

    } else {
        // Caso não haja itens no carrinho
        echo "O carrinho está sem itens.";
    }
    ?>
</div>

<!-- Botão de Finalizar Pedido -->
<div class="finalizar-container">
    <a class="btn-finalizar" href="finalizar_pedido.php">Finalizar Pedido</a>
</div>
