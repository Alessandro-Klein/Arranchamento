<?php
ini_set('session.cookie_lifetime', 0); // Sessão expira ao fechar o navegador

session_start(); // Inicia a sessão
$msgErro = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    include 'controller/db.php';
    
    // Validação e sanitização das entradas
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $senha = $_POST['senha'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $msgErro = 'Email inválido!';
    } else {
        // Prepara a consulta para evitar SQL Injection
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        
        $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verifica se a senha bate com o hash da senha armazenado
        if ($usuario && password_verify($senha, $usuario['senha'])) {
            $_SESSION['user_id'] = $usuario['id'];
            $_SESSION['tipo'] = $usuario['tipo']; // Armazena o tipo do usuário

            // Redireciona conforme o tipo de usuário
            if ($usuario['tipo'] == 'admin') {
                header('Location: views/admin_dashboard.php');
            } elseif ($usuario['tipo'] == 'gerente') {
                header('Location: views/gerente_dashboard.php');
            } else {
                header('Location: views/usuario_dashboard.php');
            }
            exit();
        } else {
            $msgErro = 'Email ou senha inválidos!';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Login | Arranchamento Militar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href='css/login.css' rel='stylesheet' />
    <link rel="icon" href="img/user.png" type="image/png">
    
</head>
<body>
    <div class="login-box">
        <h2><img src="img/soldado02.jpg" width="92" alt="soldado" class="me-2"> Login Militar</h2>
        <?php if ($msgErro): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($msgErro, ENT_QUOTES, 'UTF-8') ?></div>
        <?php endif; ?>
        <form method="POST" action="index.php">
            <div class="mb-3">
                <label for="email" class="form-label">Email Militar</label>
                <input type="email" name="email" class="form-control" placeholder="Ex: soldado@exercito.gov" required>
            </div>
            <div class="mb-3">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" name="senha" class="form-control" placeholder="Digite sua senha" required>
            </div>
            <button type="submit" class="btn btn-primary w-100">
                <i class="fa-solid fa-sign-in-alt"></i> Entrar
            </button>
            <div class="text-center mt-4">
                <a href="views/cadastro.php" class="btn btn-outline-primary w-100 rounded-pill py-2 fw-bold shadow-sm" style="transition: all 0.3s;">
                    <i class="fa-solid fa-user-plus me-2"></i> Criar uma conta
                </a>
            </div>
        </form>
    </div>

    <div class="rodape">
        Desenvolvido por 
        <a href="https://www.linkedin.com/in/alessandro-klein-030a191a4?utm_source=share&utm_campaign=share_via&utm_content=profile&utm_medium=android_app" 
           target="_blank">Alessandro Klein</a> 
        | <a href="https://github.com/Alessandro-Klein" target="_blank">GitHub</a>
    </div>
    <script>// Proteja com JavaScript extra para evitar voltar via botão “voltar”
    window.history.forward();
    window.onunload = function () { null };
</script>

</body>
</html>
