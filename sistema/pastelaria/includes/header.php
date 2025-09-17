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

<style>
        .topo {
            background-color: #ffc107;
            padding: 20px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .topo .container {
            width: 90%;
            max-width: 1000px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .topo h1 {
            font-size: 2em;
            margin: 0;
            color: #4a2e00;
        }

        .menu {
            margin-top: 10px;
        }

        .menu a {
            margin: 0 15px;
            text-decoration: none;
            color: #4a2e00;
            font-weight: bold;
            transition: color 0.3s;
        }

        .menu a:hover {
            color: #000;
        }

        .btn-logout {
            display: inline-block;
            padding: 10px 16px;
            color: white;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-left: 10px;
        }
</style>
