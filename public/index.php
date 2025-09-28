<?php require '_header.php'; ?>
<div class="row g-3">
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Cadastro de Fornecedores</h5>
        <p class="card-text">Inclua ou atualize dados dos fornecedores.</p>
        <a class="btn btn-primary" href="fornecedores.php">Abrir</a>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card shadow-sm">
      <div class="card-body">
        <h5 class="card-title">Cadastro de Produtos</h5>
        <p class="card-text">Gerencie produtos, preços e unidades de medida.</p>
        <a class="btn btn-primary" href="produtos.php">Abrir</a>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card shadow-sm mt-3">
      <div class="card-body">
        <h5 class="card-title">Criar Orçamento Base</h5>
        <p class="card-text">Selecione um fornecedor e crie um modelo de orçamento.</p>
        <a class="btn btn-success" href="orcamento_base.php">Novo Orçamento</a>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card shadow-sm mt-3">
      <div class="card-body">
        <h5 class="card-title">Montagem de Orçamento</h5>
        <p class="card-text">Selecione um fornecedor e adicione itens do cadastro.</p>
        <a class="btn btn-success" href="orcamento_novo.php">Novo Orçamento</a>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card shadow-sm mt-3">
      <div class="card-body">
        <h5 class="card-title">Buscar/Editar Orçamentos</h5>
        <p class="card-text">Localize orçamentos por fornecedor e data; edite quando necessário.</p>
        <a class="btn btn-secondary" href="orcamentos.php">Abrir</a>
      </div>
    </div>
  </div>
  <div class="col-md-6">
    <div class="card shadow-sm mt-3">
      <div class="card-body">
        <h5 class="card-title">Deshboard</h5>
        <p class="card-text">Faça análises fazendo filtros e obtendo gráficos.</p>
        <a class="btn btn-secondary" href="dashboard_orcamentos.php">Abrir</a>
      </div>
    </div>
  </div>
</div>
<?php require '_footer.php'; ?>
