<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);
include '../models/arranchar.php';
include '../includes/header.php';

// Conexão com banco
$conn = new mysqli("localhost", "root", "M@ster01", "sistema_arranchamento");
if ($conn->connect_error) {
    die("Erro na conexão com o banco: " . $conn->connect_error);
}
?>


<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Agendamento de Refeições</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/main.min.css' rel='stylesheet' />
    <link href='../css/select.css' rel='stylesheet' />
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script>
   
</head>
<body>

<?php if (isset($_SESSION['alert'])): ?>
<script>
Swal.fire({
    title: '<?= $_SESSION['alert']['title'] ?>',
    text: '<?= $_SESSION['alert']['message'] ?>',
    icon: '<?= $_SESSION['alert']['icon'] ?>'
});
</script>
<?php unset($_SESSION['alert']); endif; ?>

<div class="container">
    <div class="row">
        <!-- Formulário -->
        <div class="col-md-4">
            <div class="card shadow">
                <div class="card-header bg-primary text-white"><h5>Agendar Refeição</h5></div>
                <div class="card-body">
                    <form method="POST" action="arranchar.php">
                        <div class="mb-3">
                            <label for="data" class="form-label">Data:</label>
                          <input type="date" class="form-control" id="data" name="data" min="<?= date('Y-m-d', strtotime('+2 days')) ?>" required>
                        </div>
                        
                        <div class="mb-3">
  <label class="form-label fw-semibold">Refeições:</label>
  <div class="dropdown-checkbox" id="checkboxDropdown">
    <div class="dropdown-label" onclick="toggleDropdown()">Selecione as refeições</div>
    <div class="dropdown-options">
      <label><input type="checkbox" name="refeicao[]" value="Café da Manhã"> Café da Manhã</label>
      <label><input type="checkbox" name="refeicao[]" value="Almoço"> Almoço</label>
      <label><input type="checkbox" name="refeicao[]" value="Jantar"> Janta</label>
    </div>
  </div>
</div>
                        <button type="submit" class="btn btn-success w-100">Agendar</button>
                    </form>
                    <!-- Legenda -->
                    <div class="mt-3 alert alert-info">
    <strong>Legenda:</strong><br>
    • Para cancelar uma refeição agendada, clique sobre ela no calendário e confirme a exclusão.<br>
    • Só é permitido uma refeição por tipo por dia.<br>
    • Não é permitido arranchar às sextas, sábados e domingos (exceto guarnição de serviço).<br>
    • O horário limite é: 08:30 (Café), 10:30 (Almoço), 17:00 (Janta).<br>
    • O agendamento deve ser feito com no mínimo 2 dias de antecedência**.
</div>

                </div>
                <center><img src="../img/soldado04.jpg" width="165" alt="soldado" class="me-2"></center>
            </div>
        </div>

        <!-- Calendário -->
        <div class="col-md-8">
            <div class="card shadow">
                <div class="card-header bg-dark text-white"><h5>Calendário de Agendamentos</h5></div>
                <div class="card-body"><div id="calendar"></div></div>
            </div>
        </div>
    </div>
</div>

<!-- FullCalendar -->
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.11/index.global.min.js'></script>
<script>
document.addEventListener('DOMContentLoaded', function () {
    var calendarEl = document.getElementById('calendar');
    var calendar = new FullCalendar.Calendar(calendarEl, {
        locale: 'pt-br',
        initialView: 'dayGridMonth',
        headerToolbar: { left: 'prev,next today', center: 'title', right: '' },
        events: <?= json_encode($eventos) ?>,
        eventClick: function(info) {
            Swal.fire({
                title: 'Deseja desmarcar esta refeição?',
                text: info.event.title + ' em ' + info.event.start.toLocaleDateString(),
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'Sim, desmarcar',
                cancelButtonText: 'Cancelar'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'arranchar.php?delete_id=' + info.event.id;

                }
            });
        }
    });
    calendar.render();
});
</script>
<script>// Proteja com JavaScript extra para evitar voltar via botão “voltar”
    window.history.forward();
    window.onunload = function () { null };
</script>

<script>
  function toggleDropdown() {
    document.getElementById("checkboxDropdown").classList.toggle("active");
  }

  // Fecha dropdown ao clicar fora
  document.addEventListener("click", function(event) {
    const dropdown = document.getElementById("checkboxDropdown");
    if (!dropdown.contains(event.target)) {
      dropdown.classList.remove("active");
    }
  });
</script>



</body>
</html>
