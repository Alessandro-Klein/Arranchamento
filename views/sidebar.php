<?php
session_start();

// Verifica se o usuário está logado
if (!isset($_SESSION['tipo'])) {
    header("Location: index.php");
    exit();
}

// Redireciona automaticamente para o dashboard correto
$tipo = $_SESSION['tipo'];
if ($tipo == 'admin') {
    $dashboard = 'admin_dashboard.php';
} elseif ($tipo == 'gerente') {
    $dashboard = 'gerente_dashboard.php';
} else {
    $dashboard = 'arranchar.php'; // Usuário comum
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Arranchamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            display: flex;
            min-height: 100vh;
            margin: 0;
        }
        .sidebar {
            width: 250px;
            background-color: #003366;
            color: white;
            padding: 20px;
        }
        .sidebar h4 {
            color: #fff;
            font-weight: bold;
        }
        .sidebar a {
            color: #ccc;
            display: block;
            padding: 10px 0;
            text-decoration: none;
        }
        .sidebar a:hover {
            color: #fff;
            text-decoration: underline;
        }
        .content {
            flex: 1;
            padding: 30px;
            background-color: #f4f6f9;
        }
    </style>
</head>
<body>

<div class="sidebar">
    <h4><i class="fa-solid fa-utensils"></i> Arranchamento</h4>
    <hr>

    <p><strong><?= $_SESSION['posto'] ?? '' ?> <?= $_SESSION['nome'] ?? '' ?></strong><br>
    <span class="badge bg-light text-dark"><?= ucfirst($tipo) ?></span></p>

    <a href="arranchar.php"><i class="fa-solid fa-bowl-food"></i> Arranchar</a>

    <?php if ($tipo == 'admin' || $tipo == 'gerente'): ?>
        <a href="relatorio_arranchamento.php"><i class="fa-solid fa-chart-line"></i> Relatórios</a>
        <a href="cardapio.php"><i class="fa-solid fa-book-open"></i> Cardápio</a>
    <?php endif; ?>

    <?php if ($tipo == 'admin'): ?>
        <a href="cadastro.php"><i class="fa-solid fa-user-plus"></i> Cadastro de Usuários</a>
        <a href="admin_dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard Admin</a>
    <?php elseif ($tipo == 'gerente'): ?>
        <a href="gerente_dashboard.php"><i class="fa-solid fa-gauge"></i> Dashboard Gerente</a>
    <?php endif; ?>

    <hr>
    <a href="logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
</div>

<div class="content">
    <!-- Aqui vem o conteúdo da página atual -->
    <h3>Bem-vindo ao sistema!</h3>
    <p>Use a barra lateral para navegar.</p>
</div>

<script>
    // Proteja com JavaScript extra para evitar voltar via botão “voltar”
    window.history.forward();
    window.onunload = function () { null };
</script>
</body>
</html>
