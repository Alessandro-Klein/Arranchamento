<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['tipo'])) {
    header("Location: index.php");
    exit();
}

$tipo = $_SESSION['tipo'];
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Sistema de Arranchamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="../css/header.css" rel="stylesheet">
     <link rel="icon" href="../img/Base.png" type="image/png">
    
</head>
<body>

<div class="sidebar">
    <h4><i class="fa-solid fa-utensils"></i> Arranchamento</h4>
    <hr>

    <p><strong><?= $_SESSION['posto'] ?? '' ?> <?= $_SESSION['nome'] ?? '' ?></strong><br>
    <span class="badge bg-light text-dark"><?= ucfirst($tipo) ?></span></p>

    <a href="arranchar.php"><i class="fa-solid fa-bowl-food"></i> Arranchar</a>

    <a href="cardapio.php"><i class="fa-solid fa-book-open"></i> Cardápio</a>

    <?php if ($tipo == 'admin' || $tipo == 'gerente'): ?>
        <a href="agendar_arranchamento.php"><i class="fa-solid fa-calendar-plus"></i> Agendar Arranchamento</a>
        <a href="relatorio_arranchamento.php"><i class="fa-solid fa-chart-line"></i> Relatórios</a>
        <a href="tiragem_faltas.php"><i class="fa-solid fa-user-check"></i> Tiragem de Faltas</a>
    <?php endif; ?>

    <?php if ($tipo == 'admin'): ?>
        <a href="cadastro_perfil.php"><i class="fa-solid fa-user-plus"></i> Cadastro</a>
        <a href="admin_dashboard.php"><i class="fa-solid fa-gauge"></i> Admin Dashboard</a>
    <?php elseif ($tipo == 'gerente'): ?>
        <a href="gerente_dashboard.php"><i class="fa-solid fa-gauge"></i> Gerente Dashboard</a>
    <?php endif; ?>

    <hr>
    <a href="../logout.php" class="text-danger"><i class="fa-solid fa-right-from-bracket"></i> Sair</a>
</div>

<div class="content">


