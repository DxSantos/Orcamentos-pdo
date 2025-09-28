<?php
require __DIR__ . '/../config.php';

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

// --- SALVAR (INSERT/UPDATE) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'nome' => $_POST['nome'] ?? '',
    ];

    if (!empty($_POST['id'])) {
        // --- UPDATE ---
        $data['id'] = (int) $_POST['id'];
        $sql = "UPDATE grupos SET nome=:nome, updated_at=NOW() WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
    } else {
        // --- INSERT ---
        $sql = "INSERT INTO grupos (nome) VALUES (:nome)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
    }

    header("Location: grupos.php");
    exit;
}

// --- EXCLUIR ---
if ($action === 'delete' && $id) {
    $pdo->prepare("DELETE FROM grupos WHERE id=?")->execute([$id]);
    header("Location: grupos.php");
    exit;
}

// --- BUSCA PARA EDIÇÃO ---
$edit = null;
if ($action === 'edit' && $id) {
    $st = $pdo->prepare("SELECT * FROM grupos WHERE id=?");
    $st->execute([$id]);
    $edit = $st->fetch(PDO::FETCH_ASSOC);
}

// Lista todos os grupos
$grupos = $pdo->query("SELECT * FROM grupos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

require '_header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Grupos de Produtos</h3>
  <a href="grupos.php" class="btn btn-outline-secondary">Novo</a>
</div>

<div class="row">
  <div class="col-lg-4">
    <form method="post" class="card shadow-sm p-3">
      <input type="hidden" name="id" value="<?= htmlspecialchars($edit['id'] ?? '') ?>">
      <div class="mb-2">
        <label class="form-label">Nome do Grupo *</label>
        <input type="text" name="nome" class="form-control" required value="<?= htmlspecialchars($edit['nome'] ?? '') ?>">
      </div>
      <div class="mt-3">
        <button class="btn btn-primary">Salvar</button>
      </div>
    </form>
  </div>

  <div class="col-lg-8">
    <div class="card shadow-sm p-3">
      <h5 class="mb-3">Lista de Grupos</h5>
      <div class="table-responsive">
        <table class="table table-sm table-striped">
          <thead>
            <tr><th>Nome do Grupo</th><th></th></tr>
          </thead>
          <tbody>
          <?php foreach ($grupos as $g): ?>
            <tr>
              <td><?= htmlspecialchars($g['nome']) ?></td>
              <td class="text-end">
                <a class="btn btn-sm btn-outline-primary" href="grupos.php?action=edit&id=<?= $g['id'] ?>">Editar</a>
                <a class="btn btn-sm btn-outline-danger" href="grupos.php?action=delete&id=<?= $g['id'] ?>" onclick="return confirm('Excluir este grupo?')">Excluir</a>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>
</div>

<?php require '_footer.php'; ?>
