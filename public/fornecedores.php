<?php
require __DIR__ . '/../config.php';

$action = $_GET['action'] ?? 'list';
$id = isset($_GET['id']) ? (int) $_GET['id'] : null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = [
        'razao_social'        => $_POST['razao_social'] ?? '',
        'nome_fantasia'       => $_POST['nome_fantasia'] ?? '',
        'endereco'            => $_POST['endereco'] ?? '',
        'numero'              => $_POST['numero'] ?? '',
        'bairro'              => $_POST['bairro'] ?? '',
        'cep'                 => $_POST['cep'] ?? '',
        'cidade'              => $_POST['cidade'] ?? '',
        'uf'                  => $_POST['uf'] ?? '',
        'email'               => $_POST['email'] ?? '',
        'condicao_pagamento'  => $_POST['condicao_pagamento'] ?? '',
        'cnpj'                => str_replace(['.', '/', '-'], '', $_POST['cnpj'] ?? ''),
        'telefone'            => $_POST['telefone'] ?? '',
    ];

    if (!empty($_POST['id'])) {
        // update
        $data['id'] = (int) $_POST['id'];
        $sql = "UPDATE fornecedores 
                SET razao_social=:razao_social, nome_fantasia=:nome_fantasia, endereco=:endereco, numero=:numero, 
                    bairro=:bairro, cep=:cep, cidade=:cidade, uf=:uf, email=:email, condicao_pagamento=:condicao_pagamento, 
                    cnpj=:cnpj, telefone=:telefone 
                WHERE id=:id";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
    } else {
        // insert
        $sql = "INSERT INTO fornecedores 
                (razao_social, nome_fantasia, endereco, numero, bairro, cep, cidade, uf, email, condicao_pagamento, cnpj, telefone) 
                VALUES 
                (:razao_social, :nome_fantasia, :endereco, :numero, :bairro, :cep, :cidade, :uf, :email, :condicao_pagamento, :cnpj, :telefone)";
        $stmt = $pdo->prepare($sql);
        $stmt->execute($data);
    }
    header("Location: fornecedores.php");
    exit;
}

if ($action === 'delete' && $id) {
    $pdo->prepare("DELETE FROM fornecedores WHERE id=?")->execute([$id]);
    header("Location: fornecedores.php");
    exit;
}

// fetch for edit
$edit = null;
if ($action === 'edit' && $id) {
    $stmt = $pdo->prepare("SELECT * FROM fornecedores WHERE id=?");
    $stmt->execute([$id]);
    $edit = $stmt->fetch();
}

// list all
$fornecedores = $pdo->query("SELECT * FROM fornecedores ORDER BY nome_fantasia")->fetchAll();

require '_header.php';
?>
<div class="d-flex justify-content-between align-items-center mb-3">
  <h3>Fornecedores</h3>
  <a href="fornecedores.php" class="btn btn-outline-secondary">Novo</a>
</div>

<div class="row">
  <div class="col-lg-6">
    <form method="post" class="card shadow-sm p-3">
      <input type="hidden" name="id" value="<?php echo htmlspecialchars($edit['id'] ?? ''); ?>">
      <div class="row g-2">
        <div class="col-md-6">
          <label class="form-label">CNPJ *</label>
          <input id="cnpj" name="cnpj" class="form-control" required 
                 value="<?php echo htmlspecialchars($edit['cnpj'] ?? ''); ?>" 
                 onblur="buscarCNPJ(this.value)">
        </div>
        <div class="col-md-6">
          <label class="form-label">Telefone</label>
          <input id="telefone" name="telefone" class="form-control" 
                 value="<?php echo htmlspecialchars($edit['telefone'] ?? ''); ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Razão Social *</label>
          <input id="razao_social" name="razao_social" class="form-control" required 
                 value="<?php echo htmlspecialchars($edit['razao_social'] ?? ''); ?>">
        </div>
        <div class="col-md-6">
          <label class="form-label">Nome Fantasia *</label>
          <input id="nome_fantasia" name="nome_fantasia" class="form-control" required 
                 value="<?php echo htmlspecialchars($edit['nome_fantasia'] ?? ''); ?>">
        </div>
        <div class="col-md-3">
          <label class="form-label">CEP</label>
          <input id="cep" name="cep" class="form-control" 
                 value="<?php echo htmlspecialchars($edit['cep'] ?? ''); ?>" 
                 onblur="buscarCEP(this.value)">
        </div>
        <div class="col-md-7">
          <label class="form-label">Endereço</label>
          <input id="endereco" name="endereco" class="form-control" 
                 value="<?php echo htmlspecialchars($edit['endereco'] ?? ''); ?>">
        </div>
        <div class="col-md-2">
          <label class="form-label">Número</label>
          <input id="numero" name="numero" class="form-control" 
                 value="<?php echo htmlspecialchars($edit['numero'] ?? ''); ?>">
        </div>
        <div class="col-md-4">
          <label class="form-label">Bairro</label>
          <input id="bairro" name="bairro" class="form-control" 
                 value="<?php echo htmlspecialchars($edit['bairro'] ?? ''); ?>">
        </div>
        <div class="col-md-5">
          <label class="form-label">Cidade</label>
          <input id="cidade" name="cidade" class="form-control" 
                 value="<?php echo htmlspecialchars($edit['cidade'] ?? ''); ?>">
        </div>
        <div class="col-md-3">
          <label class="form-label">UF</label>
          <input id="uf" name="uf" class="form-control" 
                 value="<?php echo htmlspecialchars($edit['uf'] ?? ''); ?>">
        </div>
        <div class="col-md-12">
          <label class="form-label">E-mail</label>
          <input id="email" type="email" name="email" class="form-control" 
                 value="<?php echo htmlspecialchars($edit['email'] ?? ''); ?>">
        </div>
        <div class="col-md-12">
          <label class="form-label">Condição de Pagamento</label>
          <input name="condicao_pagamento" class="form-control" 
                 value="<?php echo htmlspecialchars($edit['condicao_pagamento'] ?? ''); ?>">
        </div>
      </div>
      <div class="mt-3">
        <button class="btn btn-primary">Salvar</button>
      </div>
    </form>
  </div>
  <div class="col-lg-6">
    <div class="card shadow-sm p-3">
      <h5 class="mb-3">Lista</h5>
      <div class="table-responsive">
      <table class="table table-sm table-striped">
        <thead><tr><th>Nome Fantasia</th><th>Cidade</th><th></th></tr></thead>
        <tbody>
        <?php foreach ($fornecedores as $f): ?>
          <tr>
            <td><?php echo htmlspecialchars($f['nome_fantasia']); ?></td>
            <td><?php echo htmlspecialchars(($f['cidade'] ?? '') . ' - ' . ($f['uf'] ?? '')); ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-primary" href="fornecedores.php?action=edit&id=<?php echo $f['id']; ?>">Editar</a>
              <a class="btn btn-sm btn-outline-danger" href="fornecedores.php?action=delete&id=<?php echo $f['id']; ?>" onclick="return confirm('Excluir este fornecedor?')">Excluir</a>
            </td>
          </tr>
        <?php endforeach; ?>
        </tbody>
      </table>
      </div>
    </div>
  </div>
</div>


<script>
// Função principal: busca dados pelo CNPJ via Minha Receita e preenche todos os campos
async function preencherFornecedorPorCNPJ(cnpj) {
    cnpj = (cnpj || '').replace(/\D/g,'');
    if(cnpj.length !== 14) return;

    try {
        // 1️⃣ Consulta API Minha Receita
        const r = await fetch(`https://minhareceita.org/${cnpj}`);
        const j = await r.json();

        if (!j || j.error) {
            alert('CNPJ não encontrado');
            return;
        }

        // Preenche dados básicos do fornecedor
        document.querySelector('[name="razao_social"]').value = j.razao_social || '';
        document.querySelector('[name="nome_fantasia"]').value = j.nome_fantasia || '';
        document.querySelector('[name="email"]').value = j.email || '';
        document.querySelector('[name="telefone"]').value = j.ddd_telefone_1 || '';
        document.querySelector('[name="numero"]').value = j.numero || '';

        // Preenche CEP se existir
        const cep = (j.cep || '').replace(/\D/g,'');
        if (cep) {
            document.querySelector('[name="cep"]').value = cep;

            // 2️⃣ Consulta ViaCEP para completar endereço
            try {
                const rCep = await fetch(`https://viacep.com.br/ws/${cep}/json/`);
                const jCep = await rCep.json();
                if (jCep && !jCep.erro) {
                    document.querySelector('[name="endereco"]').value = jCep.logradouro || '';
                    document.querySelector('[name="bairro"]').value   = jCep.bairro || '';
                    document.querySelector('[name="cidade"]').value   = jCep.localidade || '';
                    document.querySelector('[name="uf"]').value       = jCep.uf || '';
                }
            } catch(e) {
                console.error('Erro ao buscar endereço pelo CEP:', e);
            }
        } else {
            // Se não tiver CEP, usa os dados retornados pela Minha Receita
            document.querySelector('[name="endereco"]').value = j.logradouro || '';
            document.querySelector('[name="bairro"]').value   = j.bairro || '';
            document.querySelector('[name="cidade"]').value   = j.municipio || '';
            document.querySelector('[name="uf"]').value       = j.uf || '';
            
        }

    } catch(e) {
        console.error('Erro ao consultar CNPJ:', e);
        alert('Erro ao consultar CNPJ');
    }
}

// Dispara a função ao perder foco do campo CNPJ
document.querySelector('[name="cnpj"]').addEventListener('blur', function(){
    preencherFornecedorPorCNPJ(this.value);
});
</script>




<?php require '_footer.php'; ?>
