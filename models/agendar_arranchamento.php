<?php
function sanitize_input($data) {
    return htmlspecialchars(trim($data));
}

// Pesquisa com proteção contra SQL Injection
$pesquisa = '';
if (isset($_GET['pesquisa'])) {
    $pesquisa = sanitize_input($_GET['pesquisa']);
}

// Buscar todos os usuários (incluindo o campo 'posto'), com ou sem filtro
if (!empty($pesquisa)) {
    $sqlUsuarios = "SELECT id, nome, posto FROM users WHERE nome LIKE :pesquisa";
    $stmt = $pdo->prepare($sqlUsuarios);
    $stmt->bindValue(':pesquisa', "%" . $pesquisa . "%");
    $stmt->execute();
    $usuarios = $stmt->fetchAll(PDO::FETCH_ASSOC);
} else {
    $sqlUsuarios = "SELECT id, nome, posto FROM users";
    $resultUsuarios = $pdo->query($sqlUsuarios);
    $usuarios = $resultUsuarios->fetchAll(PDO::FETCH_ASSOC);
}

// Agendar arranchamento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'agendar') {
    $userId = sanitize_input($_POST['user_id']);
    $data = sanitize_input($_POST['data']);
    $refeicoes = $_POST['refeicoes'] ?? []; // pega múltiplas refeições

    if (strtotime($data) < strtotime(date('Y-m-d'))) {
        echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Data inválida!',
                    text: 'Não é possível agendar para uma data anterior a hoje!',
                    confirmButtonColor: '#003366'
                });
              </script>";
        exit;
    }

    $erros = 0;

    foreach ($refeicoes as $refeicao) {
        $refeicao = sanitize_input($refeicao);

        // Verifica se já existe agendamento para essa refeição
        $sqlCheck = "SELECT COUNT(*) FROM arranchamento 
                     WHERE user_id = :user_id AND refeicao = :refeicao AND data = :data";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->execute([
            ':user_id' => $userId,
            ':refeicao' => $refeicao,
            ':data' => $data
        ]);
        $count = $stmtCheck->fetchColumn();

        if ($count == 0) {
            // Inserir arranchamento
            $sqlInsert = "INSERT INTO arranchamento (user_id, refeicao, data) VALUES (:user_id, :refeicao, :data)";
            $stmtInsert = $pdo->prepare($sqlInsert);
            $executado = $stmtInsert->execute([
                ':user_id' => $userId,
                ':refeicao' => $refeicao,
                ':data' => $data
            ]);

            if (!$executado) {
                $erros++;
            }
        } else {
            $erros++;
        }
    }

    if ($erros == count($refeicoes)) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erro',
                    text: 'Todos os arranchamentos já estão agendados para esta data.',
                    confirmButtonColor: '#003366'
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Arranchamento realizado com sucesso!',
                    confirmButtonColor: '#003366'
                }).then(() => {
                    window.location.href = 'arranchar.php';
                });
              </script>";
    }
}

// Cancelar arranchamento
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'cancelar') {
    $arranchamentoId = sanitize_input($_POST['arranchamento_id']);
    $usuarioId = $_SESSION['usuario_id'];
    $tipoUsuario = $_SESSION['tipo_usuario']; // 'admin', 'gerente' ou 'comum'

    // Verifica a data do arranchamento e dono
    $sqlData = "SELECT data, id_usuario FROM arranchamento WHERE id = :id";
    $stmtData = $pdo->prepare($sqlData);
    $stmtData->bindParam(':id', $arranchamentoId);
    $stmtData->execute();
    $arranchamento = $stmtData->fetch(PDO::FETCH_ASSOC);

    if (!$arranchamento) {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Arranchamento não encontrado!',
                    confirmButtonColor: '#003366'
                });
              </script>";
        exit;
    }

    $dataArranchamento = strtotime($arranchamento['data']);
    $hoje = strtotime(date('Y-m-d'));

    // Se usuário comum e a data é passada → nega cancelamento
    if ($tipoUsuario === 'comum' && $dataArranchamento < $hoje) {
        echo "<script>
                Swal.fire({
                    icon: 'info',
                    title: 'Cancelamento não permitido',
                    text: 'Usuários comuns não podem cancelar arranchamentos passados.',
                    confirmButtonColor: '#003366'
                });
              </script>";
        exit;
    }

    // Admin ou gerente apenas marcam como cancelado
    if ($tipoUsuario === 'admin' || $tipoUsuario === 'gerente') {
        $sql = "UPDATE arranchamento SET status = 'cancelado' WHERE id = :id";
    } else {
        // Usuário comum (data válida)
        $sql = "DELETE FROM arranchamento WHERE id = :id";
    }

    $stmt = $pdo->prepare($sql);
    $stmt->bindParam(':id', $arranchamentoId);

    if ($stmt->execute()) {
        echo "<script>
                Swal.fire({
                    icon: 'success',
                    title: 'Arranchamento cancelado com sucesso!',
                    confirmButtonColor: '#003366'
                }).then(() => {
                    window.location.href = 'arranchar.php';
                });
              </script>";
    } else {
        echo "<script>
                Swal.fire({
                    icon: 'error',
                    title: 'Erro ao cancelar o arranchamento!',
                    confirmButtonColor: '#003366'
                });
              </script>";
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action']) && $_POST['action'] == 'agendar') {
    $userId = sanitize_input($_POST['user_id']);
    $data = sanitize_input($_POST['data']);
    $refeicoes = $_POST['refeicao'] ?? [];

    if (strtotime($data) < strtotime(date('Y-m-d'))) {
        echo "<script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Data inválida!',
                    text: 'Não é possível agendar para uma data anterior a hoje!',
                    confirmButtonColor: '#003366'
                });
              </script>";
        exit;
    }

    foreach ($refeicoes as $refeicao) {
        $refeicao = sanitize_input($refeicao);

        $sqlCheck = "SELECT COUNT(*) FROM arranchamento WHERE user_id = :user_id AND refeicao = :refeicao AND data = :data";
        $stmtCheck = $pdo->prepare($sqlCheck);
        $stmtCheck->bindParam(':user_id', $userId);
        $stmtCheck->bindParam(':refeicao', $refeicao);
        $stmtCheck->bindParam(':data', $data);
        $stmtCheck->execute();
        $count = $stmtCheck->fetchColumn();

        if ($count == 0) {
            $sql = "INSERT INTO arranchamento (user_id, refeicao, data) VALUES (:user_id, :refeicao, :data)";
            $stmt = $pdo->prepare($sql);
            $stmt->bindParam(':user_id', $userId);
            $stmt->bindParam(':refeicao', $refeicao);
            $stmt->bindParam(':data', $data);
            $stmt->execute();
        }
    }

    echo "<script>
            Swal.fire({
                icon: 'success',
                title: 'Arranchamento realizado com sucesso!',
                confirmButtonColor: '#003366'
            }).then(() => {
                window.location.href = 'arranchar.php';
            });
          </script>";
}

?>
