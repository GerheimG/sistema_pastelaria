<?php
session_start();
include("includes/db.php");

// Inicializa uma variável para mensagens e redirecionar
$mensagem = '';
$redirecionar = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = mysqli_real_escape_string($conn, $_POST['login']);
    $senha = $_POST['senha'];

    $check = "SELECT id FROM usuarios WHERE login = '$login'";
    $resultado = $conn->query($check);

    if ($resultado->num_rows > 0) {
        $mensagem = "<p>Usuário já cadastrado</p>";
    } else {
        $sql = "INSERT INTO usuarios (login, senha) VALUES ('$login', '$senha')";
        if ($conn->query($sql) === TRUE) {
            $mensagem = "<p>Usuário cadastrado com sucesso!</p>";
            $redirecionar = true;  // só aqui redireciona
        } else {
            $mensagem = "<p>Erro ao cadastrar: " . $conn->error . "</p>";
        }
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Cadastro - Pastelaria</title>
    <link rel="stylesheet" href="css/estilo.css" />
</head>
<body>
    <div class="login-container">
        <h2>Cadastro</h2>

        <?= $mensagem ?>

        <form method="POST" action="">
            <input type="text" name="login" placeholder="Usuário" required />
            <input type="password" name="senha" placeholder="Senha" required />
            <button type="submit">Cadastrar</button>
        </form>

        <?php if ($redirecionar): ?>
        <script>
            setTimeout(function() {
                window.location.href = 'index.php';
            }, 3000);
        </script>
        <?php endif; ?>
    </div>
</body>
</html>
