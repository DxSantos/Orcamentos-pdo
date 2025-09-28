<?php
require __DIR__ . '/../config.php';
$fid = (int)($_GET['fornecedor_id'] ?? 0);

$st = $pdo->prepare("
    SELECT p.id as materiaPri_id, p.nome, p.preco_unitario, p.unidade_medida 
    FROM orcamento_base ob
    JOIN materiaPri p ON p.id=ob.materiaPri_id
    WHERE ob.fornecedor_id=?
");
$st->execute([$fid]);
echo json_encode($st->fetchAll(PDO::FETCH_ASSOC));
