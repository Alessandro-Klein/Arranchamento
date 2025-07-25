<?php 
$msgSucesso = '';
$msgErro = '';

// Inicializa as variáveis
$nome = $nome_guerra = $email = $senha = $posto = $om = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nome = trim($_POST['nome']);
    $nome_guerra = trim($_POST['nome_guerra']);
    $email = trim($_POST['email']);
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $posto = $_POST['posto'];
    $om = $_POST['om'];
    $tipo = 'usuario';

    try {
        $verifica = $pdo->prepare("SELECT COUNT(*) FROM users WHERE nome = :nome OR email = :email");
        $verifica->bindParam(':nome', $nome);
        $verifica->bindParam(':email', $email);
        $verifica->execute();
        $existe = $verifica->fetchColumn();

        if ($existe > 0) {
            $msgErro = 'Nome ou e-mail já estão cadastrados!';
        } else {
            $stmt = $pdo->prepare("INSERT INTO users (nome, nome_guerra, email, senha, posto, om, tipo) 
                                   VALUES (:nome, :nome_guerra, :email, :senha, :posto, :om, :tipo)");
            $stmt->bindParam(':nome', $nome);
            $stmt->bindParam(':nome_guerra', $nome_guerra);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':senha', $senha);
            $stmt->bindParam(':posto', $posto);
            $stmt->bindParam(':om', $om);
            $stmt->bindParam(':tipo', $tipo);
            $stmt->execute();
            $msgSucesso = 'Usuário cadastrado com sucesso!';

            // Limpa os campos após sucesso
            $nome = $nome_guerra = $email = $posto = $om = '';
        }
    } catch (PDOException $e) {
        $msgErro = 'Erro ao cadastrar usuário: ' . $e->getMessage();
    }
}

?>