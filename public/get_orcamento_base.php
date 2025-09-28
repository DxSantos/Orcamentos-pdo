<?php
require __DIR__ . '/../config.php';

$fornecedor_id = isset($_GET['fornecedor_id']) ? (int) $_GET['fornecedor_id'] : 0;
if(!$fornecedor_id) exit(json_encode([]));

$stmt = $pdo->prepare("SELECT produto_id FROM orcamento_base WHERE fornecedor_id = ?");
$stmt->execute([$fornecedor_id]);
echo json_encode($stmt->fetchAll(PDO::FETCH_ASSOC));
