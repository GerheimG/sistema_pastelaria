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

<!-- ===== CSS embutido para o carrinho ===== -->
<style>
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

    .item-carrinho {
        padding: 10px 0;
        border-bottom: 1px solid #eee;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .item-carrinho:last-child {
        border-bottom: none;
    }

    .item-nome {
        font-weight: bold;
        color: #333;
    }

    .item-info {
        color: #555;
        font-size: 14px;
    }

    .total-carrinho {
        margin-top: 20px;
        text-align: right;
        font-size: 16px;
        font-weight: bold;
        color: #222;
    }

    .btn-carrinho {
        background-color: #e74c3c;
        color: white;
        border: none;
        padding: 5px 10px;
        border-radius: 4px;
        cursor: pointer;
        font-size: 13px;
    }

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
