<?php

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] !== 'admin') {
    header('Location: index.php');
    exit();
}

$msgSucesso = '';
$msgErro = '';

$nome = $nome_guerra = $posto = $email = $tipo = $om = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = trim($_POST['nome']);
    $nome_guerra = trim($_POST['nome_guerra']);
    $posto = trim($_POST['posto']);
    $email = trim($_POST['email']);
    $tipo = trim($_POST['tipo']);
    $om = trim($_POST['om']);
    $senhaDigitada = $_POST['senha'];

    // Validações
    if (strlen($nome) < 3) {
        $msgErro = 'O nome deve ter pelo menos 3 caracteres.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msgErro = 'E-mail inválido.';
    } elseif (strlen($senhaDigitada) < 6) {
        $msgErro = 'A senha deve ter no mínimo 6 caracteres.';
    } else {
        try {
            // Nome completo deve ser único
            $stmtNome = $pdo->prepare("SELECT id FROM users WHERE nome = :nome");
            $stmtNome->execute([':nome' => $nome]);
            if ($stmtNome->rowCount() > 0) {
                $msgErro = 'Este nome completo já está cadastrado.';
            } else {
                // Nome de guerra + posto não podem se repetir juntos
                $stmtGuerra = $pdo->prepare("SELECT id FROM users WHERE nome_guerra = :nome_guerra AND posto = :posto");
                $stmtGuerra->execute([':nome_guerra' => $nome_guerra, ':posto' => $posto]);
                if ($stmtGuerra->rowCount() > 0) {
                    $msgErro = 'Este nome de guerra já está cadastrado com esse posto.';
                } else {
                    // E-mail deve ser único
                    $stmtEmail = $pdo->prepare("SELECT id FROM users WHERE email = :email");
                    $stmtEmail->execute([':email' => $email]);
                    if ($stmtEmail->rowCount() > 0) {
                        $msgErro = 'Este e-mail já está cadastrado.';
                    } else {
                        // Tudo certo, cadastrar
                        $senhaHash = password_hash($senhaDigitada, PASSWORD_DEFAULT);
                        $stmt = $pdo->prepare("INSERT INTO users (nome, nome_guerra, email, senha, posto, tipo, om)
                                               VALUES (:nome, :nome_guerra, :email, :senha, :posto, :tipo, :om)");
                        $stmt->execute([
                            ':nome' => $nome,
                            ':nome_guerra' => $nome_guerra,
                            ':email' => $email,
                            ':senha' => $senhaHash,
                            ':posto' => $posto,
                            ':tipo' => $tipo,
                            ':om' => $om
                        ]);

                        $msgSucesso = 'Perfil cadastrado com sucesso!';
                        $nome = $nome_guerra = $posto = $email = $tipo = $om = '';
                    }
                }
            }
        } catch (PDOException $e) {
            error_log($e->getMessage());
            $msgErro = 'Erro ao cadastrar perfil.';
        }
    }
}
?>