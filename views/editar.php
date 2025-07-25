<?php
session_start();
include '../controller/db.php';
include '../models/editar.php';

?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/edit.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <div class="card shadow-lg">
        <div class="card-header bg-primary text-white">
            <h4><i class="fas fa-user-edit"></i> Editar Perfil do Usuário</h4>
        </div>
        <div class="card-body">
            <?php if (isset($erro)): ?>
                <div class="alert alert-danger"><?= $erro ?></div>
            <?php endif; ?>

            <form method="post">
                <div class="mb-3">
                    <label class="form-label">Nome Completo</label>
                    <input type="text" name="nome" class="form-control" value="<?= htmlspecialchars($user['nome']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">Nome de Guerra</label>
                    <input type="text" name="nome_guerra" class="form-control" value="<?= htmlspecialchars($user['nome_guerra']) ?>" required>
                </div>

                <div class="mb-3">
                    <label class="form-label">E-mail</label>
                    <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <!-- NOVO: Posto/Graduação -->
                <div class="mb-3">
                    <label for="posto" class="form-label">Posto/Graduação</label>
                    <select name="posto" class="form-select" required>
                        <option value="">Selecione seu posto</option>

                        <optgroup label="Oficiais Generais">
                            <option <?= $user['posto'] == 'General de Exército' ? 'selected' : '' ?>>General de Exército</option>
                            <option <?= $user['posto'] == 'General de Divisão' ? 'selected' : '' ?>>General de Divisão</option>
                            <option <?= $user['posto'] == 'General de Brigada' ? 'selected' : '' ?>>General de Brigada</option>
                        </optgroup>

                        <optgroup label="Oficiais Superiores">
                            <option <?= $user['posto'] == 'Coronel' ? 'selected' : '' ?>>Coronel</option>
                            <option <?= $user['posto'] == 'Tenente-Coronel' ? 'selected' : '' ?>>Tenente-Coronel</option>
                            <option <?= $user['posto'] == 'Major' ? 'selected' : '' ?>>Major</option>
                        </optgroup>

                        <optgroup label="Oficiais">
                            <option <?= $user['posto'] == 'Capitão' ? 'selected' : '' ?>>Capitão</option>
                            <option <?= $user['posto'] == '1º Tenente' ? 'selected' : '' ?>>1º Tenente</option>
                            <option <?= $user['posto'] == '2º Tenente' ? 'selected' : '' ?>>2º Tenente</option>
                            <option <?= $user['posto'] == 'Aspirante' ? 'selected' : '' ?>>Aspirante</option>
                        </optgroup>

                        <optgroup label="Praças">
                            <option <?= $user['posto'] == 'Subtenente' ? 'selected' : '' ?>>Subtenente</option>
                            <option <?= $user['posto'] == '1º Sargento' ? 'selected' : '' ?>>1º Sargento</option>
                            <option <?= $user['posto'] == '2º Sargento' ? 'selected' : '' ?>>2º Sargento</option>
                            <option <?= $user['posto'] == '3º Sargento' ? 'selected' : '' ?>>3º Sargento</option>
                            <option <?= $user['posto'] == 'Cabo' ? 'selected' : '' ?>>Cabo</option>
                            <option <?= $user['posto'] == 'Soldado' ? 'selected' : '' ?>>Soldado</option>
                            <option <?= $user['posto'] == 'Soldado Recruta' ? 'selected' : '' ?>>Soldado Recruta</option>
                             <option <?= $user['posto'] == 'Servidor Civil' ? 'selected' : '' ?>>Servidor Civil</option>
                        </optgroup>
                    </select>
                </div>

                <!-- NOVO: OM -->
                <div class="mb-3">
                    <label for="om" class="form-label">OM (Organização Militar)</label>
                    <select name="om" class="form-select" required>
                        <option value="">Selecione sua OM</option>
                        <option <?= $user['om'] == 'CMS' ? 'selected' : '' ?>>CMS</option>
                        <option <?= $user['om'] == 'Cmd 3ªRM' ? 'selected' : '' ?>>Cmd 3ªRM</option>
                        <option <?= $user['om'] == 'B Adm Ap3ªRM' ? 'selected' : '' ?>>B Adm Ap3ªRM</option>
                        <option <?= $user['om'] == '1ºCTA' ? 'selected' : '' ?>>1ºCTA</option>
                        <option <?= $user['om'] == '3ºGPTLOG' ? 'selected' : '' ?>>3ºGPTLOG</option>
                        <option <?= $user['om'] == '3ºCRO' ? 'selected' : '' ?>>3ºCRO</option>
                        <option <?= $user['om'] == '3ªRCG' ? 'selected' : '' ?>>3ªRCG</option>
                        <option <?= $user['om'] == '3ºGPTE' ? 'selected' : '' ?>>3ºGPTE</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Tipo de Acesso</label>
                    <select name="tipo" class="form-select" required>
                        <option value="usuario" <?= $user['tipo'] == 'usuario' ? 'selected' : '' ?>>Usuário</option>
                        <option value="gerente" <?= $user['tipo'] == 'gerente' ? 'selected' : '' ?>>Gerente</option>
                        <option value="admin" <?= $user['tipo'] == 'admin' ? 'selected' : '' ?>>Administrador</option>
                    </select>
                </div>

                <div class="mb-3">
                    <label class="form-label">Senha (preencha apenas se quiser alterar)</label>
                    <input type="password" name="senha" class="form-control" placeholder="Nova senha (opcional)">
                </div>

                <div class="d-flex justify-content-between">
                    <a href="admin_dashboard.php" class="btn btn-secondary"><i class="fas fa-arrow-left"></i> Voltar</a>
                    <button type="button" class="btn btn-success" onclick="confirmarEdicao()">
                        <i class="fas fa-save me-2"></i>Salvar Alterações
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
function confirmarEdicao() {
    Swal.fire({
        title: 'Tem certeza?',
        text: "Deseja salvar as alterações deste usuário?",
        icon: 'question',
        showCancelButton: true,
        confirmButtonColor: '#198754',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Sim, salvar!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            document.querySelector('form').submit();
        }
    });
}
</script>

<?php if (isset($_GET['editado']) && $_GET['editado'] == 1): ?>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        Swal.fire({
            icon: 'success',
            title: 'Sucesso!',
            text: 'Alterações salvas com sucesso!',
            confirmButtonText: 'Fechar',
            customClass: {
                confirmButton: 'btn btn-success'
            },
            buttonsStyling: false,
            timer: 3000
        });
    </script>
<?php endif; ?>




<script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/js/all.min.js"></script>
</body>
</html>
