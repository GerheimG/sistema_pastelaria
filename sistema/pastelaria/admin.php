<?php
session_start();
include("includes/db.php"); // Inclui a conexão com o banco

if (!isset($_SESSION['usuario_login']) || $_SESSION['usuario_login'] !== 'admin') {
    header("Location: login.php");
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['delete_id'])) {
        $delete_id = intval($_POST['delete_id']);
        $sql_delete = "DELETE FROM pedidos WHERE id = $delete_id";
        $conn->query($sql_delete);
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }

    if (isset($_POST['pedido_id']) && isset($_POST['status'])) {
        $pedido_id = intval($_POST['pedido_id']);
        $status = $_POST['status'];
        $status_validos = ['Em Preparo', 'Pronto', 'Entregue'];

        if (in_array($status, $status_validos)) {
            $sql_update = "UPDATE pedidos SET status = '$status' WHERE id = $pedido_id";
            $conn->query($sql_update);
        }
        header("Location: " . $_SERVER['PHP_SELF']);
        exit();
    }
}



// Aqui você pode consultar os pedidos para exibir e gerenciar
$sql = "SELECT * FROM pedidos ORDER BY data_pedido DESC";
$resultado = $conn->query($sql);


?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gestão de Pedidos</title>
</head>
<body>
    <a href="logout.php" style="display:inline-block; margin-bottom:20px; padding:8px 15px; background:#c00; color:#fff; text-decoration:none; border-radius:4px;">Sair</a>

    <h1>Pedidos</h1>
    <?php if ($resultado && $resultado->num_rows > 0): ?>
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>Cliente</th>
                    <th>Status</th>
                    <th>Data do Pedido</th>
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
                    <td>
                        <form action="" method="POST">
                            <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">
                            <select name="status">
                                <option value="Em Preparo" <?= $pedido['status'] == 'Em Preparo' ? 'selected' : '' ?>>Em Preparo</option>
                                <option value="Pronto" <?= $pedido['status'] == 'Pronto' ? 'selected' : '' ?>>Pronto</option>
                                <option value="Entregue" <?= $pedido['status'] == 'Entregue' ? 'selected' : '' ?>>Entregue</option>
                            </select>
                            <button type="submit">Atualizar</button>
                        </form>
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

</body>
</html>
