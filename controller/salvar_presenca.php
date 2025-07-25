<?php
session_start();
include '../db.php';

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['presencas'])) {
    foreach ($_POST['presencas'] as $id => $valor) {
        $presente = ($valor === 'on') ? 1 : 0;
        $update = $pdo->prepare("UPDATE arranchamento SET presente = :presente WHERE id = :id");
        $update->execute([':presente' => $presente, ':id' => $id]);
    }
}
$data = $_POST['data'] ?? date('Y-m-d');
header("Location: ../tiragem_faltas.php?data=$data&sucesso=1");
exit();
