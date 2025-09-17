<?php
session_start();
include("includes/db.php");

// Se o usuário já está logado, redireciona para o cardápio (ou área admin)
if (isset($_SESSION['usuario_id'])) {
    if ($_SESSION['usuario_login'] === 'admin') {
        header("Location: admin.php");
    } else {
        header("Location: cardapio.php");
    }
    exit();
}

$erro = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $login = mysqli_real_escape_string($conn, $_POST['login']);
    $senha = $_POST['senha'];

    // Busca o usuário no banco pelo login
    $sql = "SELECT * FROM usuarios WHERE login = '$login' LIMIT 1";
    $resultado = $conn->query($sql);

    if ($resultado && $resultado->num_rows === 1) {
        $usuario = $resultado->fetch_assoc();

        // Verifica a senha (para teste simples, texto puro; ideal usar password_hash)
        if ($senha === $usuario['senha']) {
            // Login válido, salva na sessão
            $_SESSION['usuario_id'] = $usuario['id'];
            $_SESSION['usuario_login'] = $usuario['login'];

            // Redireciona conforme o usuário
            if ($usuario['login'] === 'admin') {
                header("Location: admin/pedidos.php");
            } else {
                header("Location: cardapio.php");
            }
            exit();
        } else {
            $erro = "Senha incorreta.";
        }
    } else {
        $erro = "Usuário não encontrado.";
    }
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <title>Login - Pastelaria</title>
    <link rel="stylesheet" href="css/estilo.css" />
</head>
<body>
    <style>
        /* ===== Estilo do Login ===== */
        .login-container {
            max-width: 350px;
            margin: 80px auto;
            padding: 30px;
            background-color: #ffffff;
            border: 1px solid #ddd;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.08);
            text-align: center;
        }

        .login-container h2 {
            color: #6d4c41;
            margin-bottom: 20px;
        }

        .login-container input[type="text"],
        .login-container input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 8px 0 16px;
            border: 1px solid #ccc;
            border-radius: 6px;
            font-size: 14px;
        }

        .login-container button {
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

        .login-container button:hover {
            background-color: #e68900;
        }

        .erro {
            color: #e74c3c;
            margin-bottom: 10px;
            font-size: 14px;
        }

    </style>
    <div class="login-container">
        <h2>Login</h2>
        <?php if ($erro): ?>
            <div class="erro"><?= htmlspecialchars($erro) ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <input type="text" name="login" placeholder="Usuário" required />
            <input type="password" name="senha" placeholder="Senha" required />
            <button type="submit">Entrar</button>
        </form>
    </div>
</body>
</html>
