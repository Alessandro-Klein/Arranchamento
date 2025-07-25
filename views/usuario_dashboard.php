<?php
session_start();
include '../controller/db.php';

// Verifica se o usuário está logado e se não é administrador
if (!isset($_SESSION['user_id']) || $_SESSION['tipo'] == 'admin' || $_SESSION['tipo'] == 'gerente') {
    header("Location: index.php");
    exit();
}

// Busca os dados do usuário logado (agora incluindo o posto)
$id_usuario = $_SESSION['user_id'];
$stmt = $pdo->prepare("SELECT nome, nome_guerra, posto FROM users WHERE id = ?");
$stmt->execute([$id_usuario]);
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$usuario) {
    echo "Usuário não encontrado.";
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Área do Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
     <link href="../css/usuario.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
    <div class="container">
        <img src="../img/soldado01.avif" width="92" alt="soldado" class="me-2"> 
        <h4 class="mb-1">Bem-vindo, <?= htmlspecialchars($usuario['posto']) . ' ' . htmlspecialchars($usuario['nome_guerra']) ?></h4>
        <p class="lead">Clique no botão abaixo para realizar seu arranchamento.</p>
        <a href="arranchar.php" class="btn btn-custom"><i class="fa-solid fa-utensils"></i> Fazer Arranchamento</a>
    </div>

    <script>
        Swal.fire({
            icon: 'success',
            title: 'Bem-vindo!',
            text: '<?= $usuario['nome'] ?>, acesse o arranchamento.',
            confirmButtonColor: '#003366'
        });
    </script>

    <script>
        window.history.forward();
        window.onunload = function () { null };
    </script>
</body>
</html>

