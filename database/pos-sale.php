<?php

session_start();
include('connection.php');

require_once __DIR__ . '/../fpdf186/fpdf.php';

/* ================= GET DATA ================= */

$data = json_decode(file_get_contents("php://input"), true);

$cart = $data['cart'] ?? [];
$customer = $data['customer'] ?? [];

$name = $customer['name'] ?? 'Walk-in Customer';
$phone = $customer['phone'] ?? '-';
$gst = $customer['gst'] ?? '-';

/* ================= VALIDATION ================= */

if (empty($cart)) {
    die("Cart is empty");
}

/* ================= STOCK UPDATE ================= */

try {

    $conn->beginTransaction();

    foreach ($cart as $product_id => $item) {

        $qty = intval($item['qty']);

        // 🔥 CHECK CURRENT STOCK
        $stmt = $conn->prepare("SELECT quantity FROM stock WHERE product_id = ?");
        $stmt->execute([$product_id]);
        $current_stock = $stmt->fetchColumn();

        if ($current_stock < $qty) {
            throw new Exception("Not enough stock for product ID: " . $product_id);
        }

        // 🔥 REDUCE STOCK
        $stmt = $conn->prepare("
            UPDATE stock 
            SET quantity = quantity - ? 
            WHERE product_id = ?
        ");
        $stmt->execute([$qty, $product_id]);

        // 🔥 OPTIONAL: STORE SALES HISTORY
        $stmt = $conn->prepare("
            INSERT INTO sales (product_id, quantity, created_at)
            VALUES (?, ?, NOW())
        ");
        $stmt->execute([$product_id, $qty]);
    }

    $conn->commit();
} catch (Exception $e) {

    $conn->rollBack();
    die("Error: " . $e->getMessage());
}


/* ================= CALCULATIONS ================= */

$subtotal = 0;

foreach ($cart as $item) {
    $subtotal += $item['price'] * $item['qty'];
}

$gst_amount = $subtotal * 0.18;
$total = $subtotal + $gst_amount;


/* ================= PDF INVOICE ================= */

$pdf = new FPDF();
$pdf->AddPage();

/* LOGO */
$pdf->Image(__DIR__ . '/../images/logo.png', 80, 10, 50);

/* TITLE */
$pdf->Ln(30);
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(0, 10, 'TAX INVOICE', 0, 1, 'C');

/* COMPANY DETAILS */
$pdf->SetFont('Arial', '', 10);
$pdf->Cell(0, 6, 'VyaparTrack Pvt Ltd', 0, 1, 'C');
$pdf->Cell(0, 6, 'Mumbai, India', 0, 1, 'C');
$pdf->Cell(0, 6, 'GSTIN: 27ABCDE1234F1Z5', 0, 1, 'C');

$pdf->Ln(5);

/* CUSTOMER DETAILS */
$pdf->SetFont('Arial', '', 11);
$pdf->Cell(0, 6, 'Customer: ' . $name, 0, 1);
$pdf->Cell(0, 6, 'Phone: ' . $phone, 0, 1);
$pdf->Cell(0, 6, 'Customer GST: ' . $gst, 0, 1);

$pdf->Ln(5);

/* TABLE HEADER */
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(60, 8, 'Product', 1);
$pdf->Cell(30, 8, 'Price', 1);
$pdf->Cell(30, 8, 'Qty', 1);
$pdf->Cell(40, 8, 'Total', 1);
$pdf->Ln();

/* TABLE DATA */
$pdf->SetFont('Arial', '', 11);

foreach ($cart as $item) {

    $line_total = $item['price'] * $item['qty'];

    $pdf->Cell(60, 8, $item['name'], 1);
    $pdf->Cell(30, 8, 'Rs ' . number_format($item['price'], 2), 1);
    $pdf->Cell(30, 8, $item['qty'], 1);
    $pdf->Cell(40, 8, 'Rs ' . number_format($line_total, 2), 1);
    $pdf->Ln();
}

/* TOTALS */
$pdf->Ln(5);

$pdf->Cell(120, 8, '', 0);
$pdf->Cell(30, 8, 'Subtotal', 1);
$pdf->Cell(40, 8, 'Rs ' . number_format($subtotal, 2), 1);
$pdf->Ln();

$pdf->Cell(120, 8, '', 0);
$pdf->Cell(30, 8, 'GST (18%)', 1);
$pdf->Cell(40, 8, 'Rs ' . number_format($gst_amount, 2), 1);
$pdf->Ln();

$pdf->Cell(120, 8, '', 0);
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(30, 10, 'Total', 1);
$pdf->Cell(40, 10, 'Rs ' . number_format($total, 2), 1);
$pdf->Ln();

/* FOOTER */
$pdf->Ln(10);
$pdf->SetFont('Arial', 'I', 10);
$pdf->Cell(0, 6, 'Thank you for your business!', 0, 1, 'C');

/* OUTPUT PDF */
$pdf->Output('D', 'invoice.pdf');
exit();
