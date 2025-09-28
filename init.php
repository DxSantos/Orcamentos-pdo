<?php
// init.php
require_once __DIR__ . '/config.php';

echo "<h3>Conectando ao banco...</h3>";

try {
    // Testa a conexão
    $stmt = $pdo->query("SELECT DATABASE() as db");
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    echo "✅ Conexão realizada com sucesso!<br>";
    echo "Banco em uso: <b>" . $row['db'] . "</b>";
} catch (PDOException $e) {
    die("❌ Erro ao conectar no banco: " . $e->getMessage());
}
