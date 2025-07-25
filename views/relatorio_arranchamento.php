<?php
session_start();
include '../controller/db.php';
include '../includes/header.php';
include '../models/relatorio_arranchamento.php';
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Relatórios de Arranchamento</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <link href="../css/relatorio.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
  
</head>
<body>
<div class="container mt-5">
    <h2><i class="fa-solid fa-shield-halved"></i> Relatórios de Arranchamento</h2>
    <hr>

    <div class="d-flex justify-content-between align-items-center mb-2">
        <span><strong>Total de arranchados:</strong> <?= $totalArranchamentos ?></span>
        <div>
            <button onclick="exportarArranchamentoParaExcel()" class="btn btn-success btn-sm"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button onclick="exportarArranchamentosParaPDF()" class="btn btn-danger btn-sm"><i class="fa-solid fa-file-pdf"></i> PDF</button>
        </div>
    </div>

    <form method="GET" class="mb-3">
        <input type="date" name="data" value="<?= htmlspecialchars($filterData) ?>" class="form-control" style="max-width: 200px; display: inline-block;">
        <button type="submit" class="btn btn-primary">Filtrar</button>
    </form>

    <h4>Contagem por Refeição</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Refeição</th>
                <th>Quantidade</th>
            </tr>
        </thead>
        <tbody>
            <tr><td>Café da Manhã</td><td><?= $contagemRefeicoes['cafe'] ?></td></tr>
            <tr><td>Almoço</td><td><?= $contagemRefeicoes['almoco'] ?></td></tr>
            <tr><td>Janta</td><td><?= $contagemRefeicoes['jantar'] ?></td></tr>
            <tr><th>Total</th><th><?= $totalArranchamentos ?></th></tr>
        </tbody>
    </table>

    <input type="text" id="pesquisaArranchamento" class="form-control mb-2" placeholder="Pesquisar por militar...">

    <table class="table table-bordered table-hover" id="tabelaArranchamentos">
        <thead class="table-secondary">
            <tr>
                <th>Data</th>
                <th>Nome Completo</th>
                <th>Nome de Guerra</th>
                <th>Posto</th>
                <th>OM</th>
                <th>Refeições</th>
            </tr>
        </thead>
        <tbody>
        <?php foreach ($agendamentos as $data => $usuarios): ?>
            <?php foreach ($usuarios as $userId => $dados): ?>
                <tr>
                    <td><?= htmlspecialchars($data) ?></td>
                    <td><?= htmlspecialchars($dados['nome']) ?></td>
                    <td><?= htmlspecialchars($dados['nome_guerra']) ?></td>
                    <td><?= htmlspecialchars($dados['posto']) ?></td>
                    <td><?= htmlspecialchars($dados['om']) ?></td>
                    <td><?= implode(", ", array_map('ucfirst', array_unique($dados['refeicoes']))) ?></td>
                </tr>
            <?php endforeach; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
</div>

<script>
function calcularTotaisRefeicoes() {
    const linhas = document.querySelectorAll('#tabelaArranchamentos tbody tr');
    let total = 0, cafe = 0, almoco = 0, janta = 0;

    linhas.forEach(tr => {
        const celulaRefeicoes = tr.querySelector('td:nth-child(6)');
        if (!celulaRefeicoes) return;

        const refeicoes = celulaRefeicoes.textContent.toLowerCase().split(',');

        refeicoes.forEach(ref => {
            if (ref.includes('café') || ref.includes('cafe')) cafe++;
            else if (ref.includes('almoço') || ref.includes('almoco')) almoco++;
            else if (ref.includes('jantar') || ref.includes('jantar')) janta++;
        });

        total += refeicoes.length;
    });

    return { total, cafe, almoco, janta };
}



function gerarTabelaTotaisHTML(totais) {
    return `
        <table border="1" style="margin-top: 20px; border-collapse: collapse; width: 100%;">
            <thead style="background-color: #34495e; color: white;">
                <tr>
                    <th style="padding: 8px;">Refeição</th>
                    <th style="padding: 8px;">Total</th>
                </tr>
            </thead>
            <tbody>
                <tr><td style="padding: 8px;">Café da Manhã</td><td style="padding: 8px;">${totais.cafe}</td></tr>
                <tr><td style="padding: 8px;">Almoço</td><td style="padding: 8px;">${totais.almoco}</td></tr>
                <tr><td style="padding: 8px;">Janta</td><td style="padding: 8px;">${totais.janta}</td></tr>
                <tr style="font-weight: bold;"><td style="padding: 8px;">Total Geral</td><td style="padding: 8px;">${totais.total}</td></tr>
            </tbody>
        </table>
    `;
}

function exportarArranchamentoParaExcel() {
    const totais = calcularTotaisRefeicoes();

    // Tabela principal
    const tabela = document.getElementById("tabelaArranchamentos");
    const wb = XLSX.utils.book_new();
    const wsDados = XLSX.utils.table_to_sheet(tabela);
    XLSX.utils.book_append_sheet(wb, wsDados, "Arranchamentos");

    // Criar nova planilha para totais
    const dataTotais = [
        ["Refeição", "Total"],
        ["Café da Manhã", totais.cafe],
        ["Almoço", totais.almoco],
        ["Janta", totais.janta],
        ["Total Geral", totais.total]
    ];
    const wsTotais = XLSX.utils.aoa_to_sheet(dataTotais);
    XLSX.utils.book_append_sheet(wb, wsTotais, "Totais");

    // Salvar o arquivo
    XLSX.writeFile(wb, "arranchamentos.xlsx");
}

async function exportarArranchamentosParaPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    doc.setFontSize(16);
    doc.text("Relatório de Arranchamentos", 14, 15);

    // Tabela principal
    doc.autoTable({
        startY: 20,
        html: '#tabelaArranchamentos',
        theme: 'grid',
        headStyles: { fillColor: [52, 73, 94] },
        styles: { fontSize: 10 }
    });

    // Totais
    const totais = calcularTotaisRefeicoes();
    const finalY = doc.lastAutoTable.finalY + 10;

    doc.setFontSize(14);
    doc.text("Totais por Refeição:", 14, finalY);

    doc.autoTable({
        startY: finalY + 5,
        head: [['Refeição', 'Total']],
        body: [
            ['Café da Manhã', totais.cafe],
            ['Almoço', totais.almoco],
            ['Janta', totais.janta],
            ['Total Geral', totais.total]
        ],
        theme: 'grid',
        styles: { fontSize: 10 },
        headStyles: { fillColor: [52, 73, 94] }
    });

    doc.save("arranchamentos.pdf");
}

// Filtro de busca
$('#pesquisaArranchamento').on('keyup', function () {
    let value = $(this).val().toLowerCase();
    $('#tabelaArranchamentos tbody tr').filter(function () {
        $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
    });
});
</script>



<script>
    window.history.forward();
    window.onunload = function () { null };
</script>
</body>
</html>
