<?php
if (session_status() === PHP_SESSION_NONE) session_start();

// Detecta o caminho base automaticamente
$base_url = rtrim(dirname($_SERVER['PHP_SELF']), '/\\');
?>
<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8">
  <title>Sistema ERP</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">



  <!-- BOOTSTRAP -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

  <!-- ÍCONES -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">

  <!-- CSS LOCAL -->
  <link rel="stylesheet" href="assets/css/style.css">

  <!-- JS LOCAL -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
  <script src="<?= $base_url ?>assets/js/custom.js" defer></script>

  <style>
    body {
      display: flex;
      min-height: 100vh;
      background-color: #f8fafc;
      font-family: "Segoe UI", Arial, sans-serif;
    }

    .sidebar {
      width: 240px;
      background: linear-gradient(180deg, #0d6efd 0%, #0149b5 100%);
      color: white;
      display: flex;
      flex-direction: column;
      position: fixed;
      top: 0;
      left: 0;
      height: 100%;
      box-shadow: 2px 0 10px rgba(0, 0, 0, 0.15);
      z-index: 1000;
      border-right: 1px solid rgba(255, 255, 255, 0.15);
    }

    .sidebar .brand {
      font-size: 1.4rem;
      font-weight: 600;
      text-align: center;
      padding: 22px 10px;
      background-color: rgba(0, 0, 0, 0.1);
      border-bottom: 1px solid rgba(255, 255, 255, 0.2);
      letter-spacing: 0.5px;
    }

    .sidebar ul {
      list-style: none;
      padding: 0;
      margin: 0;
      flex-grow: 1;
    }

    .sidebar ul li {
      border-bottom: 1px solid rgba(255, 255, 255, 0.05);
    }

    .sidebar ul li a {
      display: flex;
      align-items: center;
      gap: 10px;
      padding: 13px 20px;
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
      font-size: 1.1rem;
    }

    .sidebar .logout {
      text-align: center;
      padding: 15px 0;
      border-top: 1px solid rgba(255, 255, 255, 0.1);
    }

    .sidebar .logout a {
      color: white;
      background-color: rgba(255, 255, 255, 0.2);
      border-radius: 6px;
      padding: 6px 18px;
      text-decoration: none;
      transition: 0.3s;
    }

    .sidebar .logout a:hover {
      background-color: rgba(255, 255, 255, 0.3);
    }

    main {
      flex-grow: 1;
      margin-left: 240px;
      padding: 25px 35px;
    }

    @media (max-width: 991px) {
      .sidebar {
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
        padding: 8px 12px;
        box-shadow: 0 2px 6px rgba(0, 0, 0, 0.2);
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
      <li><a href="<?= $base_url ?>/index.php" class="<?= basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : '' ?>"><i class="bi bi-speedometer2"></i> Início</a></li>
      <li><a href="<?= $base_url ?>/fornecedores.php" class="<?= basename($_SERVER['PHP_SELF']) == 'fornecedores.php' ? 'active' : '' ?>"><i class="bi bi-people"></i> Fornecedores</a></li>
      <li><a href="<?= $base_url ?>/materiaPri.php" class="<?= basename($_SERVER['PHP_SELF']) == 'materiaPri.php' ? 'active' : '' ?>"><i class="bi bi-box"></i> Matéria-Prima</a></li>
      <li><a href="<?= $base_url ?>/orcamento_base.php" class="<?= basename($_SERVER['PHP_SELF']) == 'orcamento_base.php' ? 'active' : '' ?>"><i class="bi bi-journal-text"></i> Orçamento Base</a></li>
      <li><a href="<?= $base_url ?>/orcamento_novo.php" class="<?= basename($_SERVER['PHP_SELF']) == 'orcamento_novo.php' ? 'active' : '' ?>"><i class="bi bi-file-plus"></i> Novo Orçamento</a></li>
      <li><a href="<?= $base_url ?>/orcamentos.php" class="<?= basename($_SERVER['PHP_SELF']) == 'orcamentos.php' ? 'active' : '' ?>"><i class="bi bi-file-earmark-text"></i> Orçamentos</a></li>
      <li><a href="<?= $base_url ?>/dashboard_orcamentos.php" class="<?= basename($_SERVER['PHP_SELF']) == 'dashboard_orcamentos.php' ? 'active' : '' ?>"><i class="bi bi-graph-up"></i> Dashboard Orçamentos</a></li>
    </ul>

    <div class="logout">
      <a href="<?= $base_url ?>/logout.php"><i class="bi bi-box-arrow-right"></i> Sair</a>
    </div>
  </nav>

  <main>