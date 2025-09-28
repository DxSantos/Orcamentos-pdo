<?php
require __DIR__ . '/../config.php';

$q = trim($_GET['q'] ?? '');
$status = trim($_GET['status'] ?? '');
$data_inicio = trim($_GET['data_inicio'] ?? '');
$data_fim = trim($_GET['data_fim'] ?? '');
$produtos = trim($_GET['produtos'] ?? '');

$sql = "SELECT o.id, o.data_orcamento, o.status,
               f.nome_fantasia, COALESCE(SUM(oi.total),0) AS total,
               GROUP_CONCAT(DISTINCT p.nome ORDER BY p.nome SEPARATOR ', ') AS produtos
        FROM orcamentos o
        JOIN fornecedores f ON f.id=o.fornecedor_id
        LEFT JOIN orcamento_itens oi ON oi.orcamento_id=o.id
        LEFT JOIN produtos p ON p.id=oi.produto_id
        WHERE 1=1";
$params = [];

// filtro fornecedor
if ($q !== '') { 
    $sql .= " AND f.nome_fantasia LIKE ?"; 
    $params[] = "%$q%"; 
}

// filtro status
if ($status !== '') {
    $sql .= " AND o.status = ?";
    $params[] = $status;
}

// filtro intervalo de datas
if ($data_inicio !== '') {
    $sql .= " AND date(o.data_orcamento) >= ?";
    $params[] = $data_inicio;
}
if ($data_fim !== '') {
    $sql .= " AND date(o.data_orcamento) <= ?";
    $params[] = $data_fim;
}

// filtro produtos (múltiplos separados por vírgula)
if ($produtos !== '') {
    $lista = array_filter(array_map('trim', explode(',', $produtos)));
    if ($lista) {
        $condicoes = [];
        foreach ($lista as $p) {
            $condicoes[] = "p.nome LIKE ?";
            $params[] = "%$p%";
        }
        $sql .= " AND (" . implode(" OR ", $condicoes) . ")";
    }
}

$sql .= " GROUP BY o.id ORDER BY o.data_orcamento DESC";

$st = $pdo->prepare($sql);
$st->execute($params);
$rows = $st->fetchAll();

require '_header.php';
?>

<h3>Buscar/Editar Orçamentos</h3>

<form class="row gy-2 gx-2 align-items-end mb-3">
  <div class="col-md-3">
    <label class="form-label">Fornecedor</label>
    <input name="q" value="<?php echo htmlspecialchars($q); ?>" class="form-control" placeholder="Nome fantasia">
  </div>

  <div class="col-md-2">
    <label class="form-label">Status</label>
    <select name="status" class="form-select">
      <option value="">Todos</option>
      <option value="Em Andamento" <?php if($status==='Em Andamento') echo 'selected'; ?>>Em andamento</option>
      <option value="Finalizado" <?php if($status==='Finalizado') echo 'selected'; ?>>Finalizado</option>
    </select>
  </div>

  <div class="col-md-2">
    <label class="form-label">Data Início</label>
    <input type="date" name="data_inicio" value="<?php echo htmlspecialchars($data_inicio); ?>" class="form-control">
  </div>

  <div class="col-md-2">
    <label class="form-label">Data Fim</label>
    <input type="date" name="data_fim" value="<?php echo htmlspecialchars($data_fim); ?>" class="form-control">
  </div>

  <div class="col-md-3">
    <label class="form-label">Produtos (separar por vírgula)</label>
    <input name="produtos" value="<?php echo htmlspecialchars($produtos); ?>" class="form-control" placeholder="Ex: Parafuso, Prego">
  </div>

  <div class="col-12 mt-3">
    <button class="btn btn-primary">Pesquisar</button>
    <a class="btn btn-outline-secondary" href="orcamentos.php">Limpar</a>
  </div>
</form>

<?php if ($q !== '' || $status !== '' || $data_inicio !== '' || $data_fim !== '' || $produtos !== ''): ?>
  <?php if (empty($rows)): ?>
    <div class="alert alert-warning">Nenhum orçamento encontrado com os filtros aplicados.</div>
  <?php else: ?>
    <div class="alert alert-success"><?php echo count($rows); ?> orçamento(s) encontrado(s).</div>
  <?php endif; ?>
<?php endif; ?>

<div class="card shadow-sm p-3">
  <div class="table-responsive">
    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'pdfsalvo'): ?>
      <div class="alert alert-success">📁 PDF salvo/atualizado com sucesso na pasta <code>pdfs/</code>.</div>
    <?php endif; ?>

    <?php if (isset($_GET['msg']) && $_GET['msg'] == 'excluido'): ?>
  <div class="alert alert-danger">❌ Orçamento excluído com sucesso.</div>
<?php endif; ?>


    <table class="table table-striped table-sm align-middle">
      <thead>
        <tr>
          <th>#</th>
          <th>Fornecedor</th>
          <th>Produtos</th>
          <th>Data/Hora</th>
          <th>Status</th>
          <th>Total</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?php echo $r['id']; ?></td>
            <td><?php echo htmlspecialchars($r['nome_fantasia']); ?></td>
            <td><?php echo htmlspecialchars($r['produtos']); ?></td>
            <td><?php echo date('d/m/Y', strtotime($r['data_orcamento'])); ?></td>

            <td>
              <?php if ($r['status'] === 'Finalizado'): ?>
                <span class="badge bg-success">Finalizado</span>
              <?php else: ?>
                <span class="badge bg-warning text-dark">Em andamento</span>
              <?php endif; ?>
            </td>
            <td>R$ <?php echo number_format($r['total'],2,',','.'); ?></td>
            <td class="text-end">
              <td class="text-end">
  <div class="btn-group btn-group-sm" role="group">
    <a class="btn btn-outline-primary" href="orcamento_novo.php?id=<?php echo $r['id']; ?>">
      ✏️ Editar
    </a>
    <a class="btn btn-outline-danger" target="_blank" href="orcamento_pdf.php?id=<?php echo $r['id']; ?>">
      📄 Abrir PDF
    </a>
    <a class="btn btn-outline-success" href="orcamento_pdf.php?id=<?php echo $r['id']; ?>&save=1">
      💾 Salvar PDF
    </a>
    <a class="btn btn-outline-dark"
       href="orcamento_excluir.php?id=<?php echo $r['id']; ?>"
       onclick="return confirm('Tem certeza que deseja excluir este orçamento?');">
      ❌ Excluir
    </a>
  </div>
</td>

            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require '_footer.php'; ?>
