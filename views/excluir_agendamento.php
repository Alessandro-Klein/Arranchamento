<?php
$host = "localhost";
$usuario = "root";
$senha = "M@ster01";
$banco = "sistema_arranchamento";

$conn = new mysqli($host, $usuario, $senha, $banco);
if ($conn->connect_error) {
    die("Erro na conexão com o banco: " . $conn->connect_error);
}

if (isset($_GET['id']) && is_numeric($_GET['id'])) {
    $id = $_GET['id'];

    $stmt = $conn->prepare("DELETE FROM arranchamento WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();

    if ($stmt->affected_rows > 0) {
        echo "<script>alert('Agendamento removido com sucesso.'); window.location.href='arranchar.php';</script>";
    } else {
        echo "<script>alert('Erro ao remover.'); window.location.href='arranchar.php';</script>";
    }

    $stmt->close();
} else {
    echo "<script>alert('ID inválido.'); window.location.href='arranchar.php';</script>";
}

$conn->close();
?>
