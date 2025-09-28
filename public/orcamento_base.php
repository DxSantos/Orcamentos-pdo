<?php
require __DIR__ . '/../config.php';
require '_header.php';

$fornecedores = $pdo->query("SELECT * FROM fornecedores ORDER BY nome_fantasia")->fetchAll(PDO::FETCH_ASSOC);
$produtos = $pdo->query("SELECT * FROM produtos ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fornecedor_id = (int) ($_POST['fornecedor_id'] ?? 0);
    $itens = json_decode($_POST['itens_json'] ?? '[]', true);

    $pdo->beginTransaction();
    try {
        // Limpa base antiga do fornecedor
        $pdo->prepare("DELETE FROM orcamento_base WHERE fornecedor_id=?")->execute([$fornecedor_id]);

        $stmt = $pdo->prepare("INSERT INTO orcamento_base (fornecedor_id, produto_id) VALUES (?,?)");
        foreach ($itens as $produto_id) {
            $stmt->execute([$fornecedor_id, $produto_id]);
        }

        $pdo->commit();
        echo "<div class='alert alert-success'>Orçamento Base salvo!</div>";
    } catch (Throwable $e) {
        $pdo->rollBack();
        echo "<div class='alert alert-danger'>Erro: " . htmlspecialchars($e->getMessage()) . "</div>";
    }
}
?>

<h3>Orçamento Base</h3>

<form method="post" onsubmit="return beforeSubmitBase()">
    <div class="mb-3">
        <label>Fornecedor</label>
        <select id="fornecedorSelect" name="fornecedor_id" class="form-select" required>
            <option value="">Selecione...</option>
            <?php foreach ($fornecedores as $f): ?>
                <option value="<?= $f['id'] ?>"><?= htmlspecialchars($f['nome_fantasia']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>

    <table class="table table-bordered" id="itensBaseTable">
        <thead>
            <tr>
                <th>Produto</th>
                <th></th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>
    <button type="button" class="btn btn-sm btn-success mb-2" onclick="addRowBase()">+ Adicionar item</button>

    <input type="hidden" name="itens_json" id="itens_json">
    <button class="btn btn-primary">Salvar Base</button>
</form>

<script>
const PRODUTOS = <?php echo json_encode($produtos, JSON_UNESCAPED_UNICODE); ?>;
const tabelaItensBase = document.querySelector('#itensBaseTable tbody');
const fornecedorSelect = document.getElementById('fornecedorSelect');

function addRowBase(produtoId = '') {
    const tr = document.createElement('tr');
    tr.innerHTML = `
        <td>
            <select class="form-select form-select-sm produto-select">
                <option value="">Selecione...</option>
                ${PRODUTOS.map(p => `<option value="${p.id}">${p.nome}</option>`).join('')}
            </select>
        </td>
        <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove()">×</button></td>
    `;
    tabelaItensBase.appendChild(tr);
    if(produtoId) tr.querySelector('select').value = produtoId;
}

// Carrega itens ao escolher fornecedor
fornecedorSelect.addEventListener('change', () => {
    const fornecedorId = fornecedorSelect.value;
    tabelaItensBase.innerHTML = '';

    if(!fornecedorId) return;

    fetch(`get_orcamento_base.php?fornecedor_id=${fornecedorId}`)
        .then(res => res.json())
        .then(itens => {
            itens.forEach(item => addRowBase(item.produto_id));
        });
});

function beforeSubmitBase(){
    const produtosSelecionados = [];
    document.querySelectorAll('#itensBaseTable tbody tr').forEach(tr => {
        const produto_id = tr.querySelector('select').value;
        if(produto_id) produtosSelecionados.push(parseInt(produto_id));
    });
    if(produtosSelecionados.length === 0){ alert('Adicione pelo menos um item'); return false; }
    document.getElementById('itens_json').value = JSON.stringify(produtosSelecionados);
    return true;
}
</script>

<?php require '_footer.php'; ?>
