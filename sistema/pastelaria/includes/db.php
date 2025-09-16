<?php
$host = "localhost";   // servidor (pode ser 127.0.0.1)
$user = "root";        // usuário do MySQL
$pass = "";            // senha do MySQL (deixa vazio no XAMPP/MAMP padrão)
$db   = "pastelaria_fidelim"; // nome do banco

$conn = new mysqli($host, $user, $pass, $db);

// Verifica se deu erro
if ($conn->connect_error) {
    die("Erro de conexão: " . $conn->connect_error);
}
?>
