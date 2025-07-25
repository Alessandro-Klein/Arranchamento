<?php
// Verifica se o usuário está logado e é admin ou gerente
if (!isset($_SESSION['tipo']) || ($_SESSION['tipo'] != 'admin' && $_SESSION['tipo'] != 'gerente')) {
    header("Location: index.php");
    exit();
}

// Filtro por data - se não houver, usa a data atual
$filterData = isset($_GET['data']) ? $_GET['data'] : date('Y-m-d');

// Consulta SQL com JOIN
$sqlAgendamentos = "SELECT a.*, u.id AS user_id, u.nome AS nome_usuario, u.nome_guerra, u.posto, u.om 
                    FROM arranchamento a 
                    JOIN users u ON a.user_id = u.id
                    WHERE a.data = :data";

$stmt = $pdo->prepare($sqlAgendamentos);
$stmt->bindParam(':data', $filterData);
$stmt->execute();

// Agrupando por data e usuário
$agendamentos = [];
$contagemRefeicoes = ['cafe' => 0, 'almoco' => 0, 'jantar' => 0];

while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
    $data = $row['data'];
    $userId = $row['user_id'];

    $agendamentos[$data][$userId]['nome'] = $row['nome_usuario'];
     $agendamentos[$data][$userId]['nome_guerra'] = $row['nome_guerra'];
    $agendamentos[$data][$userId]['posto'] = $row['posto'];
    $agendamentos[$data][$userId]['om'] = $row['om'];
    $agendamentos[$data][$userId]['refeicoes'][] = $row['refeicao'];

    if ($row['refeicao'] == 'Café da Manhã') $contagemRefeicoes['cafe']++;
    elseif ($row['refeicao'] == 'Almoço') $contagemRefeicoes['almoco']++;
    elseif ($row['refeicao'] == 'Jantar') $contagemRefeicoes['jantar']++;
}

$totalArranchamentos = array_sum($contagemRefeicoes);

?>