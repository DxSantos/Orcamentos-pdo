<?php
require __DIR__ . '/../config.php';

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

// --- SALVAR (INSERT/UPDATE)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nome'              => $_POST['nome'] ?? '',
        'grupo_id'          => (int) ($_POST['grupo_id'] ?? 0),
        'tipo'              => $_POST['tipo'] ?? 'insumo',
        'origem'            => $_POST['origem'] ?? 'comprado',
        'preco_unitario'    => ($_POST['origem'] === 'comprado') ? (float) ($_POST['preco_unitario'] ?? 0) : 0,
        'icms'              => ($_POST['origem'] === 'comprado') ? (float) ($_POST['icms'] ?? 0) : 0,
        'preco_fabricacao'  => ($_POST['origem'] === 'fabricacao') ? (float) ($_POST['preco_fabricacao'] ?? 0) : 0,
        'unidade_medida'    => $_POST['unidade_medida'] ?? 'un',
        'limite_alerta'     => (int) ($_POST['limite_alerta'] ?? 0),
        'ativo'             => isset($_POST['ativo']) ? (int) $_POST['ativo'] : 1,
    ];

    if (!empty($_POST['id'])) {
        $data['id'] = (int) $_POST['id'];
        $sql = "UPDATE materiaPri 
                SET nome=:nome, grupo_id=:grupo_id, tipo=:tipo, origem=:origem,
                    preco_unitario=:preco_unitario, icms=:icms, preco_fabricacao=:preco_fabricacao,
                    unidade_medida=:unidade_medida, limite_alerta=:limite_alerta,
                    ativo=:ativo, updated_at=NOW() 
                WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
    } else {
        $sql = "INSERT INTO materiaPri 
                (nome, grupo_id, tipo, origem, preco_unitario, icms, preco_fabricacao, unidade_medida, limite_alerta, ativo) 
                VALUES (:nome, :grupo_id, :tipo, :origem, :preco_unitario, :icms, :preco_fabricacao, :unidade_medida, :limite_alerta, :ativo)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
    }

    header("Location: materiaPri.php");
    exit;
}

// --- EXCLUIR
if ($action === 'delete' && $id) {
    $pdo->prepare("DELETE FROM materiaPri WHERE id=?")->execute([$id]);
    header("Location: materiaPri.php");
    exit;
}

// --- BUSCA PARA EDIÇÃO
$edit = null;
if ($action === 'edit' && $id) {
    $st = $pdo->prepare("SELECT * FROM materiaPri WHERE id=?");
    $st->execute([$id]);
    $edit = $st->fetch(PDO::FETCH_ASSOC);
}

// --- LISTA DE GRUPOS
$grupos = $pdo->query("SELECT * FROM grupos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// --- FILTROS DE BUSCA
$f_nome  = trim($_GET['f_nome'] ?? '');
$f_grupo = trim($_GET['f_grupo'] ?? '');
$f_ativo = trim($_GET['f_ativo'] ?? '');

$sql = "SELECT p.*, g.nome AS grupo_nome,
               (CASE WHEN p.origem='comprado' 
                     THEN (p.preco_unitario + (p.preco_unitario * (p.icms/100)))
                     ELSE p.preco_fabricacao END) AS preco_final
        FROM materiaPri p
        LEFT JOIN grupos g ON g.id=p.grupo_id
        WHERE 1=1";
$params = [];

if ($f_nome !== '') {
    $sql .= " AND p.nome LIKE ?";
    $params[] = "%$f_nome%";
}
if ($f_grupo !== '') {
    $sql .= " AND p.grupo_id = ?";
    $params[] = $f_grupo;
}
if ($f_ativo !== '') {
    $sql .= " AND p.ativo = ?";
    $params[] = $f_ativo;
}

$sql .= " ORDER BY p.nome";
$st = $pdo->prepare($sql);
$st->execute($params);
$materiaPri = $st->fetchAll(PDO::FETCH_ASSOC);

require '_header.php';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Cadastro de Matéria-Prima</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container my-4">

  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2 class="mb-0">🧾 Cadastro de Matéria-Prima</h2>
    <a href="materiaPri.php" class="btn btn-outline-secondary">➕ Novo</a>
  </div>

  <div class="row">
    <!-- FORMULÁRIO -->
    <div class="col-lg-5 mb-4">
      <form method="post" class="card shadow-sm border-0">
        <div class="card-body">
          <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit['id'] ?? ''); ?>">

          <h5 class="text-primary mb-3">Informações</h5>
          <div class="mb-3">
            <label class="form-label fw-bold">Nome *</label>
            <input name="nome" class="form-control" required 
                   value="<?php echo htmlspecialchars($edit['nome'] ?? ''); ?>">
          </div>

          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">Grupo *</label>
              <select name="grupo_id" class="form-select" required>
                <option value="">Selecione</option>
                <?php foreach ($grupos as $g): ?>
                  <option value="<?php echo $g['id']; ?>" 
                    <?php if (($edit['grupo_id'] ?? '') == $g['id']) echo 'selected'; ?>>
                    <?php echo htmlspecialchars($g['nome']); ?>
                  </option>
                <?php endforeach; ?>
              </select>
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Tipo *</label>
              <select name="tipo" class="form-select" required>
                <option value="insumo" <?php if (($edit['tipo'] ?? '') === 'insumo') echo 'selected'; ?>>Insumo</option>
                <option value="acabado" <?php if (($edit['tipo'] ?? '') === 'acabado') echo 'selected'; ?>>Acabado</option>
              </select>
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Origem *</label>
            <select name="origem" id="origem" class="form-select" required onchange="toggleOrigem()">
              <option value="comprado" <?php if (($edit['origem'] ?? 'comprado') === 'comprado') echo 'selected'; ?>>Comprado</option>
              <option value="fabricacao" <?php if (($edit['origem'] ?? '') === 'fabricacao') echo 'selected'; ?>>Fabricação Própria</option>
            </select>
          </div>

          <!-- CAMPOS PARA COMPRADO -->
          <div id="compradoFields">
            <h5 class="text-primary mb-3">Preço & Tributação</h5>
            <div class="row g-2 mb-3">
              <div class="col-md-6">
                <label class="form-label fw-bold">Preço Unitário *</label>
                <input name="preco_unitario" type="number" step="0.01" class="form-control"
                       value="<?php echo htmlspecialchars($edit['preco_unitario'] ?? ''); ?>">
              </div>
              <div class="col-md-6">
                <label class="form-label fw-bold">ICMS (%)</label>
                <input name="icms" type="number" step="0.01" class="form-control"
                       value="<?php echo htmlspecialchars($edit['icms'] ?? '0'); ?>">
              </div>
            </div>
            <div class="mb-3">
              <label class="form-label fw-bold">Preço Final</label>
              <input id="preco_final" type="text" class="form-control" readonly>
            </div>
          </div>

          <!-- CAMPOS PARA FABRICAÇÃO -->
          <div id="fabricacaoFields">
            <h5 class="text-primary mb-3">Preço Fabricação</h5>
            <div class="mb-3">
              <label class="form-label fw-bold">Preço Fabricação *</label>
              <input name="preco_fabricacao" type="number" step="0.01" class="form-control"
                     value="<?php echo htmlspecialchars($edit['preco_fabricacao'] ?? ''); ?>">
            </div>
          </div>

          <h5 class="text-primary mb-3">Estoque</h5>
          <div class="row g-2 mb-3">
            <div class="col-md-6">
              <label class="form-label fw-bold">Unidade Medida</label>
              <input name="unidade_medida" class="form-control"
                     value="<?php echo htmlspecialchars($edit['unidade_medida'] ?? 'un'); ?>">
            </div>
            <div class="col-md-6">
              <label class="form-label fw-bold">Limite para Alerta</label>
              <input name="limite_alerta" type="number" class="form-control"
                     value="<?php echo htmlspecialchars($edit['limite_alerta'] ?? ''); ?>">
            </div>
          </div>

          <div class="mb-3">
            <label class="form-label fw-bold">Ativo</label>
            <select name="ativo" class="form-select">
              <option value="1" <?php if (($edit['ativo'] ?? '1') == '1') echo 'selected'; ?>>Sim</option>
              <option value="0" <?php if (($edit['ativo'] ?? '') == '0') echo 'selected'; ?>>Não</option>
            </select>
          </div>

          <div class="mt-4 text-end">
            <button class="btn btn-primary px-4">💾 Salvar</button>
          </div>
        </div>
      </form>
    </div>

    <!-- LISTA -->
    <div class="col-lg-7">
      <div class="card shadow-sm border-0 p-3">
        <h5 class="mb-3">📑 Lista de Matérias-Primas</h5>

        <!-- FILTROS -->
        <form class="row g-2 mb-3">
          <div class="col-md-4">
            <input type="text" name="f_nome" class="form-control" placeholder="Nome"
                   value="<?php echo htmlspecialchars($f_nome); ?>">
          </div>
          <div class="col-md-4">
            <select name="f_grupo" class="form-select">
              <option value="">Todos os Grupos</option>
              <?php foreach ($grupos as $g): ?>
                <option value="<?php echo $g['id']; ?>" <?php if ($f_grupo == $g['id']) echo 'selected'; ?>>
                  <?php echo htmlspecialchars($g['nome']); ?>
                </option>
              <?php endforeach; ?>
            </select>
          </div>
          <div class="col-md-2">
            <select name="f_ativo" class="form-select">
              <option value="">Ativo?</option>
              <option value="1" <?php if ($f_ativo==='1') echo 'selected'; ?>>Sim</option>
              <option value="0" <?php if ($f_ativo==='0') echo 'selected'; ?>>Não</option>
            </select>
          </div>
          <div class="col-md-2">
            <button class="btn btn-outline-primary w-100">Filtrar</button>
          </div>
        </form>

        <!-- TABELA -->
        <div class="table-responsive">
          <table class="table table-hover table-sm align-middle">
            <thead class="table-light">
              <tr>
                <th>Nome</th>
                <th>Grupo</th>
                <th>Origem</th>
                <th>Preço Final</th>
                <th>UM</th>
                <th>Ativo</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
            <?php foreach ($materiaPri as $p): ?>
              <tr>
                <td><?php echo htmlspecialchars($p['nome']); ?></td>
                <td><?php echo htmlspecialchars($p['grupo_nome']); ?></td>
                <td><?php echo $p['origem'] === 'comprado' ? 'Comprado' : 'Fabricação'; ?></td>
                <td>R$ <?php echo number_format($p['preco_final'], 2, ',', '.'); ?></td>
                <td><?php echo htmlspecialchars($p['unidade_medida']); ?></td>
                <td>
                  <?php if ($p['ativo']): ?>
                    <span class="badge bg-success">Ativo</span>
                  <?php else: ?>
                    <span class="badge bg-secondary">Inativo</span>
                  <?php endif; ?>
                </td>
                <td class="text-end">
                  <a class="btn btn-sm btn-outline-primary" 
                     href="materiaPri.php?action=edit&id=<?php echo $p['id']; ?>">✏️ Editar</a>
                  <a class="btn btn-sm btn-outline-danger" 
                     href="materiaPri.php?action=delete&id=<?php echo $p['id']; ?>"
                     onclick="return confirm('Excluir este materiaPri?')">🗑️ Excluir</a>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<script>
function calcularPrecoFinal() {
  if (document.getElementById('origem').value === 'comprado') {
    let preco = parseFloat(document.querySelector('[name="preco_unitario"]').value) || 0;
    let icms = parseFloat(document.querySelector('[name="icms"]').value) || 0;
    let final = preco + (preco * (icms / 100));
    document.getElementById('preco_final').value = final.toFixed(2).replace('.', ',');
  }
}
function toggleOrigem() {
  let origem = document.getElementById('origem').value;
  document.getElementById('compradoFields').style.display = (origem === 'comprado') ? 'block' : 'none';
  document.getElementById('fabricacaoFields').style.display = (origem === 'fabricacao') ? 'block' : 'none';
}
document.querySelector('[name="preco_unitario"]').addEventListener('input', calcularPrecoFinal);
document.querySelector('[name="icms"]').addEventListener('input', calcularPrecoFinal);
window.addEventListener('load', function() {
  toggleOrigem();
  calcularPrecoFinal();
});
</script>
</body>
</html>
<?php require '_footer.php'; ?>
