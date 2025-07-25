<?php
$host = 'localhost';
$dbname = 'sistema_arranchamento';
$username = 'root'; // Altere se necessário
$password = 'M@ster01'; // Altere se necessário

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo 'Erro de conexão: ' . $e->getMessage();
}
?>