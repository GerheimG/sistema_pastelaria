<?php
session_start();
include("includes/db.php");

// Inicializa variáveis para mensagem e controle de redirecionamento
$mensagem = '';
$redirecionar = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Escapa o login para evitar SQL Injection
    $login = mysqli_real_escape_string($conn, $_POST['login']);
    // Recebe a senha diretamente (ideal usar hash em produção)
    $senha = $_POST['senha'];

    // Verifica se já existe um usuário com esse login
    $check = "SELECT id FROM usuarios WHERE login = '$login'";
    $resultado = $conn->query($check);

    if ($resultado->num_rows > 0) {
        // Usuário já cadastrado, exibe mensagem de erro
        $mensagem = "<p>Usuário já cadastrado</p>";
    } else {
        // Insere novo usuário na tabela
        $sql = "INSERT INTO usuarios (login, senha) VALUES ('$login', '$senha')";
        if ($conn->query($sql) === TRUE) {
            // Cadastro com sucesso, exibe mensagem e prepara redirecionamento
            $mensagem = "<p>Usuário cadastrado com sucesso!</p>";
            $redirecionar = true;  // sinaliza que deve redirecionar
        } else {
            // Erro ao inserir no banco, exibe mensagem
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

        <!-- Exibe a mensagem (erro ou sucesso) -->
        <?= $mensagem ?>

        <!-- Formulário para cadastro de novo usuário -->
        <form method="POST" action="">
            <input type="text" name="login" placeholder="Usuário" required />
            <input type="password" name="senha" placeholder="Senha" required />
            <button type="submit">Cadastrar</button>
        </form>

        <!-- Se cadastro foi realizado com sucesso, executa redirecionamento após 3 segundos -->
        <?php if ($redirecionar): ?>
        <script>
            setTimeout(function() {
                window.location.href = 'index.php';  // Redireciona para index.php
            }, 3000); // Tempo em milissegundos (3 segundos)
        </script>
        <?php endif; ?>
    </div>
</body>
</html>
