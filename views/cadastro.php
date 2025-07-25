<?php
include '../controller/db.php';
include '../models/cadastro.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro | Arranchamento Militar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="../css/cadastro.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  
</head>
<body>

<div class="cadastro-box">
    <h2><img src="../img/soldado03.jpg" width="70" alt="soldado" class="me-2"> Cadastro Militar</h2>

    <?php if ($msgErro): ?>
        <div class="alert alert-danger"><?= $msgErro ?></div>
    <?php endif; ?>

    <?php if ($msgSucesso): ?>
        <script>
            Swal.fire({
                icon: 'success',
                title: 'Cadastro Realizado!',
                text: '<?= $msgSucesso ?>',
                confirmButtonText: 'Fechar',
                customClass: {
                    confirmButton: 'btn btn-success'
                },
                buttonsStyling: false,
                timer: 3500
            });
        </script>
    <?php endif; ?>

    <form method="POST" action="<?= htmlspecialchars($_SERVER['PHP_SELF']) ?>">
        <div class="mb-3">
            <label class="form-label">Nome Completo</label>
            <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($nome) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Nome de Guerra</label>
            <input type="text" name="nome_guerra" class="form-control" value="<?= htmlspecialchars($nome_guerra) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">OM</label>
            <select name="om" class="form-select" required>
                <option value="">Selecione sua OM</option>
                <?php
                $opcoes_om = ['CMS', 'Cmd 3ªRM', 'B Adm Ap3ªRM', '1ºCTA', '3ºGPTLOG', '3ºCRO', '3ªRCG', '3ºGPTE'];
                foreach ($opcoes_om as $item) {
                    $selected = ($item == $om) ? 'selected' : '';
                    echo "<option $selected>$item</option>";
                }
                ?>
            </select>
        </div>

      <div class="mb-3">
    <label class="form-label">Posto/Graduação</label>
    <select name="posto" class="form-select" required>
        <option value="">Selecione seu posto</option>
        <?php
        $postos = [
            "Oficiais Generais" => ['General de Exército', 'General de Divisão', 'General de Brigada'],
            "Oficiais Superiores" => ['Coronel', 'Tenente-Coronel', 'Major'],
            "Oficiais" => ['Capitão', '1º Tenente', '2º Tenente', 'Aspirante'],
            "Praças" => [
                'Subtenente', '1º Sargento', '2º Sargento', '3º Sargento',
                'Cabo', 'Soldado', 'Soldado Recruta', 'Servidor Civil'
            ]
        ];
        foreach ($postos as $grupo => $lista) {
            echo "<optgroup label=\"$grupo\">";
            foreach ($lista as $p) {
                $selected = ($p == $posto) ? 'selected' : '';
                echo "<option value=\"$p\" $selected>$p</option>";
            }
            echo "</optgroup>";
        }
        ?>
    </select>
</div>


        <div class="mb-3">
            <label class="form-label">Email Militar</label>
            <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
        </div>

        <div class="mb-3">
            <label class="form-label">Senha</label>
            <input type="password" name="senha" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-success w-100">
            <i class="fa-solid fa-user-plus"></i> Cadastrar
        </button>

        <div class="text-center mt-4">
            <a href="../index.php" class="btn btn-outline-primary w-100 rounded-pill py-2 fw-bold shadow-sm">
                <i class="fa-solid fa-right-to-bracket me-2"></i> Voltar à Tela de Login
            </a>
        </div>
    </form>
</div>

</body>
</html>
