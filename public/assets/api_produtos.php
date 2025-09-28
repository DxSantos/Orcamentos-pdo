<?php
require __DIR__ . '/../../config.php';
header('Content-Type: application/json; charset=utf-8');
$rows = $pdo->query("SELECT id, nome, preco_unitario, unidade_medida FROM produtos ORDER BY nome")->fetchAll();
echo json_encode($rows, JSON_UNESCAPED_UNICODE);
