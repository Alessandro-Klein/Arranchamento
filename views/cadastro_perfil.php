<?php
session_start();
include '../includes/header.php';
include '../controller/db.php';
include '../models/cadastro_perfil.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cadastro de Perfil</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css">
</head>
<body>

<div class="content">
    <div class="container">
        <div class="card shadow-sm p-5 rounded" style="max-width: 700px; margin: 0 auto;">
            <center><img src="../img/soldado03.jpg" width="70" alt="soldado" class="me-2"></center>
<h2 class="mb-2 text-primary text-center">Cadastro de Novo Perfil</h2>
            <form method="POST" action="cadastro_perfil.php">
                <div class="mb-3">
                    <label for="nome" class="form-label"><i class="fa fa-user me-2"></i> Nome Completo</label>
                    <input type="text" name="nome" class="form-control" required value="<?= htmlspecialchars($nome) ?>">
                </div>

                <div class="mb-3">
                    <label for="nome_guerra" class="form-label"><i class="fa fa-user-shield me-2"></i> Nome de Guerra</label>
                    <input type="text" name="nome_guerra" class="form-control" required value="<?= htmlspecialchars($nome_guerra) ?>">
                </div>

                <div class="mb-3">
                    <label for="posto" class="form-label"><i class="fa fa-cogs me-2"></i> Posto/Graduação</label>
                    <select name="posto" class="form-select border-primary shadow-sm" required>
                        <option value="">Selecione seu posto</option>
                        <?php
                        $postos = [
                            'Oficiais Generais' => ['General de Exército', 'General de Divisão', 'General de Brigada'],
                            'Oficiais Superiores' => ['Coronel', 'Tenente-Coronel', 'Major'],
                            'Oficiais' => ['Capitão', '1º Tenente', '2º Tenente', 'Aspirante'],
                            'Praças' => ['Subtenente', '1º Sargento', '2º Sargento', '3º Sargento', 'Cabo', 'Soldado', 'Soldado Recruta', 'Servidor Civil']
                        ];
                        foreach ($postos as $grupo => $lista) {
                            echo "<optgroup label=\"$grupo\">";
                            foreach ($lista as $p) {
                                $selected = $posto === $p ? 'selected' : '';
                                echo "<option value=\"$p\" $selected>$p</option>";
                            }
                            echo "</optgroup>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="email" class="form-label"><i class="fa fa-envelope me-2"></i> Email Militar</label>
                    <input type="email" name="email" class="form-control" required value="<?= htmlspecialchars($email) ?>">
                </div>

                <div class="mb-3">
                    <label for="tipo" class="form-label"><i class="fa fa-user-tag me-2"></i> Tipo de Perfil</label>
                    <select name="tipo" class="form-select" required>
                        <option value="">Selecione o tipo</option>
                        <option value="administrador" <?= $tipo === 'administrador' ? 'selected' : '' ?>>Administrador</option>
                        <option value="gerente" <?= $tipo === 'gerente' ? 'selected' : '' ?>>Gerente</option>
                        <option value="usuario" <?= $tipo === 'usuario' ? 'selected' : '' ?>>Usuário</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="om" class="form-label">OM (Organização Militar)</label>
                    <select name="om" class="form-select" required>
                        <option value="">Selecione sua OM</option>
                        <?php
                        $oms = ['CMS', 'Cmd 3ªRM', 'B Adm Ap3ªRM', '1ºCTA', '3ºGPTLOG', '3ºCRO', '3ªRCG', '3ºGPTE'];
                        foreach ($oms as $o) {
                            $selected = $om === $o ? 'selected' : '';
                            echo "<option value=\"$o\" $selected>$o</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="senha" class="form-label">Senha</label>
                    <input type="password" name="senha" class="form-control" placeholder="Crie uma senha segura" required>
                </div>

                <button type="submit" class="btn btn-primary w-100">
                    <i class="fa fa-check-circle me-2"></i> Cadastrar Perfil
                </button>
            </form>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if ($msgSucesso): ?>
    <script>
        Swal.fire({ icon: 'success', title: 'Sucesso!', text: '<?= $msgSucesso ?>', confirmButtonText: 'OK' });
    </script>
<?php elseif ($msgErro): ?>
    <script>
        Swal.fire({ icon: 'error', title: 'Erro!', text: '<?= $msgErro ?>', confirmButtonText: 'OK' });
    </script>
<?php endif; ?>

<script>
    window.history.forward();
    window.onunload = function () { null };
</script>
</body>
</html>
