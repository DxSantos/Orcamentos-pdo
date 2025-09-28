<?php
require __DIR__ . '/../config.php';
require __DIR__ . '/../fpdf.php'; // Certifique-se que FPDF está na pasta vendor/fpdf

$id = isset($_GET['id']) ? (int) $_GET['id'] : null;
if (!$id) exit('Orçamento inválido.');

// Buscar orçamento
$st = $pdo->prepare("SELECT o.*, f.nome_fantasia, f.razao_social, f.endereco, f.cidade, f.uf, f.cep 
                     FROM orcamentos o
                     JOIN fornecedores f ON f.id=o.fornecedor_id
                     WHERE o.id=?");
$st->execute([$id]);
$orcamento = $st->fetch(PDO::FETCH_ASSOC);

// Buscar itens
$sti = $pdo->prepare("SELECT oi.*, p.nome 
                      FROM orcamento_itens oi 
                      LEFT JOIN produtos p ON p.id=oi.produto_id 
                      WHERE oi.orcamento_id=?");
$sti->execute([$id]);
$itens = $sti->fetchAll(PDO::FETCH_ASSOC);

// Criar PDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial','B',14);
$pdf->Cell(0,10,'Orcamento #'.$id,0,1,'C');
$pdf->SetFont('Arial','',12);

// Dados do fornecedor
$pdf->Cell(0,6,'Fornecedor: '.$orcamento['nome_fantasia'],0,1);
$pdf->Cell(0,6,'Endereco: '.$orcamento['endereco'].' - '.$orcamento['cidade'].'/'.$orcamento['uf'],0,1);
$pdf->Cell(0,6,'CEP: '.$orcamento['cep'],0,1);
$pdf->Cell(0,6,'Status: '.$orcamento['status'],0,1);
$pdf->Ln(5);

// Tabela de itens
$pdf->SetFont('Arial','B',12);
$pdf->Cell(80,7,'Produto',1);
$pdf->Cell(20,7,'Qtd',1);
$pdf->Cell(30,7,'Preco',1);
$pdf->Cell(30,7,'Total',1);
$pdf->Ln();

$pdf->SetFont('Arial','',12);
$totalGeral = 0;
foreach($itens as $i){
    $total = $i['quantidade'] * $i['preco_unitario'];
    $totalGeral += $total;
    $pdf->Cell(80,6,$i['nome'],1);
    $pdf->Cell(20,6,$i['quantidade'],1,0,'C');
    $pdf->Cell(30,6,'R$ '.number_format($i['preco_unitario'],2,',','.'),1,0,'R');
    $pdf->Cell(30,6,'R$ '.number_format($total,2,',','.'),1,0,'R');
    $pdf->Ln();
}

// Total geral
$pdf->Cell(130,7,'Total Geral',1);
$pdf->Cell(30,7,'R$ '.number_format($totalGeral,2,',','.'),1,0,'R');

// Salvar PDF na pasta pdfs
$pdfFile = __DIR__ . "/../pdfs/orcamento_$id.pdf";
$pdf->Output('F', $pdfFile);

// Abrir PDF
header("Location: ../pdfs/orcamento_$id.pdf");
exit;
?>
