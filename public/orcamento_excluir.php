<?php
require __DIR__ . '/../config.php';

$id = intval($_GET['id'] ?? 0);

if ($id > 0) {
    try {
        // Exclui os itens primeiro (FK)
        $st = $pdo->prepare("DELETE FROM orcamento_itens WHERE orcamento_id=?");
        $st->execute([$id]);

        // Exclui o orçamento
        $st = $pdo->prepare("DELETE FROM orcamentos WHERE id=?");
        $st->execute([$id]);

        header("Location: orcamentos.php?msg=excluido");
        exit;
    } catch (Exception $e) {
        die("Erro ao excluir: " . $e->getMessage());
    }
} else {
    header("Location: orcamentos.php");
    exit;
}
