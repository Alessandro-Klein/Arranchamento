<?php
session_start();
include '../controller/db.php';
include '../includes/header.php';
include '../models/admin_dashboard.php';

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Painel de Administração | Arranchamento Militar</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.17.0/xlsx.full.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
    <link href="../css/admin.css" rel="stylesheet">
</head>
<body>
    
<!-- Mostrar mensagem de boas-vindas apenas uma vez-->
    <?php if (!empty($mensagem_boas_vindas)): ?>
<script>
    Swal.fire({
        icon: 'success',
        title: '<?= $mensagem_boas_vindas ?>',
        showConfirmButton: false,
        timer: 3000
    });
</script>

<?php endif; ?>
<div class="container mt-5">
    <h2><i class="fa-solid fa-shield-halved"></i> Painel de Administração</h2>
    <hr>

    <div class="d-flex justify-content-between align-items-center">
        <h4><i class="fa-solid fa-users"></i> Usuários Cadastrados</h4>
        <div>
            <button onclick="exportarUsuariosParaExcel()" class="btn btn-success btn-sm"><i class="fa-solid fa-file-excel"></i> Excel</button>
            <button onclick="exportarUsuariosParaPDF()" class="btn btn-danger btn-sm"><i class="fa-solid fa-file-pdf"></i> PDF</button>
        </div>
    </div>

    <p class="mb-2">
        <span class="badge badge-admin">Admin: <?= $contagemPerfis['admin'] ?></span>
        <span class="badge badge-gerente">Gerente: <?= $contagemPerfis['gerente'] ?></span>
        <span class="badge badge-usuario">Usuário: <?= $contagemPerfis['usuario'] ?></span>
    </p>

    <input type="text" id="pesquisaUsuarios" class="form-control mt-2 mb-2" placeholder="Pesquisar usuários...">
    <table class="table table-bordered table-hover" id="tabelaUsuarios">
<thead class="table-dark">
    <tr>
        <th>Nome Completo</th>
        <th>Nome de Guerra</th> 
        <th>Email</th>
        <th>Posto</th>
        <th>OM</th>
        <th>Perfil</th>
        <th>Ações</th>
    </tr>
</thead>
<tbody>
<?php foreach ($users as $user): ?>
    <tr>
        <td><?= htmlspecialchars($user['nome']) ?></td>
        <td><?= htmlspecialchars($user['nome_guerra']) ?></td>
        <td><?= htmlspecialchars($user['email']) ?></td>
        <td><?= htmlspecialchars($user['posto']) ?></td>
        <td><?= htmlspecialchars($user['om']) ?></td>
        <td>
            <span class="badge <?= $user['tipo'] == 'admin' ? 'badge-admin' : ($user['tipo'] == 'gerente' ? 'badge-gerente' : 'badge-usuario') ?>">
                <?= ucfirst($user['tipo']) ?>
            </span>
        </td>
        <td>
            <a href="editar.php?id=<?= $user['id'] ?>" class="btn btn-primary btn-sm"><i class="fa-solid fa-pen-to-square"></i> Editar</a>
            <button class="btn btn-danger btn-sm" onclick="confirmarRemocao(<?= $user['id'] ?>)"><i class="fa-solid fa-trash"></i> Remover</button>
        </td>
    </tr>
<?php endforeach; ?>
</tbody>

    </table>
</div>

<script>
function confirmarRemocao(id) {
    Swal.fire({
        title: 'Tem certeza?',
        text: "Essa ação irá remover permanentemente o usuário!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'Sim, remover!',
        cancelButtonText: 'Cancelar'
    }).then((result) => {
        if (result.isConfirmed) {
            window.location.href = 'remover.php?id=' + id;
        }
    });
}

function exportarUsuariosParaExcel() {
    let wb = XLSX.utils.table_to_book(document.getElementById('tabelaUsuarios'), { sheet: "Usuários" });
    XLSX.writeFile(wb, 'usuarios.xlsx');
}

async function exportarUsuariosParaPDF() {
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();

    // Título do documento
    doc.setFontSize(16);
    doc.setTextColor(40);
    doc.text("Relatório de Usuários Cadastrados", 14, 15);

    // Gerar tabela automaticamente com base no HTML
    doc.autoTable({
        html: '#tabelaUsuarios',
        startY: 25,
        theme: 'striped',
        headStyles: {
            fillColor: [41, 128, 185], // Cor de cabeçalho: azul
            textColor: 255,
            fontStyle: 'bold'
        },
        styles: {
            fontSize: 9,
            cellPadding: 3,
        },
        didDrawPage: function (data) {
            // Rodapé com número da página
            const pageCount = doc.internal.getNumberOfPages();
            doc.setFontSize(10);
            doc.setTextColor(150);
            doc.text(`Página ${pageCount}`, data.settings.margin.left, doc.internal.pageSize.height - 10);
        }
    });

    // Nome do arquivo final
    doc.save("usuarios_arranchamento.pdf");
}
</script>
<script>// Proteja com JavaScript extra para evitar voltar via botão “voltar”
    window.history.forward();
    window.onunload = function () { null };
</script>


<script>//  pesquisa em tempo real por nome completo ou nome de guerra
    $(document).ready(function() {
        $('#pesquisaUsuarios').on('keyup', function() {
            const termo = $(this).val().toLowerCase();

            $('#tabelaUsuarios tbody tr').each(function() {
                const nomeCompleto = $(this).find('td:nth-child(1)').text().toLowerCase();
                const nomeGuerra = $(this).find('td:nth-child(2)').text().toLowerCase();

                if (nomeCompleto.includes(termo) || nomeGuerra.includes(termo)) {
                    $(this).show();
                } else {
                    $(this).hide();
                }
            });
        });
    });
</script>


</body>
</html>
