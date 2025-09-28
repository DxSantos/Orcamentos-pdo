<?php
require __DIR__ . '/../../config.php';
header('Content-Type: application/json; charset=utf-8');
$rows = $pdo->query("SELECT * FROM fornecedores ORDER BY nome_fantasia")->fetchAll();
echo json_encode($rows, JSON_UNESCAPED_UNICODE);
