<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Pastelaria Do Fidelim</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <header class="topo">
        <div class="container">
            <h1> Pastelaria Do Fidelim</h1>
            <nav class="menu">
                <a href="cardapio.php">Cardápio</a>
                <a href="carrinho.php">Carrinho</a>
                <?php if (isset($_SESSION['usuario_id'])): ?>
                    <a href="logout.php" class="btn-logout">Sair</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

