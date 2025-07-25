<?php
session_start();
include '../controller/db.php';
include '../includes/header.php';
include '../models/cardapio.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Cardápio do Dia</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <?php if ($tipo === 'admin' || $tipo === 'gerente'): ?>
        <!-- Bibliotecas para exportação -->
        <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <?php endif; ?>
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="mb-4 text-center text-primary"><i class="fa-solid fa-utensils"></i> Cardápio do Dia</h2>

    <form method="GET" class="mb-3 text-center">
        <label for="data">Selecionar data:</label>
        <input type="date" name="data" id="data" value="<?= htmlspecialchars($dataSelecionada) ?>">
        <button type="submit" class="btn btn-primary btn-sm"><i class="fa fa-search"></i> Ver Cardápio</button>
    </form>

    <div class="card">
        <div class="card-body">
            <p class="text-muted">Exibindo cardápio do dia <strong><?= date('d/m/Y', strtotime($dataSelecionada)) ?></strong></p>
            <form method="POST">
                <div id="tabelaCardapio" class="row">
                    <div class="col-md-4">
                        <h5 class="text-success">Café da Manhã</h5>
                        <?php if (($tipo == 'admin' || $tipo == 'gerente') && $dataSelecionada === date('Y-m-d')): ?>
                            <textarea name="cafe" class="form-control" rows="5"><?= htmlspecialchars($cardapio['cafe'] ?? '') ?></textarea>
                        <?php else: ?>
                            <p><?= nl2br(htmlspecialchars($cardapio['cafe'] ?? 'Não informado')) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <h5 class="text-warning">Almoço</h5>
                        <?php if (($tipo == 'admin' || $tipo == 'gerente') && $dataSelecionada === date('Y-m-d')): ?>
                            <textarea name="almoco" class="form-control" rows="5"><?= htmlspecialchars($cardapio['almoco'] ?? '') ?></textarea>
                        <?php else: ?>
                            <p><?= nl2br(htmlspecialchars($cardapio['almoco'] ?? 'Não informado')) ?></p>
                        <?php endif; ?>
                    </div>
                    <div class="col-md-4">
                        <h5 class="text-primary">Janta</h5>
                        <?php if (($tipo == 'admin' || $tipo == 'gerente') && $dataSelecionada === date('Y-m-d')): ?>
                            <textarea name="janta" class="form-control" rows="5"><?= htmlspecialchars($cardapio['janta'] ?? '') ?></textarea>
                        <?php else: ?>
                            <p><?= nl2br(htmlspecialchars($cardapio['janta'] ?? 'Não informado')) ?></p>
                        <?php endif; ?>
                    </div>
                </div>

                <?php if (($tipo == 'admin' || $tipo == 'gerente') && $dataSelecionada === date('Y-m-d')): ?>
                    <div class="mt-4 text-end">
                        <button type="submit" class="btn btn-success"><i class="fa-solid fa-save"></i> Atualizar Cardápio</button>
                    </div>
                <?php endif; ?>
            </form>
        </div>
    </div>

    <?php if ($tipo == 'admin' || $tipo == 'gerente'): ?>
        <div class="mt-4 text-end">
            <button onclick="exportarParaExcel()" class="btn btn-outline-success btn-sm me-2"><i class="fa-solid fa-file-excel"></i> Exportar Excel</button>
            <button onclick="exportarParaPDF()" class="btn btn-outline-danger btn-sm"><i class="fa-solid fa-file-pdf"></i> Exportar PDF</button>
        </div>
    <?php endif; ?>
</div>

<?php if ($tipo == 'admin' || $tipo == 'gerente'): ?>
<script>
function exportarParaExcel() {
    const data = [
        ["Café da Manhã", "<?= preg_replace('/\r\n|\n|\r/', ' ', $cardapio['cafe'] ?? 'Não informado') ?>"],
        ["Almoço", "<?= preg_replace('/\r\n|\n|\r/', ' ', $cardapio['almoco'] ?? 'Não informado') ?>"],
        ["Janta", "<?= preg_replace('/\r\n|\n|\r/', ' ', $cardapio['janta'] ?? 'Não informado') ?>"]
    ];
    const wb = XLSX.utils.book_new();
    const ws = XLSX.utils.aoa_to_sheet([["Refeição", "Descrição"], ...data]);
    XLSX.utils.book_append_sheet(wb, ws, "Cardápio");
    XLSX.writeFile(wb, 'cardapio_<?= $dataSelecionada ?>.xlsx');
}

  // PDF export function 
async function exportarParaPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Título e data
    doc.setFontSize(18);
    doc.text("Cardápio do Dia", 105, 20, { align: "center" });
    doc.setFontSize(12);
    doc.setTextColor(100);
    doc.text("Data: <?= date('d/m/Y', strtotime($dataSelecionada)) ?>", 105, 28, { align: "center" });

    // Blocos de refeições
    const bloco = (titulo, texto, y) => {
        doc.setDrawColor(0);
        doc.setFillColor(240);
        doc.rect(15, y, 180, 30, 'F');

        doc.setFontSize(14);
        doc.setTextColor(33, 37, 41);
        doc.text(titulo, 20, y + 8);

        doc.setFontSize(11);
        doc.setTextColor(50, 50, 50);
        doc.text(doc.splitTextToSize(texto, 170), 20, y + 16);
    };

    bloco(" Café da Manhã", `<?= str_replace(["\r\n", "\n"], " ", $cardapio['cafe'] ?? 'Não informado') ?>`, 40);
    bloco(" Almoço", `<?= str_replace(["\r\n", "\n"], " ", $cardapio['almoco'] ?? 'Não informado') ?>`, 80);
    bloco(" Janta", `<?= str_replace(["\r\n", "\n"], " ", $cardapio['janta'] ?? 'Não informado') ?>`, 120);

    // Assinaturas
    const yAssinaturas = 170;
    doc.setFontSize(12);
    doc.setTextColor(0);

    // Linhas
    doc.line(25, yAssinaturas, 85, yAssinaturas); // Responsável
    doc.line(125, yAssinaturas, 185, yAssinaturas); // Comandante

    // Nomes abaixo das linhas
    doc.setFontSize(10);
    doc.text("Responsável: <?= $posto . ' ' . $nome ?>", 25, yAssinaturas + 6);
    doc.text("Comandante: ", 125, yAssinaturas + 6); // Troque pelo nome real se quiser

    doc.save("cardapio_<?= $dataSelecionada ?>.pdf");
}
</script>


<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<?php if (isset($_SESSION['mensagem'])): ?>
<script>
    Swal.fire({
        title: 'Sucesso!',
        text: <?= json_encode($_SESSION['mensagem']) ?>,
        icon: 'success',
        timer: 3000,
        showConfirmButton: false,
        timerProgressBar: true
    });
</script>
<?php unset($_SESSION['mensagem']); endif; ?>

<?php endif; ?>
</body>
</html>
