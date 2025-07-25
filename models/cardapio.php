<?php
include '../controller/db.php';


$tipo = $_SESSION['tipo'];
$nome = $_SESSION['nome'] ?? '';
$posto = $_SESSION['posto'] ?? '';

$dataSelecionada = $_GET['data'] ?? date('Y-m-d');

$mensagem = ''; // Definindo fora do if para evitar erro

// Atualiza cardápio (somente admin ou gerente no dia atual)
if (($tipo == 'admin' || $tipo == 'gerente') && $_SERVER['REQUEST_METHOD'] === 'POST' && $dataSelecionada === date('Y-m-d')) {
    $cafe = $_POST['cafe'] ?? '';
    $almoco = $_POST['almoco'] ?? '';
    $janta = $_POST['janta'] ?? '';

    $stmt = $pdo->prepare("REPLACE INTO cardapio (dia, cafe, almoco, janta) VALUES (:dia, :cafe, :almoco, :janta)");
    $stmt->execute([
        ':dia' => $dataSelecionada,
        ':cafe' => $cafe,
        ':almoco' => $almoco,
        ':janta' => $janta
    ]);

    // Define a mensagem para o JavaScript
    $_SESSION['mensagem'] = "Cardápio do dia {$dataSelecionada} atualizado com sucesso!";
    header("Location: cardapio.php?data=$dataSelecionada"); // Redireciona para evitar reenvio de formulário
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM cardapio WHERE dia = :dia");
$stmt->execute([':dia' => $dataSelecionada]);
$cardapio = $stmt->fetch(PDO::FETCH_ASSOC);
?>
