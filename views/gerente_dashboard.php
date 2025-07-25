<?php
session_start();
include '../controller/db.php';
include '../includes/header.php';

if ($_SESSION['tipo'] != 'gerente') {
    header("Location: index.php");
    exit();
}

date_default_timezone_set('America/Sao_Paulo'); // <-- Fuso horário correto

$usuarioId = $_SESSION['user_id'];

// Busca nome e posto do gerente
$sqlUsuario = "SELECT nome, posto, nome_guerra FROM users WHERE id = :user_id";
$stmt = $pdo->prepare($sqlUsuario);
$stmt->bindParam(':user_id', $usuarioId);
$stmt->execute();
$usuario = $stmt->fetch(PDO::FETCH_ASSOC);

// Data atual
$dataHoje = date("Y-m-d");

// Total de arranchamentos de hoje
$sqlTotalHoje = "SELECT COUNT(*) FROM arranchamento WHERE data = :hoje";
$stmtTotal = $pdo->prepare($sqlTotalHoje);
$stmtTotal->bindParam(':hoje', $dataHoje);
$stmtTotal->execute();
$totalHoje = $stmtTotal->fetchColumn();
?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel do Gerente</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .soldado-img {
            width: 70px;
            height: 70px;
            object-fit: cover;
            border-radius: 50%;
            border: 2px solid #333;
        }
    </style>
</head>
<body>
<div class="container mt-5">
    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h4 class="d-flex align-items-center">
                <img src="../img/soldado01.avif" alt="soldado" class="me-3 soldado-img">
                <div>
                    <h4 class="mb-1">
                        Bem-vindo, <?= htmlspecialchars($usuario['posto']) . ' ' . htmlspecialchars($usuario['nome_guerra']) ?>

                    </h4>
                    <small class="text-muted">Painel do Gerente</small>
                </div>
            </h4><br>
            <p><strong>Horário atual:</strong> <span id="horaAtual"></span></p>
            <p><strong>Data de hoje:</strong> <?= date("d/m/Y") ?></p>
            <p><strong>Total de arranchamentos de hoje:</strong> <?= $totalHoje ?></p>
        </div>
    </div>

    <div class="card shadow-sm">
        <div class="card-header bg-primary text-white">
            <strong>Últimos Arranchamentos Registrados</strong>
        </div>
        <div class="card-body">
            <table class="table table-hover">
                <thead>
                <tr>
                    <th>Data</th>
                    <th>Refeição</th>
                    <th>Militar</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $sqlArranchamentos = "SELECT a.data, a.refeicao, u.nome 
                                      FROM arranchamento a
                                      JOIN users u ON a.user_id = u.id
                                      ORDER BY a.data DESC, a.id DESC
                                      LIMIT 10";
                $stmt = $pdo->query($sqlArranchamentos);
                while ($row = $stmt->fetch(PDO::FETCH_ASSOC)): ?>
                    <tr>
                        <td><?= date("d/m/Y", strtotime($row['data'])) ?></td>
                        <td><?= htmlspecialchars($row['refeicao']) ?></td>
                        <td><?= htmlspecialchars($row['nome']) ?></td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
    // Relógio ao vivo
    function atualizarHora() {
        const agora = new Date();
        const horas = agora.getHours().toString().padStart(2, '0');
        const minutos = agora.getMinutes().toString().padStart(2, '0');
        const segundos = agora.getSeconds().toString().padStart(2, '0');
        document.getElementById('horaAtual').textContent = `${horas}:${minutos}:${segundos}`;
    }

    setInterval(atualizarHora, 1000);
    atualizarHora();

    // Alerta de boas-vindas
    if (!sessionStorage.getItem('boas_vindas')) {
        sessionStorage.setItem('boas_vindas', 'true');
        Swal.fire({
            icon: 'success',
            title: 'Bem-vindo!',
            html: `<img src="https://cdn-icons-png.flaticon.com/512/2965/2965567.png" width="40" class="me-2">
                   <?= htmlspecialchars($usuario['posto']) . ' ' . htmlspecialchars($usuario['nome_guerra']) ?>, você está no painel do gerente.`,
            confirmButtonColor: '#003366'
        });
    }

    // Previne voltar após logout
    window.history.forward();
    window.onunload = function () { null };
</script>
  <script>
        Swal.fire({
            icon: 'success',
            title: 'Bem-vindo!',
            text: '<?= $usuario['nome'] ?>, acesse o arranchamento.',
            confirmButtonColor: '#003366'
        });
    </script>
</body>
</html>
