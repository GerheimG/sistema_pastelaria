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
// Se o formulário foi enviado via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // --- Deletar pedido ---
    if (isset($_POST['delete_id'])) {
        $delete_id = intval($_POST['delete_id']); // Converte o ID para inteiro

        // Deleta o pedido com base no ID
        $sql_delete = "DELETE FROM pedidos WHERE id = $delete_id";
        $conn->query($sql_delete);

        // Redireciona para a mesma página para atualizar a lista de pedidos
        header("Location: " . $_SERVER['PHP_SELF']); // $_SERVER['PHP_SELF'] = nome do arquivo atual (ex: pedidos.php)
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

// ===== CONSULTA PARA EXIBIR TODOS OS PEDIDOS =====
$sql = "SELECT * FROM pedidos ORDER BY data_pedido DESC"; // Lista todos os pedidos do mais recente para o mais antigo
$resultado = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Admin - Gestão de Pedidos</title>
</head>
<style>
        /* ======== ADMIN CSS - Gestão de Pedidos ======== */

    /* Estilo geral da página */
    body {
        font-family: Arial, sans-serif;
        background-color: #f6f7fb;
        color: #333;
        margin: 0;
        padding: 20px;
    }

    /* Título principal */
    h1 {
        color: #2c3e50;
        text-align: center;
        margin-bottom: 30px;
    }

    /* Botão de logout */
    a[href="logout.php"] {
        background-color: #c0392b;
        color: #fff;
        padding: 10px 18px;
        text-decoration: none;
        border-radius: 5px;
        font-weight: bold;
        float: right;
        transition: background-color 0.3s ease;
    }

    a[href="logout.php"]:hover {
        background-color: #a93226;
    }

    /* Tabela */
    table {
        width: 100%;
        border-collapse: collapse;
        background-color: #fff;
        box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        border-radius: 8px;
        overflow: hidden;
    }

    /* Cabeçalho da tabela */
    table thead {
        background-color: #f0f0f0;
    }

    table th, table td {
        padding: 14px 12px;
        text-align: left;
        border-bottom: 1px solid #eee;
    }

    table th {
        color: #555;
        font-weight: bold;
    }

    /* Última linha sem borda */
    table tr:last-child td {
        border-bottom: none;
    }

    /* Células alternadas */
    table tbody tr:nth-child(even) {
        background-color: #fafafa;
    }

    /* Formulário de status */
    form {
        display: inline-block;
        margin-right: 8px;
    }

    select {
        padding: 6px 8px;
        border-radius: 4px;
        border: 1px solid #ccc;
        font-size: 14px;
    }

    /* Botões de ação */
    button {
        padding: 6px 10px;
        font-size: 13px;
        font-weight: bold;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    button[type="submit"] {
        background-color: #3498db;
        color: #fff;
        transition: background-color 0.2s ease;
    }

    button[type="submit"]:hover {
        background-color: #2980b9;
    }

    /* Botão excluir (vermelho) */
    form button[onclick] {
        background-color: #e74c3c;
        margin-left: 5px;
    }

    form button[onclick]:hover {
        background-color: #c0392b;
    }

    /* Mensagem de nenhum pedido */
    p {
        text-align: center;
        margin-top: 50px;
        color: #888;
        font-size: 16px;
    }

</style>
<body>

    <!-- Botão de logout -->
    <a href="logout.php" style="
        display:inline-block; 
        margin-bottom:20px; 
        padding:8px 15px; 
        background:#c00; 
        color:#fff; 
        text-decoration:none; 
        border-radius:4px;
    ">Sair</a>

    <h1>Pedidos</h1>

    <?php if ($resultado && $resultado->num_rows > 0): ?>
        <!-- 
            Verifica se a consulta ao banco retornou resultados.
            Se houver pedidos (linhas encontradas), exibe a tabela.
        -->

        <!-- Início da tabela de pedidos -->
        <table border="1" cellpadding="10" cellspacing="0">
            <thead>
                <tr>
                    <th>ID Pedido</th>             <!-- Coluna com o ID do pedido -->
                    <th>Cliente</th>               <!-- Nome do cliente que fez o pedido -->
                    <th>Status</th>                <!-- Status atual do pedido -->
                    <th>Data do Pedido</th>        <!-- Data e hora que o pedido foi feito -->
                    <th>Ações</th>                 <!-- Coluna para botões de ação (editar status, deletar) -->
                </tr>
            </thead>
            <tbody>

            <!-- Loop que percorre todos os pedidos encontrados no banco -->
            <?php while ($pedido = $resultado->fetch_assoc()): ?> 
                <tr>
                    <!-- Exibe o ID do pedido -->
                    <td><?= htmlspecialchars($pedido['id']) ?></td>

                    <!-- Exibe o nome do cliente (protege contra XSS com htmlspecialchars) -->
                    <td><?= htmlspecialchars($pedido['cliente_nome']) ?></td>

                    <!-- Exibe o status atual do pedido -->
                    <td><?= htmlspecialchars($pedido['status']) ?></td>

                    <!-- Exibe a data e hora do pedido -->
                    <td><?= htmlspecialchars($pedido['data_pedido']) ?></td>

                    <td>
                        <!-- === Formulário para alterar o status do pedido === -->
                        <form action="" method="POST">
                            <!-- Envia o ID do pedido escondido para saber qual atualizar -->
                            <input type="hidden" name="pedido_id" value="<?= $pedido['id'] ?>">

                            <!-- Menu suspenso com os 3 status possíveis -->
                            <select name="status">
                                <!-- O valor atual do status vem selecionado -->
                                <option value="Em Preparo" <?= $pedido['status'] == 'Em Preparo' ? 'selected' : '' ?>>Em Preparo</option>
                                <option value="Pronto" <?= $pedido['status'] == 'Pronto' ? 'selected' : '' ?>>Pronto</option>
                                <option value="Entregue" <?= $pedido['status'] == 'Entregue' ? 'selected' : '' ?>>Entregue</option>
                            </select>

                            <!-- Botão para enviar o formulário e atualizar o status -->
                            <button type="submit">Atualizar</button>
                        </form>

                        <!-- === Formulário para deletar o pedido === -->
                        <form method="POST" style="display:inline;">
                            <!-- Campo oculto com o ID do pedido a ser deletado -->
                            <input type="hidden" name="delete_id" value="<?= $pedido['id'] ?>">

                            <!-- Botão para deletar, com confirmação via JS -->
                            <button type="submit" onclick="return confirm('Confirma exclusão?')">Deletar</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>

            </tbody>
        </table>
    <?php else: ?>
        <!-- Caso a consulta não encontre pedidos, exibe uma mensagem -->
        <p>Nenhum pedido encontrado.</p>
    <?php endif; ?>


</body>
</html>
