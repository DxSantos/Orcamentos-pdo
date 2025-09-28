<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../fpdf.php'; // Inclua a biblioteca FPDF

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
$orcamento = null;
$itens = [];

// Carrega orçamento existente
if ($id) {
    $st = $pdo->prepare("
        SELECT o.*, f.nome_fantasia, f.razao_social, f.cep, f.endereco, f.numero, f.bairro, f.cidade, f.uf, f.email, f.condicao_pagamento, f.telefone
        FROM orcamentos o 
        JOIN fornecedores f ON f.id=o.fornecedor_id 
        WHERE o.id=?
    ");
    $st->execute([$id]);
    $orcamento = $st->fetch(PDO::FETCH_ASSOC);

    $sti = $pdo->prepare("
        SELECT oi.*, p.nome, p.preco_unitario AS preco_cadastro, p.unidade_medida AS unidade_cadastro
        FROM orcamento_itens oi 
        LEFT JOIN materiaPri p ON p.id=oi.materiaPri_id 
        WHERE oi.orcamento_id=?
    ");
    $sti->execute([$id]);
    $itens = $sti->fetchAll(PDO::FETCH_ASSOC);
}

// Lista fornecedores e materiaPri
$fornecedores = $pdo->query("SELECT * FROM fornecedores ORDER BY nome_fantasia")->fetchAll(PDO::FETCH_ASSOC);
$materiaPri = $pdo->query("SELECT * FROM materiaPri ORDER BY nome")->fetchAll(PDO::FETCH_ASSOC);

// Salvar
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pdo->beginTransaction();
    try {
        $fornecedor_id = (int) ($_POST['fornecedor_id'] ?? 0);
        $observacoes   = $_POST['observacoes'] ?? '';
        $status        = $_POST['status'] ?? 'Em Andamento';

        if ($status === 'andamento') $status = 'Em Andamento';
        if ($status === 'finalizado') $status = 'Finalizado';

        // Atualiza ou insere orçamento
        if (!empty($_POST['orcamento_id'])) {
            $oid = (int) $_POST['orcamento_id'];
            $pdo->prepare("
                UPDATE orcamentos 
                SET fornecedor_id=?, observacoes=?, status=? 
                WHERE id=?
            ")->execute([$fornecedor_id, $observacoes, $status, $oid]);
            $pdo->prepare("DELETE FROM orcamento_itens WHERE orcamento_id=?")->execute([$oid]);
        } else {
            $pdo->prepare("
                INSERT INTO orcamentos (fornecedor_id, observacoes, status, data_orcamento) 
                VALUES (?,?,?,NOW())
            ")->execute([$fornecedor_id, $observacoes, $status]);
            $oid = (int) $pdo->lastInsertId();
        }

        // Salvar itens
        $rows = json_decode($_POST['itens_json'] ?? '[]', true);
        foreach ($rows as $r) {
            $materiaPri_id = $r['materiaPri_id'] ?? null;
            $qtemb      = $r['qtemb'] ?? '';
            $um         = $r['unidade_medida'] ?? 'un';
            $qtd        = (float) ($r['quantidade'] ?? 1);
            $preco      = (float) ($r['preco_unitario'] ?? 0);
            $total      = $qtd * $preco;

            if ($materiaPri_id && $status === 'Em Andamento') {
                $st = $pdo->prepare("SELECT preco_unitario, unidade_medida FROM materiaPri WHERE id=?");
                $st->execute([$materiaPri_id]);
                $p = $st->fetch(PDO::FETCH_ASSOC);
                if ($p && ((float)$p['preco_unitario'] !== $preco || $p['unidade_medida'] !== $um)) {
                    $pdo->prepare("UPDATE materiaPri SET preco_unitario=?, unidade_medida=?, updated_at=NOW() WHERE id=?")
                        ->execute([$preco, $um, $materiaPri_id]);
                }
            }

            $pdo->prepare("
                INSERT INTO orcamento_itens 
                (orcamento_id, materiaPri_id, qtemb, unidade_medida, quantidade, preco_unitario, total) 
                VALUES (?,?,?,?,?,?,?)
            ")->execute([$oid, $materiaPri_id, $qtemb, $um, $qtd, $preco, $total]);
        }

        $pdo->commit();

        // Gerar PDF se solicitado
        if (isset($_POST['salvar_pdf']) && $_POST['salvar_pdf'] === '1') {
            // Carrega dados do orçamento novamente
            $orcamento = $pdo->query("SELECT o.*, f.nome_fantasia, f.razao_social FROM orcamentos o JOIN fornecedores f ON f.id=o.fornecedor_id WHERE o.id=$oid")->fetch();
            $itens = $pdo->query("SELECT oi.*, p.nome FROM orcamento_itens oi LEFT JOIN materiaPri p ON p.id=oi.materiaPri_id WHERE oi.orcamento_id=$oid")->fetchAll();

            // Criar pasta pdfs se não existir
            if (!is_dir(__DIR__ . '/../pdfs')) mkdir(__DIR__ . '/../pdfs', 0777, true);

            $pdfFile = __DIR__ . "/../pdfs/orcamento_$oid.pdf";

            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial','B',14);
            $pdf->Cell(0,10,"Orçamento #$oid",0,1,'C');
            $pdf->SetFont('Arial','',12);
            $pdf->Cell(0,6,"Fornecedor: ".$orcamento['nome_fantasia'],0,1);
            $pdf->Cell(0,6,"Status: ".$orcamento['status'],0,1);
            $pdf->Ln(5);

            

            // Itens
            $pdf->SetFont('Arial','B',12);
            $pdf->Cell(80,6,"materiaPri",1);
            $pdf->Cell(20,6,"Qtd",1,0,'C');
            $pdf->Cell(30,6,"Unidade",1,0,'C');
            $pdf->Cell(30,6,"Preço",1,0,'C');
            $pdf->Cell(30,6,"Total",1,1,'C');
            $pdf->SetFont('Arial','',12);
            $totalGeral = 0;
            foreach($itens as $it){
                $pdf->Cell(80,6,$it['nome'],1);
                $pdf->Cell(20,6,$it['quantidade'],1,0,'C');
                $pdf->Cell(30,6,$it['unidade_medida'],1,0,'C');
                $pdf->Cell(30,6,number_format($it['preco_unitario'],2,',','.'),1,0,'C');
                $pdf->Cell(30,6,number_format($it['total'],2,',','.'),1,1,'C');
                $totalGeral += $it['total'];
            }
            $pdf->Cell(160,6,"Total Geral",1);
            $pdf->Cell(30,6,number_format($totalGeral,2,',','.'),1,1,'C');

            $pdf->Output('F', $pdfFile);

            header("Location: orcamentos.php");
            exit;
        }

        header("Location: orcamentos.php?ok=1");
        exit;

    } catch (Throwable $e) {
        $pdo->rollBack();
        http_response_code(500);
        echo "Erro ao salvar: " . htmlspecialchars($e->getMessage());
        exit;
    }
}

require '_header.php';
?>

<h3><?php echo $id ? 'Editar Orçamento #'.$id : 'Novo Orçamento'; ?></h3>

<form method="post" onsubmit="return beforeSubmit()">
  <input type="hidden" name="orcamento_id" value="<?php echo htmlspecialchars($orcamento['id'] ?? ''); ?>">

  <!-- FORNECEDOR -->
  <div class="card shadow-sm p-3 mb-3">
    <h5>Fornecedor</h5>
    <div class="row g-2 align-items-end">
      <div class="col-md-6">
        <label class="form-label">Fornecedor *</label>
        <select name="fornecedor_id" class="form-select" required>
          <option value="">Selecione...</option>
          <?php foreach ($fornecedores as $f): ?>
            <option value="<?php echo $f['id']; ?>" <?php echo ($orcamento && $orcamento['fornecedor_id']==$f['id'])?'selected':''; ?>>
              <?php echo htmlspecialchars($f['nome_fantasia'] . ' - ' . ($f['cidade'] ?? '') . '/' . ($f['uf'] ?? '')); ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-6">
        <label class="form-label">Observações</label>
        <input name="observacoes" class="form-control" value="<?php echo htmlspecialchars($orcamento['observacoes'] ?? ''); ?>">
      </div>
    </div>

    <!-- STATUS -->
    <div class="mt-3">
      <label class="form-label">Status</label><br>
      <input type="hidden" name="status" id="statusInput" value="<?php echo htmlspecialchars($orcamento['status'] ?? 'Em Andamento'); ?>">
      <button type="button" class="btn btn-sm" id="statusBtn" onclick="toggleStatus()"></button>
    </div>
  </div>


  <div class="mb-3">
  <label>Deseja iniciar orçamento:</label><br>
  <button type="button" class="btn btn-outline-secondary" onclick="importarBase()">Usar Base do Fornecedor</button>
  <button type="button" class="btn btn-outline-dark" onclick="iniciarZero()">Do Zero</button>
</div>


  <!-- ITENS -->
  <div class="card shadow-sm p-3 mb-3">
    <div class="d-flex justify-content-between align-items-center">
      <h5 class="mb-0">ITENS DO ORÇAMENTO</h5>
      <button type="button" class="btn btn-sm btn-outline-success" onclick="addRow()">+ Adicionar item</button>
    </div>
    <div class="table-responsive mt-2">
      <table class="table align-middle" id="itensTable">
        <thead><tr>
          <th style="min-width: 220px;">ITENS</th>
          <th>QUAT. EMBAL.</th>
          <th>UM</th>
          <th>Qtd</th>
          <th>Preço</th>
          <th>Total</th>
          <th>Status</th>
          <th></th>
        </tr></thead>
        <tbody id="tbodyItens"></tbody>
        <tfoot>
          <tr>
            <th colspan="5" class="text-end">Total geral:</th>
            <th id="totalGeral">R$ 0,00</th>
            <th></th>
            <th></th>
          </tr>
        </tfoot>
      </table>
    </div>
  </div>

  <input type="hidden" name="itens_json" id="itens_json">
  <div class="d-flex gap-2">
    <button class="btn btn-primary">Salvar</button>
   <button type="button" class="btn btn-outline-primary" 
    onclick="
        document.getElementById('salvar_pdf').value='1';
        if(beforeSubmit()) { this.closest('form').submit(); }
    ">
    Salvar & PDF
</button>
    <?php if ($id && file_exists(__DIR__ . "/../pdfs/orcamento_$id.pdf")): ?>
      <a href="<?php echo "../pdfs/orcamento_$id.pdf"; ?>" target="_blank" class="btn btn-success">Abrir PDF</a>
    <?php endif; ?>
    <input type="hidden" name="salvar_pdf" id="salvar_pdf" value="0">
  </div>
</form>

<script>
const materiaPri = <?php echo json_encode($materiaPri, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;
const ITENS = <?php echo json_encode($itens, JSON_UNESCAPED_UNICODE|JSON_UNESCAPED_SLASHES); ?>;

function currency(v){ return (v||0).toLocaleString('pt-BR',{style:'currency',currency:'BRL'}); }

function addRow(data={}){
  const tr = document.createElement('tr');
  const tdProd = document.createElement('td');
  const sel = document.createElement('select');
  sel.className = 'form-select form-select-sm materiaPri-select';
  sel.innerHTML = '<option value="">Selecione...</option>';
  for (const p of materiaPri) {
    const opt = new Option(p.nome, p.id);
    opt.dataset.preco = p.preco_unitario;
    opt.dataset.um = p.unidade_medida;
    sel.add(opt);
  }
  sel.onchange = ()=>{ 
    const selected = sel.options[sel.selectedIndex];
    tr.querySelector('.um').value = selected.dataset.um || 'un';
    tr.querySelector('.preco').value = selected.dataset.preco || 0;
    checkAlterado(tr); calcRow(tr);
  };
  tdProd.appendChild(sel);

  tr.innerHTML += `
    <td><input type="text" class="form-control form-control-sm qtemb" value="${data.qtemb||''}"></td>
    <td><input class="form-control form-control-sm um" value="${data.unidade_medida||'un'}" oninput="checkAlterado(this.closest('tr'))"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm qtd" value="${data.quantidade||1}" oninput="calcRow(this.closest('tr'))"></td>
    <td><input type="number" step="0.01" class="form-control form-control-sm preco" value="${data.preco_unitario||0}" oninput="checkAlterado(this.closest('tr'));calcRow(this.closest('tr'))"></td>
    <td><span class="fw-bold total">${currency(0)}</span></td>
    <td><span class="badge bg-warning text-dark d-none indicador">Alterado</span></td>
    <td><button type="button" class="btn btn-sm btn-outline-danger" onclick="this.closest('tr').remove();calcTotal()">×</button></td>
  `;
  tr.prepend(tdProd);
  document.getElementById('tbodyItens').appendChild(tr);

  if(data.materiaPri_id){ sel.value = data.materiaPri_id; }
  calcRow(tr); checkAlterado(tr);
}

function calcRow(tr){
  const qtd = parseFloat(tr.querySelector('.qtd').value||0);
  const preco = parseFloat(tr.querySelector('.preco').value||0);
  tr.querySelector('.total').textContent = currency(qtd*preco);
  calcTotal();
}

function calcTotal(){
  let total=0;
  for(const tr of document.querySelectorAll('#tbodyItens tr')){
    const qtd=parseFloat(tr.querySelector('.qtd').value||0);
    const preco=parseFloat(tr.querySelector('.preco').value||0);
    total+=qtd*preco;
  }
  document.getElementById('totalGeral').textContent=currency(total);
}

function checkAlterado(tr){
  const sel=tr.querySelector('.materiaPri-select');
  if(!sel || !sel.selectedOptions[0]) return;
  const precoOriginal=parseFloat(sel.selectedOptions[0].dataset.preco||0);
  const umOriginal=sel.selectedOptions[0].dataset.um||'';
  const precoAtual=parseFloat(tr.querySelector('.preco').value||0);
  const umAtual=tr.querySelector('.um').value||'';
  const span=tr.querySelector('.indicador');
  if(precoOriginal!==precoAtual || umOriginal!==umAtual){
    span.classList.remove('d-none');
  } else {
    span.classList.add('d-none');
  }
}

function beforeSubmit(){
  const items=[];
  for(const tr of document.querySelectorAll('#tbodyItens tr')){
    const sel=tr.querySelector('select');
    const materiaPri_id=sel && sel.value ? parseInt(sel.value) : null;
    const qtemb=tr.querySelector('.qtemb')?.value || '';
    const unidade_medida=tr.querySelector('.um').value||'un';
    const quantidade=parseFloat(tr.querySelector('.qtd').value||0);
    const preco_unitario=parseFloat(tr.querySelector('.preco').value||0);
    if(!materiaPri_id && !qtemb) continue;
    items.push({materiaPri_id,qtemb,unidade_medida,quantidade,preco_unitario});
  }
  document.getElementById('itens_json').value=JSON.stringify(items);
  if(items.length===0){alert('Adicione pelo menos um item.');return false;}
  return true;
}

// STATUS TOGGLE
function toggleStatus(){
  const input=document.getElementById('statusInput');
  input.value = input.value === 'Em Andamento' ? 'Finalizado' : 'Em Andamento';
  renderStatus();
}

function renderStatus(){
  const input=document.getElementById('statusInput');
  const btn=document.getElementById('statusBtn');
  if(input.value==='Em Andamento'){
    btn.textContent='Em andamento';
    btn.className='btn btn-sm btn-warning';
  } else {
    btn.textContent='Finalizado';
    btn.className='btn btn-sm btn-success';
  }
}

renderStatus();
if(Array.isArray(ITENS)&&ITENS.length){ITENS.forEach(it=>addRow(it));}else{addRow();}

async function importarBase(){
  const fornecedor_id = document.querySelector('[name=fornecedor_id]').value;
  if(!fornecedor_id){ alert('Selecione um fornecedor primeiro!'); return; }
  const res = await fetch('api_base.php?fornecedor_id='+fornecedor_id);
  const dados = await res.json();
  document.getElementById('tbodyItens').innerHTML = '';
  dados.forEach(it => {
    addRow({
      materiaPri_id: it.materiaPri_id,
      quantidade: 1,
      unidade_medida: it.unidade_medida,
      preco_unitario: parseFloat(it.preco_unitario)
    });
  });
  calcTotal();
}

function iniciarZero(){
  document.getElementById('tbodyItens').innerHTML='';
  addRow();
}



</script>

<?php require '_footer.php'; ?>
