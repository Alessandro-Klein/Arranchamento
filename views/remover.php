<?php
session_start();
include '../controller/db.php';

// Verifica se o usuário é administrador
if (!isset($_SESSION['tipo']) || $_SESSION['tipo'] != 'admin') {
    header("Location: index.php");
    exit();
}

// Verifica se o ID foi passado via GET
if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = intval($_GET['id']);

    // Impede o admin de remover a si mesmo
    if (isset($_SESSION['id']) && $_SESSION['id'] == $id) {
        echo "<!DOCTYPE html>
        <html lang='pt-br'>
        <head>
            <meta charset='UTF-8'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'warning',
                    title: 'Ação não permitida',
                    text: 'Você não pode remover a si mesmo!',
                    confirmButtonText: 'OK'
                }).then(() => {
                    window.location.href = 'admin_dashboard.php';
                });
            </script>
        </body>
        </html>";
        exit();
    }

    // Tenta executar a exclusão
    try {
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        echo "<!DOCTYPE html>
        <html lang='pt-br'>
        <head>
            <meta charset='UTF-8'>
            <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
        </head>
        <body>
            <script>
                Swal.fire({
                    icon: 'success',
                    title: 'Usuário removido!',
                    text: 'O usuário foi removido com sucesso.',
                    timer: 2000,
                    showConfirmButton: false
                }).then(() => {
                    window.location.href = 'admin_dashboard.php';
                });
            </script>
        </body>
        </html>";
    } catch (PDOException $e) {
        // Verifica se é erro de integridade referencial (chave estrangeira)
        if ($e->getCode() == '23000') {
            echo "<!DOCTYPE html>
            <html lang='pt-br'>
            <head>
                <meta charset='UTF-8'>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            </head>
            <body>
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Não foi possível remover!',
                        text: 'Este usuário possui arranchamentos registrados e não pode ser excluído.',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'admin_dashboard.php';
                    });
                </script>
            </body>
            </html>";
        } else {
            // Outro erro qualquer
            echo "<!DOCTYPE html>
            <html lang='pt-br'>
            <head>
                <meta charset='UTF-8'>
                <script src='https://cdn.jsdelivr.net/npm/sweetalert2@11'></script>
            </head>
            <body>
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Erro!',
                        text: 'Erro ao remover o usuário: " . $e->getMessage() . "',
                        confirmButtonText: 'OK'
                    }).then(() => {
                        window.location.href = 'admin_dashboard.php';
                    });
                </script>
            </body>
            </html>";
        }
    }

} else {
    // ID inválido ou ausente
    header("Location: admin_dashboard.php");
    exit();
}
?>
