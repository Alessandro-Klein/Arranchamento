<?php


// Conexão com banco
$conn = new mysqli("localhost", "root", "M@ster01", "sistema_arranchamento");
if ($conn->connect_error) {
    die("Erro na conexão com o banco: " . $conn->connect_error);
}

function sanitize_path($path) {
    return basename($path);
}

date_default_timezone_set('America/Sao_Paulo');

// Função para excluir arranchamento com verificação de data
function excluirArranchamento($conn, $id, $user_id) {
    $stmt = $conn->prepare("SELECT data FROM arranchamento WHERE id = ? AND user_id = ?");
    $stmt->bind_param("is", $id, $user_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 0) {
        return ['success' => false, 'message' => 'Agendamento não encontrado.'];
    }

    $row = $result->fetch_assoc();
    $dataArranchamento = $row['data'];
    $dataAtual = date('Y-m-d');

    if ($dataArranchamento <= $dataAtual) {
        return ['success' => false, 'message' => 'Você não pode excluir agendamentos para hoje ou datas passadas.'];
    }

    $stmt = $conn->prepare("DELETE FROM arranchamento WHERE id = ? AND user_id = ?");
    $stmt->bind_param("is", $id, $user_id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        return ['success' => true, 'message' => 'Agendamento excluído com sucesso.'];
    } else {
        return ['success' => false, 'message' => 'Erro ao excluir agendamento.'];
    }
}

// Exclusão (se existir parâmetro delete_id)
if ($_SERVER["REQUEST_METHOD"] === "GET" && isset($_GET['delete_id'])) {
    $id = intval($_GET['delete_id']);
    $user_id = $_SESSION['user_id'];

    $resultado = excluirArranchamento($conn, $id, $user_id);

    $_SESSION['alert'] = [
        'title' => $resultado['success'] ? 'Sucesso!' : 'Erro!',
        'message' => $resultado['message'],
        'icon' => $resultado['success'] ? 'success' : 'warning'
    ];

    header("Location: arranchar.php");
    exit;
}

// INSERÇÃO
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $data = $_POST['data'] ?? '';
    $refeicoes = $_POST['refeicao'] ?? [];
    $user_id = $_SESSION['user_id'];

    if (!empty($data) && !empty($refeicoes)) {
        $dataAtual = date('Y-m-d');
        $horaAtual = date('H:i');

        if ($data < $dataAtual) {
            $_SESSION['alert'] = [
                'title' => 'Erro!',
                'message' => 'Você não pode agendar para dias anteriores!',
                'icon' => 'error'
            ];
            header("Location: arranchar.php");
            exit;
        }

        $dataMinima = date('Y-m-d', strtotime('+2 days'));
        if ($data < $dataMinima) {
            $_SESSION['alert'] = [
                'title' => 'Erro!',
                'message' => 'É necessário agendar com no mínimo 2 dias de antecedência!',
                'icon' => 'warning'
            ];
            header("Location: arranchar.php");
            exit;
        }

        $limites = [
            'Café da Manhã' => '08:30',
            'Almoço' => '10:30',
            'Janta' => '17:00'
        ];

        $diaSemana = date('w', strtotime($data));
        if (in_array($diaSemana, [0, 5, 6])) {
            $_SESSION['alert'] = [
                'title' => 'Dia Não Permitido',
                'message' => 'Apenas guarnição de serviço pode arranchar na sexta-feira, sábado ou domingo.',
                'icon' => 'info'
            ];
            header("Location: arranchar.php");
            exit;
        }

        $erroDuplicado = false;

        foreach ($refeicoes as $refeicao) {
            if ($data == $dataAtual && $horaAtual > $limites[$refeicao]) {
                $_SESSION['alert'] = [
                    'title' => 'Horário Expirado!',
                    'message' => "Você perdeu o horário de agendamento para $refeicao!",
                    'icon' => 'warning'
                ];
                header("Location: arranchar.php");
                exit;
            }

            $stmt = $conn->prepare("SELECT * FROM arranchamento WHERE data = ? AND refeicao = ? AND user_id = ?");
            $stmt->bind_param("sss", $data, $refeicao, $user_id);
            $stmt->execute();
            $result = $stmt->get_result();
            if ($result->num_rows > 0) {
                $erroDuplicado = true;
                break;
            }
            $stmt->close();
        }

        if ($erroDuplicado) {
            $_SESSION['alert'] = [
                'title' => 'Já Agendado',
                'message' => 'Você já agendou uma das refeições selecionadas para esse dia.',
                'icon' => 'info'
            ];
            header("Location: arranchar.php");
            exit;
        }

        foreach ($refeicoes as $refeicao) {
            $stmt = $conn->prepare("INSERT INTO arranchamento (data, refeicao, user_id) VALUES (?, ?, ?)");
            $stmt->bind_param("sss", $data, $refeicao, $user_id);
            $stmt->execute();
            $stmt->close();
        }

        $_SESSION['alert'] = [
            'title' => 'Sucesso!',
            'message' => 'Refeições agendadas com sucesso!',
            'icon' => 'success'
        ];
        header("Location: arranchar.php");
        exit;
    } else {
        $_SESSION['alert'] = [
            'title' => 'Campos Vazios!',
            'message' => 'Todos os campos são obrigatórios.',
            'icon' => 'warning'
        ];
        header("Location: arranchar.php");
        exit;
    }
}

// Carregar agendamentos do usuário
$eventos = [];
$user_id = $_SESSION['user_id'];
$sql = "SELECT id, data, refeicao FROM arranchamento WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $user_id);
$stmt->execute();
$result = $stmt->get_result();
while ($row = $result->fetch_assoc()) {
    $eventos[] = [
        "id" => $row['id'],
        "title" => $row['refeicao'],
        "start" => $row['data']
    ];
}
$stmt->close();
$conn->close();
?>
