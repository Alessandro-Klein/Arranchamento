<?php
session_start();
include '../controller/db.php';
include '../includes/header.php';
include '../models/agendar_arranchamento.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Agendar Arranchamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="../css/agendar.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://kit.fontawesome.com/a2d9d5a4f5.js" crossorigin="anonymous"></script>
</head>
<body>
<div class="container py-4">
    <div class="card shadow rounded p-4">
        <h2 class="mb-4"><i class="fas fa-utensils"></i> Agendar Arranchamento</h2>

        <!-- Pesquisa -->
        <form method="GET" class="mb-4">
            <div class="input-group">
                <span class="input-group-text"><i class="fas fa-search"></i></span>
                <input type="text" class="form-control" name="pesquisa" value="<?= htmlspecialchars($pesquisa) ?>" placeholder="Pesquisar militar..." />
                <button class="btn btn-primary" type="submit">
                    <i class="fas fa-search"></i> Pesquisar
                </button>
            </div>
        </form>

        <!-- Agendar -->
        <form method="POST" class="mb-5">
            <input type="hidden" name="action" value="agendar">

      <!-- Campo de digitação e seleção do militar -->
<!-- Select2 CSS/JS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<script src="https://cdn.jsdelivr.net/npm/jquery@3.6.0/dist/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="mb-4">
    <label for="user_id" class="form-label fw-semibold">
        <i class="fas fa-user"></i> Militar
    </label>
    <select name="user_id" id="user_id" class="form-select" required style="width: 100%;">
        <option value="">Selecione um Militar</option>

        <?php
        $hierarquia = [
            "Oficiais Generais" => ["General de Exército", "General de Divisão", "General de Brigada"],
            "Oficiais Superiores" => ["Coronel", "Tenente-Coronel", "Major"],
            "Oficiais" => ["Capitão", "1º Tenente", "2º Tenente", "Aspirante"],
            "Praças" => ["Subtenente", "1º Sargento", "2º Sargento", "3º Sargento", "Cabo", "Soldado", "Soldado Recruta"]
        ];

        $porPosto = [];
        foreach ($usuarios as $u) {
            $porPosto[$u['posto']][] = $u;
        }

        foreach ($hierarquia as $grupo => $postos) {
            echo "<optgroup label=\"$grupo\">";
            foreach ($postos as $posto) {
                if (!isset($porPosto[$posto])) continue;
                foreach ($porPosto[$posto] as $u) {
                    echo '<option value="' . htmlspecialchars($u['id']) . '">' .
                        htmlspecialchars($u['nome']) . ' - ' . htmlspecialchars($posto) .
                        '</option>';
                }
            }
            echo "</optgroup>";
        }
        ?>
    </select>
</div>

<div class="mb-4">
    <label class="form-label fw-semibold"><i class="fas fa-utensils"></i> Refeições</label>
    <div class="dropdown-checkbox form-control" onclick="toggleDropdown()" id="checkboxDropdown">
        <div class="dropdown-label text-muted">Clique para selecionar as refeições</div>
        <div class="dropdown-options mt-2">
            <label><input type="checkbox" name="refeicao[]" value="Café da Manhã"> Café da Manhã</label>
            <label><input type="checkbox" name="refeicao[]" value="Almoço"> Almoço</label>
            <label><input type="checkbox" name="refeicao[]" value="Jantar"> Jantar</label>
        </div>
    </div>
</div>

<script>
    function toggleDropdown() {
        const dropdown = document.getElementById("checkboxDropdown");
        dropdown.classList.toggle("active");
    }

    // Fecha o dropdown ao clicar fora
    document.addEventListener("click", function (event) {
        const dropdown = document.getElementById("checkboxDropdown");
        if (!dropdown.contains(event.target)) {
            dropdown.classList.remove("active");
        }
    });
</script>


<script>
    function toggleDropdown() {
        document.getElementById('checkboxDropdown').classList.toggle('active');
    }
</script>

            <div class="mb-4">
                <label for="data" class="form-label"><i class="fas fa-calendar-day"></i> Data</label>
                <input type="date" name="data" id="data" class="form-control" min="<?= date('Y-m-d') ?>" required>

            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-calendar-plus"></i> Agendar Arranchamento
            </button>
        </form>

        <h3 class="mb-3">Arranchamentos Agendados</h3>
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>Data</th>
                        <th>Refeição</th>
                        <th>Militar</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
            if (!empty($pesquisa)) {
    $sqlArranchamentos = "
        SELECT a.*, u.nome, u.posto 
        FROM arranchamento a 
        JOIN users u ON a.user_id = u.id 
        WHERE a.data = CURDATE() AND u.nome LIKE :pesquisa 
        ORDER BY a.data DESC";
    $stmt = $pdo->prepare($sqlArranchamentos);
    $stmt->bindValue(':pesquisa', '%' . $pesquisa . '%');
    $stmt->execute();
} else {
    $sqlArranchamentos = "
        SELECT a.*, u.nome, u.posto 
        FROM arranchamento a 
        JOIN users u ON a.user_id = u.id 
        WHERE a.data = CURDATE() 
        ORDER BY a.data DESC";
    $stmt = $pdo->prepare($sqlArranchamentos);
    $stmt->execute();
}

$resultArranchamentos = $stmt;

while ($row = $resultArranchamentos->fetch(PDO::FETCH_ASSOC)) {
    $militarSql = "SELECT nome, posto FROM users WHERE id = :user_id";
    $stmt = $pdo->prepare($militarSql);
    $stmt->bindParam(':user_id', $row['user_id']);
    $stmt->execute();
    $militar = $stmt->fetch(PDO::FETCH_ASSOC);
    ?>
    <tr>
        <tr>
    <td><?= htmlspecialchars($row['data']) ?></td>
    <td><?= htmlspecialchars($row['refeicao']) ?></td>
    <td><?= htmlspecialchars($row['nome']) ?> - <?= htmlspecialchars($row['posto']) ?></td>
    <td>
        <form method="POST" class="cancelar-form" style="display:inline;">
            <input type="hidden" name="action" value="cancelar">
            <input type="hidden" name="arranchamento_id" value="<?= htmlspecialchars($row['id']) ?>">
            <button type="button" class="btn btn-danger btn-sm cancelar-btn">
                <i class="fas fa-times-circle"></i> Cancelar
            </button>
        </form>
    </td>
</tr>
<?php } ?>

                </tbody>
            </table>
        </div>
    </div>
</div>
<script>// Filtra a pesquisa de usuários do Arranchemnto
    $(document).ready(function () {
        $('#user_id').select2({
            placeholder: "Digite ou selecione o nome do militar",
            width: '100%',
            allowClear: true,
            dropdownAutoWidth: true
        });
    });
</script>

    <script>
        document.querySelectorAll('.cancelar-btn').forEach(button => {
            button.addEventListener('click', function () {
                const form = this.closest('form');

                Swal.fire({
                    title: 'Tem certeza?',
                    text: 'Deseja realmente cancelar este arranchamento?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'Sim, cancelar',
                    cancelButtonText: 'Não',
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    </script>
    <script>
    // Proteja com JavaScript extra para evitar voltar via botão “voltar”
    window.history.forward();
    window.onunload = function () { null };
</script>

</body>
</html>
