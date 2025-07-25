<?php
session_start();
include '../controller/db.php';
include '../includes/header.php';
include '../models/tiragem_faltas.php';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Tiragem de Faltas</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="../css/tiragem_faltas.css" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
</head>
<body>

<div class="container my-4">
    <h2 class="text-center mb-4">Tiragem de Faltas - <?= date('d/m/Y', strtotime($dataSelecionada)) ?></h2>

    <?php if (!empty($mensagemSucesso)): ?>
        <div class="alert alert-success text-center">
            <i class="bi bi-check-circle-fill"></i> <?= $mensagemSucesso ?>
        </div>
    <?php endif; ?>

    <form method="GET" class="form-section mb-4 row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Selecionar data:</label>
            <input type="date" name="data" value="<?= $dataSelecionada ?>" class="form-control">
        </div>
        <div class="col-md-8 d-flex gap-2">
            <button type="submit" class="btn btn-primary"><i class="bi bi-funnel-fill"></i> Filtrar</button>
            <button type="button" onclick="exportarParaExcel()" class="btn btn-success"><i class="bi bi-file-earmark-excel"></i> Exportar Excel</button>
            <button type="button" onclick="exportarParaPDF()" class="btn btn-danger"><i class="bi bi-file-earmark-pdf"></i> Exportar PDF</button>
        </div>
    </form>

    <div class="summary-box row mb-4">
        <div class="col-md-6 border-end">
            <h5><i class="bi bi-person-check-fill text-success"></i> Presenças:</h5>
            <ul class="list-unstyled">
                <li><strong>Café:</strong> <?= $contagemPresenca['Café da Manhã'] ?></li>
                <li><strong>Almoço:</strong> <?= $contagemPresenca['Almoço'] ?></li>
                <li><strong>Jantar:</strong> <?= $contagemPresenca['Jantar'] ?></li>
            </ul>
        </div>
        <div class="col-md-6">
            <h5><i class="bi bi-person-x-fill text-danger"></i> Faltas:</h5>
            <ul class="list-unstyled">
                <li><strong>Café:</strong> <?= $contagemFaltas['Café da Manhã'] ?></li>
                <li><strong>Almoço:</strong> <?= $contagemFaltas['Almoço'] ?></li>
                <li><strong>Jantar:</strong> <?= $contagemFaltas['Jantar'] ?></li>
            </ul>
        </div>
    </div>

    <input type="text" class="form-control mb-3" id="searchInput" placeholder="🔍 Buscar por nome ou posto...">

    <form method="POST" class="form-section">
        <div class="table-responsive">
            <table class="table table-bordered table-hover align-middle text-center">
                <thead>
                    <tr>
                        <th>Nome de Guerra</th>
                        <th>Posto</th>
                        <th colspan="2">Café</th>
                        <th colspan="2">Almoço</th>
                        <th colspan="2">Jantar</th>
                    </tr>
                    <tr>
                        <th colspan="2"></th>
                        <th><i class="bi bi-check-lg text-success"></i></th>
                        <th><i class="bi bi-x-lg text-danger"></i></th>
                        <th><i class="bi bi-check-lg text-success"></i></th>
                        <th><i class="bi bi-x-lg text-danger"></i></th>
                        <th><i class="bi bi-check-lg text-success"></i></th>
                        <th><i class="bi bi-x-lg text-danger"></i></th>
                    </tr>
                </thead>
                <tbody id="faltaTable">
                    <?php foreach ($usuarios as $user): ?>
                        <tr>
                            <td><?= htmlspecialchars($user['nome_guerra']) ?></td>
                            <td><?= htmlspecialchars($user['posto']) ?></td>
                            <?php foreach (['Café da Manhã', 'Almoço', 'Jantar'] as $ref): 
                                $r = $user['refeicoes'][$ref] ?? null;
                                if ($r): ?>
                                    <td>
                                        <input type="radio" name="presencas[<?= $r['id'] ?>]" value="1" <?= $r['presente'] ? 'checked' : '' ?> <?= !$dataEhHoje ? 'disabled' : '' ?>>
                                    </td>
                                    <td>
                                        <input type="radio" name="presencas[<?= $r['id'] ?>]" value="0" <?= $r['faltou'] ? 'checked' : '' ?> <?= !$dataEhHoje ? 'disabled' : '' ?>>
                                    </td>
                                <?php else: ?>
                                    <td colspan="2" class="text-muted">-</td>
                                <?php endif;
                            endforeach; ?>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <div class="text-center mt-4">
            <button type="submit" class="btn btn-success btn-lg px-5" <?= !$dataEhHoje ? 'disabled' : '' ?>>
                <i class="bi bi-save"></i> Salvar
            </button>
            <?php if (!$dataEhHoje): ?>
                <p class="text-danger mt-2">Você só pode registrar presenças/faltas para o dia atual.</p>
            <?php endif; ?>
        </div>
    </form>
</div>

<script>
    document.getElementById("searchInput").addEventListener("input", function () {
        const filter = this.value.toLowerCase();
        document.querySelectorAll("#faltaTable tr").forEach(row => {
            const nome = row.cells[0]?.textContent.toLowerCase() || "";
            const posto = row.cells[1]?.textContent.toLowerCase() || "";
            row.style.display = nome.includes(filter) || posto.includes(filter) ? "" : "none";
        });
    });

    async function exportarParaPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF({
        orientation: 'portrait',
        unit: 'mm',
        format: 'a4',
    });

    const margemEsquerda = 20;
    const larguraTotal = 170;

    const dataAtual = "<?= date('d/m/Y', strtotime($dataSelecionada)) ?>";

    // Título
    doc.setFont("helvetica", "bold");
    doc.setFontSize(18);
    doc.setTextColor(33, 37, 41); // cor do título
    doc.text("Relatório de Presenças e Faltas", 105, 20, null, null, "center");

    // Subtítulo/Data
    doc.setFont("helvetica", "normal");
    doc.setFontSize(12);
    doc.setTextColor(100);
    doc.text(`Data: ${dataAtual}`, 105, 28, null, null, "center");

    // Espaço entre título e tabela
    doc.setDrawColor(200);
    doc.line(margemEsquerda, 32, 210 - margemEsquerda, 32);

    const tableBody = [
        ["Café da Manhã", "<?= $contagemPresenca['Café da Manhã'] ?>", "<?= $contagemFaltas['Café da Manhã'] ?>"],
        ["Almoço", "<?= $contagemPresenca['Almoço'] ?>", "<?= $contagemFaltas['Almoço'] ?>"],
        ["Jantar", "<?= $contagemPresenca['Jantar'] ?>", "<?= $contagemFaltas['Jantar'] ?>"]
    ];

    // Tabela
    doc.autoTable({
        head: [["Refeição", "Presenças", "Faltas"]],
        body: tableBody,
        startY: 40,
        margin: { left: margemEsquerda, right: margemEsquerda },
        tableWidth: 'auto',
        styles: {
            fontSize: 11,
            cellPadding: 5,
            halign: 'center',
        },
        headStyles: {
            fillColor: [52, 58, 64], // dark bootstrap bg
            textColor: 255,
            fontStyle: 'bold',
        },
        alternateRowStyles: { fillColor: [245, 245, 245] }
    });

    // Rodapé
    const finalY = doc.lastAutoTable.finalY + 15;
    doc.setDrawColor(230);
    doc.line(margemEsquerda, finalY - 5, 210 - margemEsquerda, finalY - 5);
    doc.setFontSize(10);
    doc.setTextColor(150);
    doc.text("Gerado automaticamente pelo Sistema de Arranchamento", 105, finalY, null, null, "center");

    // Nome do arquivo
    doc.save(`resumo_arranchamento_${dataAtual.replace(/\//g, "-")}.pdf`);
}

</script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
</body>
</html>
