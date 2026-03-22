<?php

session_start();
include('connection.php');
require_once __DIR__ . '/../fpdf186/fpdf.php';

$data = json_decode(file_get_contents("php://input"), true);

$cart = $data['cart'];
$customer = $data['customer'];

$subtotal = 0;
$items = [];

foreach ($cart as $id => $item) {

    $qty = $item['qty'];

    $stmt = $conn->prepare("SELECT * FROM products WHERE id=?");
    $stmt->execute([$id]);
    $p = $stmt->fetch(PDO::FETCH_ASSOC);

    $total = $p['price'] * $qty;

    $items[] = [
        'name' => $p['product_name'],
        'qty' => $qty,
        'price' => $p['price'],
        'total' => $total
    ];

    $subtotal += $total;
}

$gst = $subtotal * 0.18;
$grand = $subtotal + $gst;

/* PDF */
$pdf = new FPDF();
$pdf->AddPage();

/* LOGO CENTER */
$pdf->Image('../images/logo.png', 80, 10, 50);

$pdf->Ln(35);

$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'VyaparTrack Invoice', 0, 1, 'C');

$pdf->Ln(5);

$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 8, 'Customer: ' . $customer['name'], 0, 1);
$pdf->Cell(0, 8, 'Phone: ' . $customer['phone'], 0, 1);

$pdf->Ln(5);

/* TABLE */
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(70, 10, 'Product', 1);
$pdf->Cell(20, 10, 'Qty', 1);
$pdf->Cell(30, 10, 'Price', 1);
$pdf->Cell(40, 10, 'Total', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);

foreach ($items as $i) {
    $pdf->Cell(70, 10, $i['name'], 1);
    $pdf->Cell(20, 10, $i['qty'], 1);
    $pdf->Cell(30, 10, 'Rs ' . $i['price'], 1);
    $pdf->Cell(40, 10, 'Rs ' . $i['total'], 1);
    $pdf->Ln();
}

$pdf->Ln(10);

/* TOTAL BOX */
$pdf->Cell(120);
$pdf->Cell(40, 10, 'Subtotal', 1);
$pdf->Cell(30, 10, $subtotal, 1);
$pdf->Ln();

$pdf->Cell(120);
$pdf->Cell(40, 10, 'GST 18%', 1);
$pdf->Cell(30, 10, $gst, 1);
$pdf->Ln();

$pdf->Cell(120);
$pdf->Cell(40, 10, 'Total', 1);
$pdf->Cell(30, 10, $grand, 1);

$pdf->Ln(10);

$pdf->Cell(0, 10, 'Thank You Visit Again!', 0, 1, 'C');

$pdf->Output();
