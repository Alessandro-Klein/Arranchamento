<?php

if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 'admin') {
    header("Location: index.php");
    exit();
}
if (isset($_SESSION['mensagem_sucesso'])) {
    echo '
    <div id="mensagemSucesso" style="background: #d4edda; color: #155724; padding: 10px; border-radius: 5px; margin-bottom: 20px;">
        ' . $_SESSION['mensagem_sucesso'] . '
    </div>
    <script>
        setTimeout(function() {
            var msg = document.getElementById("mensagemSucesso");
            if (msg) {
                msg.style.transition = "opacity 0.5s ease";
                msg.style.opacity = 0;
                setTimeout(function() {
                    msg.remove();
                }, 500); // Remove após fade-out
            }
        }, 3000); // 3 segundos
    </script>
    ';
    unset($_SESSION['mensagem_sucesso']);
}

// Mostrar mensagem de boas-vindas apenas uma vez
$mensagem_boas_vindas = "";
if (!isset($_SESSION['boas_vindas_exibida'])) {
    $_SESSION['boas_vindas_exibida'] = true;
    $nomeGuerra = $_SESSION['nome_guerra'] ?? 'Administrador';
    $mensagem_boas_vindas = "Bem-vindo, $nomeGuerra!";
}

$users = [];
$sql = "SELECT * FROM users";
$result = $pdo->query($sql);
while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
    $users[] = $row;
}

// Contagem de perfis
$contagemPerfis = [
    'admin' => 0,
    'gerente' => 0,
    'usuario' => 0
];

foreach ($users as $user) {
    $tipo = $user['tipo'];
    if (isset($contagemPerfis[$tipo])) {
        $contagemPerfis[$tipo]++;
    }
}
?>