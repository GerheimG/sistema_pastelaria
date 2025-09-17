<?php
session_start();
include("includes/db.php");
include("includes/header.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? mysqli_real_escape_string($conn, $_POST['nome']) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';

    if (!empty($nome)) {
        if (isset($_SESSION['carrinho']) && count($_SESSION['carrinho']) > 0) {
            $sql_pedido = "INSERT INTO pedidos (cliente_nome, status, data_pedido) VALUES ('$nome', 'Em preparo', NOW())";

            if ($conn->query($sql_pedido) === TRUE) {
                $id_pedido = $conn->insert_id;

                foreach ($_SESSION['carrinho'] as $id_pastel => $item) {
                    $quantidade = intval($item['quantidade']);
                    $sql_item = "INSERT INTO itens_pedido (id_pedido, id_pastel, quantidade) VALUES ($id_pedido, $id_pastel, $quantidade)";
                    $conn->query($sql_item);
                }

                unset($_SESSION['carrinho']);

                header("Location: index.php");
                exit();
            } else {
                echo "Erro ao finalizar pedido: " . $conn->error;
            }
        } else {
            echo "<p>Seu carrinho está vazio. Adicione pelo menos um item para finalizar o pedido.</p>";
        }
    } else {
        echo "Por favor, preencha seu nome.";
    }
}

if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1) {
    echo "<p>Pedido finalizado com sucesso!</p>";
}
?>
<style>
    /* ===== Estilo do Formulário de Finalizar Pedido ===== */
    form {
        max-width: 350px;
        margin: 80px auto;
        padding: 30px;
        background-color: #ffffff;
        border: 1px solid #ddd;
        border-radius: 8px;
        box-shadow: 0 4px 8px rgba(0,0,0,0.08);
        text-align: center;
    }

    form label {
        color: #6d4c41;
        display: block;
        margin-bottom: 10px;
        font-weight: bold;
        font-size: 14px;
        text-align: left;
    }

    form input[type="text"],
    form input[type="email"] {
        width: 100%;
        padding: 10px;
        margin: 8px 0 16px;
        border: 1px solid #ccc;
        border-radius: 6px;
        font-size: 14px;
        box-sizing: border-box;
    }

    form button {
        width: 100%;
        padding: 12px;
        background-color: #ff9800;
        color: white;
        font-size: 15px;
        font-weight: bold;
        border: none;
        border-radius: 6px;
        cursor: pointer;
        transition: background-color 0.3s ease;
    }

    form button:hover {
        background-color: #e68900;
    }


</style>
<form action="" method="post">
    <label>Nome:</label><br>
    <input type="text" name="nome" placeholder="José Alduiz" required><br><br><br>

    <label>Email:</label><br>
    <input type="email" name="email" placeholder="josealduiz@gmail.com"><br>

    <button type="submit">Finalizar Pedido</button>
</form>
<?php
include("includes/footer.php");
?>