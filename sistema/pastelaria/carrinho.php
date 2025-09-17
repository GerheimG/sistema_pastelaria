<?php 
session_start(); 
include("includes/db.php");
include("includes/header.php");  

// Verifica se foi enviado um pedido para remover 1 unidade de um item do carrinho
if (isset($_POST['remover_id'])) {
    $idRemover = intval($_POST['remover_id']); // ID do item a ser removido (convertido para inteiro)
    if (isset($_SESSION['carrinho'][$idRemover])) {
        // Diminui a quantidade do item em 1
        $_SESSION['carrinho'][$idRemover]['quantidade'] -= 1;

        // Se a quantidade chegar a 0 ou menos, remove o item do carrinho completamente
        if ($_SESSION['carrinho'][$idRemover]['quantidade'] <= 0) {
            unset($_SESSION['carrinho'][$idRemover]);
        }
    }
}

// Verifica se foi enviado um pedido para adicionar um item no carrinho
if (isset($_POST['id'])) {
    $id = intval($_POST["id"]); // ID do item a adicionar

    // Busca o item no banco de dados
    $sql = "SELECT * FROM pasteis WHERE id = $id";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {
        $pastel = $resultado->fetch_assoc();

        // Inicializa o carrinho na sessão se ainda não existir
        if (!isset($_SESSION["carrinho"])) {
            $_SESSION["carrinho"] = [];
        }

        $id = $pastel['id'];
        // Se o item já estiver no carrinho, aumenta a quantidade em 1
        if (isset($_SESSION['carrinho'][$id])) {
            $_SESSION['carrinho'][$id]['quantidade'] += 1;
        } else {
            // Caso contrário, adiciona o item com quantidade 1
            $_SESSION['carrinho'][$id] = [
                'nome' => $pastel['nome'],
                'preco' => $pastel['preco'],
                'quantidade' => 1
            ];
        }
    }
}
?>

<style>
    /* Estilização geral do carrinho */
    .carrinho {
        max-width: 600px;
        margin: 30px auto;
        padding: 20px;
        border: 1px solid #bbb;
        border-radius: 8px;
        font-family: Arial, sans-serif;
        background-color: #fdfdfd;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.05);
    }

    /* Cada item do carrinho */
    .item-carrinho {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    /* Remove a borda do último item */
    .item-carrinho:last-child {
        border-bottom: none;
    }

    /* Nome do item em destaque */
    .item-nome {
        font-weight: bold;
        color: #333;
    }

    /* Informações adicionais do item */
    .item-info {
        color: #555;
        font-size: 14px;
    }

    /* Área do total do carrinho */
    .total-carrinho {
        margin-top: 20px;
        text-align: right;
        font-size: 16px;
        font-weight: bold;
        color: #222;
    }

    /* Botão para excluir item */
    .btn-carrinho {
        background-color: #e74c3c;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
    }

    /* Efeito hover no botão */
    .btn-carrinho:hover {
        background-color: #c0392b;
    }

    .finalizar-container {
        text-align: center;
        margin-top: 25px;
    }

    .btn-finalizar {
        display: inline-block;
        background-color: #27ae60;
        color: white;
        text-decoration: none;
        padding: 10px 20px;
        border-radius: 4px;
        font-size: 15px;
        font-weight: bold;
        transition: background-color 0.2s ease;
    }

    .btn-finalizar:hover {
        background-color: #1e8449;
    }


</style>

<div class="carrinho">

<?php
// Verifica se o carrinho não está vazio
if (!empty($_SESSION['carrinho'])) {
    $soma = 0; // Variável para somar o total do carrinho

    // Percorre cada item do carrinho
    foreach ($_SESSION['carrinho'] as $id => $pastel) {
        // Calcula o preço total daquele item (preço unitário * quantidade)
        $precoTotal = $pastel['preco'] * $pastel['quantidade'];
        $soma += $precoTotal; // Acumula no total geral

        echo '<div class="item-carrinho">';
        echo '  <div>';
        echo '    <div class="item-nome">' . $pastel['nome'] . '</div>';
        echo '    <div class="item-info">Quantidade: ' . $pastel['quantidade'] . '</div>';

        // Formulário para excluir 1 unidade do item do carrinho
        echo '<form method="POST" style="margin-top: 5px;">';
        echo '  <input type="hidden" name="remover_id" value="' . $id . '">';
        echo '  <button type="submit" class="btn-carrinho" onclick="return confirm(\'Deseja remover este item?\')">Excluir item</button>';
        echo '</form>';

        echo '  </div>';

        // Exibe o preço total do item formatado com duas casas decimais
        echo '  <div class="item-info">R$ ' . number_format($precoTotal, 2, ',', '.') . '</div>';
        echo '</div>';
    }

    // Exibe o valor total do carrinho formatado
    echo '<div class="total-carrinho">Valor total: R$ ' . number_format($soma, 2, ',', '.') . '</div>';

} else {
    // Caso o carrinho esteja vazio
    echo "O carrinho está sem itens.";
}
?>
</div>
<div class="finalizar-container">
    <a class="btn-finalizar" href="finalizar_pedido.php">Finalizar Pedido</a>
</div>