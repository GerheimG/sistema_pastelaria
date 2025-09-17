<?php
session_start();
include("includes/db.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = isset($_POST['nome']) ? mysqli_real_escape_string($conn, $_POST['nome']) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';

    if (!empty($nome)) {
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
        echo "Por favor, preencha seu nome.";
    }
}

if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1) {
    echo "<p>Pedido finalizado com sucesso!</p>";
}
?>

<form action="" method="post">
    <label>Nome:</label><br>
    <input type="text" name="nome" placeholder="José Alduiz" required><br>

    <label>Email:</label><br>
    <input type="email" name="email" placeholder="josealduiz@gmail.com"><br>

    <button type="submit">Finalizar Pedido</button>
</form>
