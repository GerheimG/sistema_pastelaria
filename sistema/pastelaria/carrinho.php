<?php 
session_start(); 
include("includes/db.php");
include("includes/header.php");  

if (isset($_POST['id'])) {
    $id = intval($_POST["id"]);

    $sql = "SELECT * FROM pasteis WHERE id = $id";
    $resultado = $conn->query($sql);

    if ($resultado->num_rows > 0) {
        $pastel = $resultado->fetch_assoc();

        if (!isset($_SESSION["carrinho"])) {
            $_SESSION["carrinho"] = [];
        }

        $id = $pastel['id'];
        if (isset($_SESSION['carrinho'][$id])) {
            $_SESSION['carrinho'][$id]['quantidade'] += 1;
        } else {
            $_SESSION['carrinho'][$id] = [
                'nome' => $pastel['nome'],
                'preco' => $pastel['preco'],
                'quantidade' => 1
            ];
        }
    }
}
?>

<h1>Seu carrinho</h1>

<?php

    if (count($_SESSION['carrinho']) === 0) {
        echo "O carrinho está vazio";
    } else {
        foreach ($_SESSION['carrinho'] as $carrinho) {
            echo $carrinho['nome'];
        }
    };


?>