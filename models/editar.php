<?php

if (!isset($_GET['id'])) {
    header("Location: ../admin_dashboard.php");
    exit();
}

$id = $_GET['id'];

// Buscar dados do usuário
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$id]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$user) {
    echo "Usuário não encontrado!";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $nome_guerra = $_POST['nome_guerra'];
    $email = $_POST['email'];
    $posto = $_POST['posto'];
    $tipo = $_POST['tipo'];
    $om = $_POST['om'];
    $senha = $_POST['senha'];

    // Verifica se já existe outro usuário com o mesmo nome
    $verificaNome = $pdo->prepare("SELECT id FROM users WHERE nome = ? AND id != ?");
    $verificaNome->execute([$nome, $id]);

    // Verifica se já existe outro usuário com o mesmo e-mail
    $verificaEmail = $pdo->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
    $verificaEmail->execute([$email, $id]);

    // Verifica se já existe outro usuário com o mesmo nome de guerra e posto
    $verificaGuerraPosto = $pdo->prepare("SELECT id FROM users WHERE nome_guerra = ? AND posto = ? AND id != ?");
    $verificaGuerraPosto->execute([$nome_guerra, $posto, $id]);

    if ($verificaNome->rowCount() > 0) {
        $erro = "Já existe outro usuário com esse nome!";
    } elseif ($verificaEmail->rowCount() > 0) {
        $erro = "Já existe outro usuário com esse e-mail!";
    } elseif ($verificaGuerraPosto->rowCount() > 0) {
        $erro = "Já existe outro usuário com o mesmo nome de guerra e posto!";
    } else {
        if (!empty($senha)) {
            $senhaHash = password_hash($senha, PASSWORD_DEFAULT);
            $sql = "UPDATE users SET nome = ?, nome_guerra = ?, email = ?, posto = ?, tipo = ?, om = ?, senha = ? WHERE id = ?";
            $params = [$nome, $nome_guerra, $email, $posto, $tipo, $om, $senhaHash, $id];
        } else {
            $sql = "UPDATE users SET nome = ?, nome_guerra = ?, email = ?, posto = ?, tipo = ?, om = ? WHERE id = ?";
            $params = [$nome, $nome_guerra, $email, $posto, $tipo, $om, $id];
        }

        $stmt = $pdo->prepare($sql);
        if ($stmt->execute($params)) {
            // ✅ Define a mensagem e redireciona
            $_SESSION['mensagem_sucesso'] = "Usuário alterado com sucesso!";
            header("Location: admin_dashboard.php");
            exit();
        } else {
            $erro = "Erro ao atualizar o usuário.";
        }
    }
}
?>
