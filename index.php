<?php
include('Connection.php');

// Inserção
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['nome'], $_POST['preco']) && empty($_POST['id'])) {
    $nome = $_POST['nome'];
    $preco = $_POST['preco'];  

    $stmt = $mysqli->prepare("INSERT INTO produtos (nome, preco) VALUES (?, ?)");    
    $stmt->bind_param("sd", $nome, $preco);
    $stmt->execute();   
    $stmt->close();
}

// Buscar todos os produtos
$stmt = $mysqli->prepare("SELECT id, nome, preco FROM produtos");
$stmt->execute();
$result = $stmt->get_result();
$produtos = $result->fetch_all(MYSQLI_ASSOC);
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4Q6Gf2aSP4eDXB8Miphtr37CMZZQ5oXLH2yaXMJ2w8e2ZtHTl7GptT4jmndRuHDT" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>
    <title>Cadastro de Produto</title>
    <meta charset="utf-8">
    <style>
        table, th, td { border: 1px solid black; border-collapse: collapse; padding: 6px; }
        button { margin: 2px; }
    </style>
</head>
<body>

<h2>Cadastrar Produto</h2>
<form method="post">
    <label>Nome do Produto:</label><br>
    <input type="text" name="nome" required><br><br>

    <label>Preço:</label><br>
    <input type="number" name="preco" step="0.01" required><br><br>

    <label>Estoque:</label><br>
    <input type="number" name="Estoque" step="0.01" required><br><br>

    <input type="submit" value="Cadastrar">
</form>

<h2>Produtos</h2>
<table id="tabela-produtos">
    <thead>
        <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Preço</th>
            <th>Estoque</th>
            <th>Ações</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($produtos as $produto): ?>
        <tr data-id="<?= $produto['id'] ?>">
            <td><?= $produto['id'] ?></td>
            <td class="nome"><?= htmlspecialchars($produto['nome']) ?></td>
            <td class="preco">R$ <?= number_format($produto['preco'], 2, ',', '.') ?></td>
            <td></td>
            <td>
                <button onclick="editar(this)">Editar</button>
                <button onclick="excluir(<?= $produto['id'] ?>, this)">Excluir</button>
            </td>
        </tr>
        <?php endforeach; ?>
    </tbody>
</table>

<script>
function editar(button) {
    const row = button.closest('tr');
    const id = row.dataset.id;
    const nomeCell = row.querySelector('.nome');
    const precoCell = row.querySelector('.preco');

    const nomeAtual = nomeCell.textContent;
    const precoAtual = precoCell.textContent.replace('R$', '').replace('.', '').replace(',', '.');

    nomeCell.innerHTML = `<input type="text" id="nome-${id}" value="${nomeAtual.trim()}">`;
    precoCell.innerHTML = `<input type="number" step="0.01" id="preco-${id}" value="${precoAtual}">`;
    button.textContent = "Salvar";
    button.onclick = () => salvar(button, id);
}

function salvar(button, id) {
    const nome = document.getElementById(`nome-${id}`).value;
    const preco = document.getElementById(`preco-${id}`).value;

    fetch('atualizar_produto.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `id=${id}&nome=${encodeURIComponent(nome)}&preco=${preco}`
    })
    .then(response => response.text())
    .then(data => {
        const row = button.closest('tr');
        row.querySelector('.nome').textContent = nome;
        row.querySelector('.preco').textContent = "R$ " + parseFloat(preco).toFixed(2).replace('.', ',');
        button.textContent = "Editar";
        button.onclick = () => editar(button);
        alert("Produto atualizado com sucesso!");
    })
    .catch(error => {
        console.error("Erro:", error);
        alert("Erro ao atualizar o produto.");
    });
}

function excluir(id, button) {
    if (confirm('Tem certeza que deseja excluir este produto?')) {
        fetch('excluir_produto.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: `id=${id}`
        })
        .then(response => response.text())
        .then(data => {
            if (data === 'success') {
                button.closest('tr').remove();
                alert('Produto excluído com sucesso!');
            } else {
                alert('Erro ao excluir o produto.');
            }
        })
        .catch(error => {
            console.error("Erro:", error);
            alert("Erro ao excluir o produto.");
        });
    }
}
</script>

</body>
</html>