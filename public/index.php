<?php require '_header.php'; ?>

<div class="container py-4">
  <h2 class="mb-4 text-center fw-bold text-primary">
    <i class="bi bi-grid"></i> Painel de Controle
  </h2>

  <div class="row g-4">

    <!-- Cadastro de Fornecedores -->
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body text-center">
          <div class="mb-3 text-primary fs-1">
            <i class="bi bi-building"></i>
          </div>
          <h5 class="card-title fw-bold">Cadastro de Fornecedores</h5>
          <p class="card-text text-muted">Inclua ou atualize dados dos fornecedores.</p>
          <a class="btn btn-outline-primary w-100" href="fornecedores.php">
            <i class="bi bi-box-arrow-in-right"></i> Abrir
          </a>
        </div>
      </div>
    </div>

    <!-- Cadastro de Matéria Prima -->
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body text-center">
          <div class="mb-3 text-primary fs-1">
            <i class="bi bi-box-seam"></i>
          </div>
          <h5 class="card-title fw-bold">Cadastro de Matéria-Prima</h5>
          <p class="card-text text-muted">Gerencie matérias-primas, grupos e preços.</p>
          <a class="btn btn-outline-primary w-100" href="materiaPri.php">
            <i class="bi bi-box-arrow-in-right"></i> Abrir
          </a>
        </div>
      </div>
    </div>

    <!-- Criar Orçamento Base -->
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body text-center">
          <div class="mb-3 text-success fs-1">
            <i class="bi bi-clipboard-plus"></i>
          </div>
          <h5 class="card-title fw-bold">Criar Orçamento Base</h5>
          <p class="card-text text-muted">Selecione um fornecedor e crie um modelo de orçamento.</p>
          <a class="btn btn-success w-100" href="orcamento_base.php">
            <i class="bi bi-plus-circle"></i> Novo Orçamento
          </a>
        </div>
      </div>
    </div>

    <!-- Montagem de Orçamento -->
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body text-center">
          <div class="mb-3 text-success fs-1">
            <i class="bi bi-clipboard-data"></i>
          </div>
          <h5 class="card-title fw-bold">Montagem de Orçamento</h5>
          <p class="card-text text-muted">Monte orçamentos adicionando itens e fornecedores.</p>
          <a class="btn btn-success w-100" href="orcamento_novo.php">
            <i class="bi bi-pencil-square"></i> Novo Orçamento
          </a>
        </div>
      </div>
    </div>

    <!-- Buscar/Editar Orçamentos -->
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body text-center">
          <div class="mb-3 text-secondary fs-1">
            <i class="bi bi-search"></i>
          </div>
          <h5 class="card-title fw-bold">Buscar/Editar Orçamentos</h5>
          <p class="card-text text-muted">Localize orçamentos por fornecedor e data.</p>
          <a class="btn btn-outline-secondary w-100" href="orcamentos.php">
            <i class="bi bi-folder2-open"></i> Abrir
          </a>
        </div>
      </div>
    </div>

    <!-- Dashboard -->
    <div class="col-md-4">
      <div class="card border-0 shadow-sm h-100">
        <div class="card-body text-center">
          <div class="mb-3 text-secondary fs-1">
            <i class="bi bi-bar-chart"></i>
          </div>
          <h5 class="card-title fw-bold">Dashboard</h5>
          <p class="card-text text-muted">Analise seus orçamentos com gráficos e filtros.</p>
          <a class="btn btn-outline-secondary w-100" href="dashboard_orcamentos.php">
            <i class="bi bi-graph-up"></i> Abrir
          </a>
        </div>
      </div>
    </div>

  </div>
</div>

<?php require '_footer.php'; ?>
