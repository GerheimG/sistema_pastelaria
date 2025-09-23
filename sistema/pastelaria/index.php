<?php
// Inicia a sessão para poder usar variáveis de sessão (como login do usuário)
session_start();
include("includes/db.php");

// Verifica se o usuário já está logado
if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['usuario_login'] === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: cardapio.php");
    }
    // Encerra a execução do script após redirecionamento
    exit();
}
$erro = '';

// Verifica se o formulário foi enviado via método POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Protege contra injeção SQL escapando os caracteres especiais do login
    $login = mysqli_real_escape_string($conn, $_POST['login']);
    $senha = $_POST['senha']; // A senha é recebida diretamente

    // Monta a consulta SQL para buscar o usuário com aquele login
    $sql = "SELECT * FROM usuarios WHERE login = '$login' LIMIT 1";
    $resultado = $conn->query($sql);

    // Verifica se a consulta retornou exatamente 1 usuário
    if ($resultado && $resultado->num_rows === 1) {
        // Converte o resultado em um array associativo
        $usuario = $resultado->fetch_assoc();

        // Compara a senha enviada com a senha armazenada
        if ($senha === $usuario['senha']) {
            // Se a senha estiver correta, salva dados na sessão
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_login'] = $usuario['login'];

            // Redireciona dependendo do tipo de usuário (admin ou comum)
            if ($usuario['login'] === 'admin') {
                header("Location: admin.php");
            } else {
                header("Location: cardapio.php");
            }
            exit(); // Encerra o script após redirecionar
        } else {
            // Caso a senha esteja incorreta, define mensagem de erro
            $erro = "Senha incorreta.";
        }
    } else {
        // Se nenhum usuário for encontrado com aquele login
        $erro = "Usuário não encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Login - Pastelaria</title>
    <!-- Importa o CSS externo -->
    <link rel="stylesheet" href="css/estilo.css">
</head>
<body>
    <!-- Container centralizado para o formulário de login -->
    <div class="login-container">
        <h2>Login</h2>

        <!-- Exibe mensagem de erro, se existir -->
        <?php if ($erro): ?>
            <div class="erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>

        <!-- Formulário de login -->
        <form method="POST" action="">
            <input type="text" name="login" placeholder="Usuário" required />
            <input type="password" name="senha" placeholder="Senha" required />
            <button type="submit">Entrar</button> 
        </form>
        <div style="margin-top: 20px">
            <button class="btn-link" onclick="window.location.href='cadastro.php'">Cadastrar</button>
        </div>
    </div>
</body>
</html>
