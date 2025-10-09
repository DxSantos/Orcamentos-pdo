<?php
require __DIR__ . '/../config.php';
?>
<!doctype html>
<html lang="pt-br">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Orçamentos - PHP/PDO</title>
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link rel="styLesheet" href="orcamentos.css">
</head>
<body class="bg-light">
<nav class="navbar navbar-expand-lg bg-dark navbar-dark mb-4">
  <div class="container">
    <a class="navbar-brand" href="<?php echo url('index.php'); ?>">Orçamentos</a>
    <div class="navbar-nav">
      <a class="nav-link" href="<?php echo url('fornecedores.php'); ?>">Fornecedores</a>
      <a class="nav-link" href="<?php echo url('materiaPri.php'); ?>">Materia Prima</a>
      <a class="nav-link" href="<?php echo url('orcamento_base.php'); ?>">Orçamento base</a>
      <a class="nav-link" href="<?php echo url('orcamento_novo.php'); ?>">Novo Orçamento</a>
      <a class="nav-link" href="<?php echo url('orcamentos.php'); ?>">Buscar/Editar Orçamentos</a>
      <a class="nav-link" href="<?php echo url('dashboard_orcamentos.php'); ?>">Dashboard</a>
    </div>
  </div>
</nav>
<div class="container mb-5">
