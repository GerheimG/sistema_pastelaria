<?php
session_start();
include("includes/db.php");

// ===== Verificação de acesso =====
// Verifica se o usuário está logado e se é o 'admin'
if (!isset($_SESSION['usuario_login']) || $_SESSION['usuario_login'] !== 'admin') {
    // Redireciona para a página de login se não for admin
    header("Location: login.php");
    exit();
}

// ====== PROCESSAMENTO DOS FORMULÁRIOS ======
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- DELETAR USUARIO ---
    if (isset($_POST['delete_usuario_id'])) {
        $usuario_id = intval($_POST['delete_usuario_id']);

        // Não vai deletar o ADMIN por acidente
        $sql_check = "SELECT login FROM usuarios WHERE id = $usuario_id";
        $resultado_check = $conn->query(@$sql_check);
        // Verifica se a variável $resultado_check não está vazia e se contém pelo menos uma linha
        if ($resultado_check && $resultado_check->num_rows > 0) {
            // Obtém a próxima linha do resultado como um array associativo
            $row = $resultado_check->fetch_assoc();

            if ($row['login'] !== 'admin') {
                $conn->query("DELETE FROM usuarios WHERE id = $usuario_id");
            }
        }

        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // --- Atualizar usuário ---
    if (isset($_POST['editar_usuario'])) {
        $usuario_id = intval($_POST['usuario_id']);
        $novo_login = $conn->real_escape_string($_POST['novo_login']);
        $nova_senha = $_POST['nova_senha'];

        if ($nova_senha) {
            // Atualiza login e senha (atenção: ideal usar hash na senha)
            $sql = "UPDATE usuarios SET login = '$novo_login', senha = '$nova_senha' WHERE id = $usuario_id";
        } else {
            // Atualiza só o login se a senha estiver vazia
            $sql = "UPDATE usuarios SET login = '$novo_login' WHERE id = $usuario_id";
        }

        $conn->query($sql);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    // --- DELETAR PEDIDO ---
    if (isset($_POST['delete_id'])) {
        $delete_id = intval($_POST['delete_id']); // Converte o ID para inteiro

        // Deleta o pedido com base no ID
        $sql_delete = "DELETE FROM pedidos WHERE id = $delete_id";
        $conn->query($sql_delete);

        // Redireciona para a mesma página para atualizar a lista de pedidos
        header("Location: " . $_SERVER['PHP_SELF']); // $_SERVER['PHP_SELF'] = nome do arquivo atual (ex: admin.php)
        exit();
    }

    // --- Atualizar status do pedido ---
    if (isset($_POST['pedido_id']) && isset($_POST['status'])) {
        $pedido_id = intval($_POST['pedido_id']); // Converte ID para inteiro
        $status = $_POST['status']; // Novo status selecionado

        // Lista de status válidos
        $status_validos = ['Em Preparo', 'Pronto', 'Entregue'];

        // Verifica se o status enviado é válido
        if (in_array($status, $status_validos)) {
            // Atualiza o status do pedido no banco
            $sql_update = "UPDATE pedidos SET status = '$status' WHERE id = $pedido_id";
            $conn->query($sql_update);
        }

        // Redireciona para a mesma página (evita reenvio do formulário)
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}
// ===== CONSULTAS =====
// Consulta para exibir todos os pedidos
$sql_pedidos = "SELECT * FROM pedidos ORDER BY data_pedido DESC"; 
$resultado = $conn->query($sql_pedidos);

// Consulta para exibir todos os usuários
$sql_clientes = "SELECT * FROM usuarios ORDER BY id DESC";
$resultado_cliente = $conn->query($sql_clientes);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gestão de Pedidos</title>
    <link rel="stylesheet" href="css/estilo.css">
</head>

<body>
    <div class="admin-pedidos">

        <!-- Botão de logout -->
        <a href="logout.php">Sair</a>

        <h2>Pedidos</h2>

        <?php if ($resultado && $resultado->num_rows > 0): ?>
            <!-- Tabela de pedidos -->
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID Pedido</th>
                        <th>Cliente</th>
                        <th>Status</th>
                        <th>Data do Pedido</th>
                        <th>Telefone</th>
                        <th>Email</th>
                        <th>Endereço</th>
                        <th>Número</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($pedido = $resultado->fetch_assoc()): ?> 
                    <tr>
                        <td><?= htmlspecialchars($pedido['id']) ?></td>
                        <td><?= htmlspecialchars($pedido['cliente_nome']) ?></td>
                        <td><?= htmlspecialchars($pedido['status']) ?></td>
                        <td><?= htmlspecialchars($pedido['data_pedido']) ?></td>
                        <td><?= htmlspecialchars($pedido['telefone']) ?></td>
                        <td><?= htmlspecialchars($pedido['email']) ?></td>
                        <td><?= htmlspecialchars($pedido['endereco']) ?></td>
                        <td><?= htmlspecialchars($pedido['numero']) ?></td>
                        <td>
                            <!-- Formulário para alterar o status do pedido -->
                            <form action="" method="POST">
                                <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
                                <select name="status">
                                    <option value="Em Preparo" <?= $pedido['status'] == 'Em Preparo' ? 'selected' : '' ?>>Em Preparo</option>
                                    <option value="Pronto" <?= $pedido['status'] == 'Pronto' ? 'selected' : '' ?>>Pronto</option>
                                    <option value="Entregue" <?= $pedido['status'] == 'Entregue' ? 'selected' : '' ?>>Entregue</option>
                                </select>
                                <button type="submit">Atualizar</button>
                                <input type="hidden" name="delete_id" value="<?= $pedido['id'] ?>">
                                <button type="submit">Concluído</button>
                            </form>

                            <!-- Formulário para deletar o pedido -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_id" value="<?= $pedido['id'] ?>">
                                <button type="submit" onclick="return confirm('Confirma exclusão?')">Deletar</button>
                            </form>

                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum pedido encontrado.</p>
        <?php endif; ?>

    </div>

    <div class="admin-pedidos">
        <h2>Usuários</h2>
        <?php if ($resultado_cliente && $resultado_cliente->num_rows > 0): ?>
            <table border="1" cellpadding="10" cellspacing="0">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Login</th>
                        <th>Senha</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($usuario = $resultado_cliente->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($usuario['id']) ?></td>
                        <td><?= htmlspecialchars($usuario['login']) ?></td>
                        <td><?= htmlspecialchars($usuario['senha'] ?? '') ?></td> <!-- ajuste conforme seu banco -->
                        <td>
                            <!-- Form para editar login e senha -->
                            <form method="POST" style="display:inline; gap:8px; align-items:center;">
                                <!--
                                    Campo oculto que envia o ID do usuário no formulário.
                                    Isso permite identificar qual usuário será editado no backend ao enviar o formulário.
                                -->
                                <input type="hidden" name="usuario_id" value="<?= $usuario['id'] ?>" />
                                <input type="text" name="novo_login" value="<?= htmlspecialchars($usuario['login']) ?>" required /> <!--O nome do usuário vem do banco de dados-->
                                <input type="password" name="nova_senha" placeholder="Nova senha" />
                                <button type="submit" name="editar_usuario">Salvar</button>
                            </form>

                            <!-- Form para deletar usuário -->
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="delete_usuario_id" value="<?= $usuario['id'] ?>" />
                                <button type="submit" onclick="return confirm('Confirma exclusão?')">Excluir</button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        <?php else: ?>
            <p>Nenhum usuário encontrado.</p>
        <?php endif; ?>
    </div>
</body>
</html>
