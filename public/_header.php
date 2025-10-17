<?php
if (session_status() === PHP_SESSION_NONE) session_start();
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Sistema de Produção</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet">  
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  
  <link rel="stylesheet" href="assets/css/style.css">
  <script src="assets/js/custom.js" defer></script>

  <style>
    /* SIDEBAR FIXO */
    body {
      display: flex;
      min-height: 100vh;
      background-color: #f8fafc;
    }

    .sidebar {
      width: 240px;
      background-color: #0d6efd;
      color: white;
      flex-shrink: 0;
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
      z-index: 1000;
    }

    .sidebar .brand {
      font-size: 1.3rem;
      font-weight: 600;
      padding: 20px;
      text-align: center;
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
      background-color: rgba(0, 0, 0, 0.1);
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
      margin: 0;
      flex-grow: 1;
    }

    .sidebar ul li {
      border-bottom: 1px solid rgba(255, 255, 255, 0.08);
    }

    .sidebar ul li a {
      display: block;
      padding: 14px 20px;
      color: #e9ecef;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.2s ease-in-out;
    }

    .sidebar ul li a:hover,
    .sidebar ul li a.active {
      background-color: rgba(255, 255, 255, 0.15);
      color: #fff;
    }

    .sidebar ul li a i {
      width: 22px;
      text-align: center;
      margin-right: 8px;
    }

    main {
      flex-grow: 1;
      margin-left: 240px;
      padding: 20px 30px;
    }

    @media (max-width: 991px) {
      .sidebar {
        position: fixed;
        transform: translateX(-100%);
        transition: transform 0.3s ease-in-out;
      }

      .sidebar.show {
        transform: translateX(0);
      }

      main {
        margin-left: 0;
      }

      .toggle-sidebar {
        position: fixed;
        top: 10px;
        left: 10px;
        z-index: 1100;
        background-color: #0d6efd;
        color: #fff;
        border: none;
        border-radius: 6px;
        padding: 8px 10px;
      }
    }
  </style>
</head>

<body>
  <!-- BOTÃO MOBILE -->
  <button class="toggle-sidebar d-lg-none" onclick="document.querySelector('.sidebar').classList.toggle('show')">
    ☰
  </button>

  <!-- MENU LATERAL -->
  <nav class="sidebar">
    <div class="brand">⚙️ Sistema ERP</div>
     <ul>
    <li><a href="index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>"><i class="bi bi-speedometer2"></i> Dashboard</a></li>
    <li><a href="materiaPri.php" class="<?= basename($_SERVER['PHP_SELF']) == 'materiaPri.php' ? 'active' : '' ?>"><i class="bi bi-box"></i> Matéria-Prima</a></li>
    <li><a href="fornecedores.php" class="<?= basename($_SERVER['PHP_SELF']) == 'fornecedores.php' ? 'active' : '' ?>"><i class="bi bi-people"></i> Fornecedores</a></li>
    <li><a href="orcamento_base.php" class="<?= basename($_SERVER['PHP_SELF']) == 'orcamento_base.php' ? 'active' : '' ?>"><i class="bi bi-file-earmark-plus"></i> Novo Orçamento</a></li>
    <li><a href="orcamentos.php" class="<?= basename($_SERVER['PHP_SELF']) == 'orcamentos.php' ? 'active' : '' ?>"><i class="bi bi-file-earmark-text"></i> Orçamentos</a></li>
    <li><a href="produtos.php" class="<?= basename($_SERVER['PHP_SELF']) == 'produtos.php' ? 'active' : '' ?>"><i class="bi bi-layers"></i> Produtos</a></li>
    <li><a href="estoque.php" class="<?= basename($_SERVER['PHP_SELF']) == 'estoque.php' ? 'active' : '' ?>"><i class="bi bi-archive"></i> Estoque</a></li>
    <li><a href="dashboard_orcamentos.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard_orcamentos.php' ? 'active' : '' ?>"><i class="bi bi-bar-chart-line"></i> Dashboard Orçamentos</a></li>
    <li><a href="relatorios.php" class="<?= basename($_SERVER['PHP_SELF']) == 'relatorios.php' ? 'active' : '' ?>"><i class="bi bi-journal-text"></i> Relatórios</a></li>
  </ul>
    <div class="text-center mb-3">
      <a href="/logout.php" class="btn btn-sm btn-light px-4">Sair</a>
    </div>
  </nav>

  <main>
