<?php
require __DIR__ . '/../config.php';

// ==================== FILTROS ====================
$fornecedor = trim($_GET['fornecedor'] ?? '');
$status     = trim($_GET['status'] ?? '');
$data_inicio = trim($_GET['data_inicio'] ?? '');
$data_fim    = trim($_GET['data_fim'] ?? '');

$materiaPri   = trim($_GET['materiaPri'] ?? '');


// ==================== CONSULTA ORÇAMENTOS ====================
$sql = "SELECT o.id, o.data_orcamento, o.status, f.nome_fantasia,
               COALESCE(SUM(oi.total),0) AS total
        FROM orcamentos o
        JOIN fornecedores f ON f.id=o.fornecedor_id
        LEFT JOIN orcamento_itens oi ON oi.orcamento_id=o.id
        WHERE 1=1";
$params = [];

if ($fornecedor !== '') {
    $sql .= " AND f.id=?";
    $params[] = $fornecedor;
}
if ($status !== '') {
    $sql .= " AND o.status=?";
    $params[] = $status;
}
if ($data_inicio !== '') {
    $sql .= " AND o.data_orcamento>=?";
    $params[] = $data_inicio;
}
if ($data_fim !== '') {
    $sql .= " AND o.data_orcamento<=?";
    $params[] = $data_fim;
}

if ($materiaPri !== '') {
    $sql .= " AND oi.materiaPri_id=?";
    $params[] = $materiaPri;
}

$sql .= " GROUP BY o.id ORDER BY o.data_orcamento DESC";
$st = $pdo->prepare($sql);
$st->execute($params);
$orcamentos = $st->fetchAll();

// ==================== DADOS PARA GRÁFICOS ====================


// Contagem de orçamentos por status
$sqlStatus = "SELECT status, COUNT(*) as total FROM orcamentos GROUP BY status";
$resStatus = $pdo->query($sqlStatus)->fetchAll(PDO::FETCH_ASSOC);

$labels = [];
$dados = [];
foreach ($resStatus as $row) {
    $labels[] = $row['status'];
    $dados[] = $row['total'];
}



// 1. Total por status
$status_sql = "SELECT status, COUNT(*) AS qtd, SUM(oi.total) AS total
               FROM orcamentos o
               LEFT JOIN orcamento_itens oi ON oi.orcamento_id=o.id
               
               WHERE 1=1";
$status_params = [];
if ($fornecedor !== '') {
    $status_sql .= " AND o.fornecedor_id=?";
    $status_params[] = $fornecedor;
}
if ($data_inicio !== '') {
    $status_sql .= " AND o.data_orcamento>=?";
    $status_params[] = $data_inicio;
}
if ($data_fim !== '') {
    $status_sql .= " AND o.data_orcamento<=?";
    $status_params[] = $data_fim;
}

if ($materiaPri !== '') {
    $status_sql .= " AND oi.materiaPri_id=?";
    $status_params[] = $materiaPri;
}
$status_sql .= " GROUP BY status";
$st_status = $pdo->prepare($status_sql);


$st_status->execute($status_params);
$status_data = $st_status->fetchAll();

    echo '<pre>'; // Inicia o bloco de pré-formatação
    var_dump($status_data, $status_params); // Executa o var_dump
    echo '</pre>'; // Finaliza o bloco


// 2. Total por fornecedor
$fornecedor_sql = "SELECT f.nome_fantasia, SUM(oi.total) AS total
                   FROM orcamentos o
                   JOIN fornecedores f ON f.id=o.fornecedor_id
                   LEFT JOIN orcamento_itens oi ON oi.orcamento_id=o.id
                   WHERE 1=1
                   GROUP BY f.id";
$fornecedor_data = $pdo->query($fornecedor_sql)->fetchAll();

// 3. materiaPri mais vendidos
$materiaPri_sql = "SELECT p.nome, SUM(oi.quantidade) AS qtd
                 FROM orcamento_itens oi
                 JOIN materiaPri p ON p.id=oi.materiaPri_id
                 JOIN orcamentos o ON o.id=oi.orcamento_id
                 WHERE 1=1";
$prod_params = [];
if ($fornecedor !== '') {
    $materiaPri_sql .= " AND o.fornecedor_id=?";
    $prod_params[] = $fornecedor;
}
if ($status !== '') {
    $materiaPri_sql .= " AND o.status=?";
    $prod_params[] = $status;
}
if ($data_inicio !== '') {
    $materiaPri_sql .= " AND o.data_orcamento>=?";
    $prod_params[] = $data_inicio;
}
if ($data_fim !== '') {
    $materiaPri_sql .= " AND o.data_orcamento<=?";
    $prod_params[] = $data_fim;
}

if ($materiaPri !== '') {
    $materiaPri_sql .= " AND oi.materiaPri_id=?";
    $prod_params[] = $materiaPri;
}
$materiaPri_sql .= " GROUP BY p.id ORDER BY qtd DESC LIMIT 10";
$st_prod = $pdo->prepare($materiaPri_sql);
$st_prod->execute($prod_params);
$materiaPri_data = $st_prod->fetchAll();

// 4. Total mensal (linha)
$mes_sql = "SELECT DATE_FORMAT(o.data_orcamento,'%Y-%m') AS mes, SUM(oi.total) AS total
            FROM orcamentos o
            LEFT JOIN orcamento_itens oi ON oi.orcamento_id=o.id
            WHERE 1=1";
$mes_params = [];
if ($fornecedor !== '') {
    $mes_sql .= " AND o.fornecedor_id=?";
    $mes_params[] = $fornecedor;
}
if ($status !== '') {
    $mes_sql .= " AND o.status=?";
    $mes_params[] = $status;
}
if ($data_inicio !== '') {
    $mes_sql .= " AND o.data_orcamento>=?";
    $mes_params[] = $data_inicio;
}
if ($data_fim !== '') {
    $mes_sql .= " AND o.data_orcamento<=?";
    $mes_params[] = $data_fim;
}

if ($materiaPri !== '') {
    $mes_sql .= " AND oi.materiaPri_id=?";
    $mes_params[] = $materiaPri;
}
$mes_sql .= " GROUP BY mes ORDER BY mes ASC";
$st_mes = $pdo->prepare($mes_sql);
$st_mes->execute($mes_params);
$mes_data = $st_mes->fetchAll();

// 5. Valor total por materiaPri
$materiaPri_valor_sql = "SELECT p.nome, SUM(oi.total) AS valor
                       FROM orcamento_itens oi
                       JOIN materiaPri p ON p.id=oi.materiaPri_id
                       JOIN orcamentos o ON o.id=oi.orcamento_id
                       WHERE 1=1";
$prod_val_params = [];
if ($fornecedor !== '') {
    $materiaPri_valor_sql .= " AND o.fornecedor_id=?";
    $prod_val_params[] = $fornecedor;
}
if ($status !== '') {
    $materiaPri_valor_sql .= " AND o.status=?";
    $prod_val_params[] = $status;
}
if ($data_inicio !== '') {
    $materiaPri_valor_sql .= " AND o.data_orcamento>=?";
    $prod_val_params[] = $data_inicio;
}
if ($data_fim !== '') {
    $materiaPri_valor_sql .= " AND o.data_orcamento<=?";
    $prod_val_params[] = $data_fim;
}

if ($materiaPri !== '') {
    $materiaPri_valor_sql .= " AND oi.materiaPri_id=?";
    $prod_val_params[] = $materiaPri;
}
$materiaPri_valor_sql .= " GROUP BY p.id ORDER BY valor DESC LIMIT 10";
$st_prod_val = $pdo->prepare($materiaPri_valor_sql);
$st_prod_val->execute($prod_val_params);
$materiaPri_valor_data = $st_prod_val->fetchAll();




require '_header.php';
?>

<h3>Dashboard Completo de Orçamentos</h3>

<form class="row gy-2 gx-2 align-items-end mb-4">
    <div class="col-md-3">
        <label>Fornecedor</label>
        <select name="fornecedor" class="form-select">
            <option value="">Todos</option>
            <?php
            $fs = $pdo->query("SELECT id,nome_fantasia FROM fornecedores ORDER BY nome_fantasia")->fetchAll();
            foreach ($fs as $f): ?>
                <option value="<?= $f['id'] ?>" <?= $fornecedor == $f['id'] ? 'selected' : '' ?>><?= htmlspecialchars($f['nome_fantasia']) ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <div class="col-md-3">
        <label>materiaPri</label>
        <select name="materiaPri" class="form-select">
            <option value="">Todos</option>
            <?php
            $ps = $pdo->query("SELECT id,nome FROM materiaPri ORDER BY nome")->fetchAll();
            foreach ($ps as $p): ?>
                <option value="<?= $p['id'] ?>" <?= $materiaPri == $p['id'] ? 'selected' : '' ?>>
                    <?= htmlspecialchars($p['nome']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>

    <div class="col-md-2">
        <label>Status</label>
        <select name="status" class="form-select">
            <option value="">Todos</option>
            <option value="Em Andamento" <?= $status == 'Em Andamento' ? 'selected' : '' ?>>Em Andamento</option>
            <option value="Finalizado" <?= $status == 'Finalizado' ? 'selected' : '' ?>>Finalizado</option>
        </select>
    </div>
    <div class="col-md-2">
        <label>Data Início</label>
        <input type="date" name="data_inicio" value="<?= htmlspecialchars($data_inicio) ?>" class="form-control">
    </div>
    <div class="col-md-2">
        <label>Data Fim</label>
        <input type="date" name="data_fim" value="<?= htmlspecialchars($data_fim) ?>" class="form-control">
    </div>
    <div class="col-md-3">
        <button class="btn btn-primary">Filtrar</button>
        <a class="btn btn-outline-secondary" href="dashboard_orcamentos.php">Limpar</a>
    </div>
</form>
<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card p-3">
            <canvas id="statusChart"></canvas>
        </div>
    </div>

    <div class="col-md-6 mb-4">
  <h5 class="mb-3">📊 Quantidade de Orçamentos por Status</h5>
  <canvas id="graficoStatus" height="50"></canvas>
</div>


</div>



<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card p-3">
            <canvas id="materiaPriChart"></canvas>
        </div>
    </div>



    <div class="col-md-6 mb-4">
        <div class="card p-3">
            <canvas id="materiaPriValorChart"></canvas>
        </div>
    </div>

</div>

<div class="row">
    <div class="col-md-6 mb-4">
        <div class="card p-3">
            <canvas id="mesChart"></canvas>
        </div>
    </div>

    <div class="col-md-6 mb-4">
        <div class="card p-3">
            <canvas id="fornecedorChart"></canvas>
        </div>
    </div>
</div>


<div class="card p-3">
    <h5>Orçamentos Recentes</h5>
    <div class="table-responsive">
        <table class="table table-striped table-sm">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Fornecedor</th>
                    <th>Data</th>
                    <th>Status</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($orcamentos as $o): ?>
                    <tr>
                        <td><?= $o['id'] ?></td>
                        <td><?= htmlspecialchars($o['nome_fantasia']) ?></td>
                        <td><?= date('d/m/Y', strtotime($o['data_orcamento'])) ?></td>
                        <td>
                            <?php if ($o['status'] == 'Finalizado'): ?>
                                <span class="badge bg-success">Finalizado</span>
                            <?php else: ?>
                                <span class="badge bg-warning text-dark">Em Andamento</span>
                            <?php endif; ?>
                        </td>
                        <td>R$ <?= number_format($o['total'], 2, ',', '.') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script src="https://cdn.jsdelivr.net/npm/chartjs-plugin-datalabels@2"></script>
<script>

const ctx = document.getElementById('graficoStatus').getContext('2d');
new Chart(ctx, {
    type: 'pie',
    data: {
        labels: <?php echo json_encode($labels); ?>,
        datasets: [{
            label: 'Orçamentos',
            data: <?php echo json_encode($dados); ?>,
            backgroundColor: [
                '#FFC107', 
                '#198754',
                'rgba(75, 192, 192, 0.6)',
                'rgba(255, 99, 132, 0.6)'
            ],
            borderColor: '#fff',
            borderWidth: 2
        }]
    },
    options: {
        responsive: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});

    // Status Chart
    new Chart(document.getElementById('statusChart'), {
        type: 'doughnut',
        data: {
            labels: <?= json_encode(array_column($status_data, 'status')) ?>,
            datasets: [{
                data: <?= json_encode(array_map('intval', array_column($status_data, 'qtd'))) ?>,
                backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444']
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                datalabels: {
                    color: '#fff',
                    formatter: (value, ctx) => value
                }
            }
        },
        plugins: [ChartDataLabels]
    });

    // Fornecedor Chart (Bar)
    new Chart(document.getElementById('fornecedorChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($fornecedor_data, 'nome_fantasia')) ?>,
            datasets: [{
                label: 'Total em R$',
                data: <?= json_encode(array_map(function ($f) {
                            return floatval($f['total']);
                        }, $fornecedor_data)) ?>,
                backgroundColor: '#3b82f6'
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    display: false
                },
                datalabels: {
                    anchor: 'end',
                    align: 'end',
                    formatter: (value) => 'R$ ' + value.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        },
        plugins: [ChartDataLabels]
    });

    // materiaPri mais vendidos (Horizontal Bar)
    new Chart(document.getElementById('materiaPriChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($materiaPri_data, 'nome')) ?>,
            datasets: [{
                label: 'Quantidade',
                data: <?= json_encode(array_map(function ($p) {
                            return floatval($p['qtd']);
                        }, $materiaPri_data)) ?>,
                backgroundColor: '#f59e0b'
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                },
                datalabels: {
                    anchor: 'end',
                    align: 'end'
                }
            },
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        },
        plugins: [ChartDataLabels]
    });

    // Total mensal (Line)
    new Chart(document.getElementById('mesChart'), {
        type: 'line',
        data: {
            labels: <?= json_encode(array_column($mes_data, 'mes')) ?>,
            datasets: [{
                label: 'Total R$',
                data: <?= json_encode(array_map(function ($m) {
                            return floatval($m['total']);
                        }, $mes_data)) ?>,
                fill: true,
                backgroundColor: 'rgba(59,130,246,0.2)',
                borderColor: '#3b82f6',
                tension: 0.3
            }]
        },
        options: {
            responsive: true,
            plugins: {
                legend: {
                    position: 'bottom'
                },
                datalabels: {
                    anchor: 'end',
                    align: 'top',
                    formatter: (value) => 'R$ ' + value.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })
                }
            },
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        },
        plugins: [ChartDataLabels]
    });

    // Valor total por materiaPri (Horizontal Bar)
    new Chart(document.getElementById('materiaPriValorChart'), {
        type: 'bar',
        data: {
            labels: <?= json_encode(array_column($materiaPri_valor_data, 'nome')) ?>,
            datasets: [{
                label: 'Valor Total (R$)',
                data: <?= json_encode(array_map(function ($p) {
                            return floatval($p['valor']);
                        }, $materiaPri_valor_data)) ?>,
                backgroundColor: '#10b981'
            }]
        },
        options: {
            responsive: true,
            indexAxis: 'y',
            plugins: {
                legend: {
                    display: false
                },
                datalabels: {
                    anchor: 'end',
                    align: 'end',
                    formatter: (value) => 'R$ ' + value.toLocaleString('pt-BR', {
                        minimumFractionDigits: 2,
                        maximumFractionDigits: 2
                    })
                }
            },
            scales: {
                x: {
                    beginAtZero: true
                }
            }
        },
        plugins: [ChartDataLabels]
    });
</script>


<?php require '_footer.php'; ?>