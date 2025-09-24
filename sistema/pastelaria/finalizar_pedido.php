<?php
// Inicia a sessão para acessar o carrinho e outras informações do usuário
session_start();
include("includes/db.php");
include("includes/header.php");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Protege contra injeção SQL escapando os campos recebidos
    $nome = isset($_POST['nome']) ? mysqli_real_escape_string($conn, $_POST['nome']) : '';
    $telefone = isset($_POST['telefone']) ? mysqli_real_escape_string($conn, $_POST['telefone']) : '';
    $endereco = isset($_POST['endereco']) ? mysqli_real_escape_string($conn, $_POST['endereco']) : '';
    $numero = isset($_POST['numero']) ? mysqli_real_escape_string($conn, $_POST['numero']) : '';
    $email = isset($_POST['email']) ? mysqli_real_escape_string($conn, $_POST['email']) : '';



    // Verifica se o campo nome foi preenchido
    if (!empty($nome)) {
        // Verifica se o carrinho existe e tem pelo menos 1 item
        if (isset($_SESSION['carrinho']) && count($_SESSION['carrinho']) > 0) {

            // Cria o pedido na tabela 'pedidos' com o nome do cliente e status inicial
            $sql_pedido = "INSERT INTO pedidos (cliente_nome, status, data_pedido, telefone, email, endereco, numero) VALUES ('$nome', 'Em preparo', NOW(), '$telefone', '$email', '$endereco', '$numero')";

            // Se o pedido for inserido com sucesso
            if ($conn->query($sql_pedido) === TRUE) {
                // Pega o ID do pedido recém-criado
                $id_pedido = $conn->insert_id;

                // Para cada item do carrinho, insere na tabela itens_pedido
                foreach ($_SESSION['carrinho'] as $id_pastel => $item) {
                    $quantidade = intval($item['quantidade']);
                    $sql_item = "INSERT INTO itens_pedido (id_pedido, id_pastel, quantidade) VALUES ($id_pedido, $id_pastel, $quantidade)";
                    $conn->query($sql_item);
                }
                // Limpa o carrinho após finalizar o pedido
                unset($_SESSION['carrinho']);

                // Redireciona para a página inicial
                header("Location: index.php");
                exit();
            } else {
                // Exibe erro caso a inserção do pedido falhe
                echo "Erro ao finalizar pedido: " . $conn->error;
            }
        } else {
            // Se o carrinho estiver vazio
            echo "<p>Seu carrinho está vazio. Adicione pelo menos um item para finalizar o pedido.</p>";
        }
    } else {
        // Se o nome estiver vazio
        echo "Por favor, preencha seu nome.";
    }
}
// Verifica se existe um parâmetro GET indicando que o pedido foi finalizado com sucesso
if (isset($_GET['sucesso']) && $_GET['sucesso'] == 1) {
    echo "<p>Pedido finalizado com sucesso!</p>";
}
?>
<!-- ===== Formulário de Finalização de Pedido ===== -->
<form class="finalizar-pedido-form" action="" method="post">
    <!-- Campo para o nome do cliente -->
    <label>Nome:</label>
    <input type="text" name="nome" placeholder="José Alduiz" required><br>

    <label>Telefone:</label>
    <input type="text" name="telefone" placeholder="XX XXXX-XXXX" required><br>

    <label>Endereço:</label>
    <input type="text" name="endereco" placeholder="Rua Alexandre Augusto" required><br>

    <label>Número:</label>
    <input type="text" name="numero" placeholder="345" required><br>

    <!-- Campo para o e-mail do cliente -->
    <label>Email:</label>
    <input type="email" name="email" placeholder="josealduiz@gmail.com"><br>

    <!-- Botão para enviar o formulário e finalizar o pedido -->
    <button type="submit">Finalizar Pedido</button>
</form>
<?php
// Inclui o rodapé da página
include("includes/footer.php");
?>
