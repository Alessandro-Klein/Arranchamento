<?php

if (!isset($_SESSION['tipo']) || !in_array($_SESSION['tipo'], ['admin', 'gerente'])) {
    header("Location: index.php");
    exit();
}

$dataSelecionada = $_GET['data'] ?? date('Y-m-d');
$hoje = date('Y-m-d');
$dataEhHoje = $dataSelecionada === $hoje;

$mensagemSucesso = '';
if (isset($_SESSION['sucesso'])) {
    $mensagemSucesso = $_SESSION['sucesso'];
    unset($_SESSION['sucesso']);
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['presencas'])) {
    if ($dataSelecionada !== $hoje) {
        $_SESSION['sucesso'] = 'Você só pode registrar presenças/faltas para o dia atual.';
        header("Location: tiragem_faltas.php?data=$dataSelecionada");
        exit();
    }

    foreach ($_POST['presencas'] as $id => $status) {
        $presente = $status === '1' ? 1 : 0;
        $faltou = $status === '0' ? 1 : 0;

        $update = $pdo->prepare("UPDATE arranchamento SET presente = :presente, faltou = :faltou WHERE id = :id");
        $update->execute([
            ':presente' => $presente,
            ':faltou' => $faltou,
            ':id' => $id
        ]);
    }

    $_SESSION['sucesso'] = 'Presenças e faltas salvas com sucesso!';
    header("Location: tiragem_faltas.php?data=$dataSelecionada");
    exit();
}

$sql = "SELECT a.id, u.id AS user_id, u.nome_guerra, u.posto, a.refeicao, a.presente, a.faltou
        FROM arranchamento a
        JOIN users u ON a.user_id = u.id
        WHERE a.data = :data
        ORDER BY u.nome_guerra";
$stmt = $pdo->prepare($sql);
$stmt->bindParam(':data', $dataSelecionada);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

$usuarios = [];
$contagemPresenca = ['Café da Manhã' => 0, 'Almoço' => 0, 'Jantar' => 0];
$contagemFaltas = ['Café da Manhã' => 0, 'Almoço' => 0, 'Jantar' => 0];

foreach ($rows as $row) {
    $uid = $row['user_id'];
    if (!isset($usuarios[$uid])) {
        $usuarios[$uid] = [
            'nome_guerra' => $row['nome_guerra'],
            'posto' => $row['posto'],
            'refeicoes' => []
        ];
    }

    $usuarios[$uid]['refeicoes'][$row['refeicao']] = [
        'id' => $row['id'],
        'presente' => $row['presente'],
        'faltou' => $row['faltou']
    ];

    if ($row['presente']) $contagemPresenca[$row['refeicao']]++;
    if ($row['faltou']) $contagemFaltas[$row['refeicao']]++;
}
?>