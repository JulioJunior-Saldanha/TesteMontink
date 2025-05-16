<?php
include('Connection.php');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $id = intval($_POST['id']);
    $nome = $_POST['nome'];
    $preco = floatval($_POST['preco']);

    $stmt = $mysqli->prepare("UPDATE produtos SET nome = ?, preco = ? WHERE id = ?");
    $stmt->bind_param("sdi", $nome, $preco, $id);
    $stmt->execute();
    $stmt->close();

    echo "OK";
}
?>
