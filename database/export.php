<?php

include('connection.php');
require_once __DIR__ . '/SimpleXLSXGen.php';
require_once __DIR__ . '/../fpdf186/fpdf.php';

use Shuchkin\SimpleXLSXGen;

$type   = $_GET['type']   ?? '';
$format = $_GET['format'] ?? '';
$from   = $_GET['from']   ?? '';
$to     = $_GET['to']     ?? '';

/* =========================
   BUILD DATE FILTER
========================= */

$dateFilter = "";

if ($from && $to) {
    $dateFilter = " WHERE DATE(created_at) BETWEEN '$from' AND '$to' ";
}

/* =========================
   REPORT QUERIES
========================= */

switch ($type) {

    case "products":

        $query = "
SELECT
p.img AS Image,
p.product_name AS Product,
p.description AS Description,
(
    SELECT s.supplier_name
    FROM productsupplier ps
    LEFT JOIN supplier s ON s.id = ps.supplier
    WHERE ps.product = p.id
    LIMIT 1
) AS Supplier,
CONCAT(u.first_name,' ',u.last_name) AS Created_By,
DATE_FORMAT(p.created_at,'%d-%m-%Y %H:%i') AS Created_At,
DATE_FORMAT(p.updated_at,'%d-%m-%Y %H:%i') AS Updated_At
FROM products p
LEFT JOIN users u ON u.id = p.created_by
";

        break;


    case "suppliers":

        $query = "
SELECT
s.supplier_name AS Supplier,
s.supplier_location AS Location,
s.email AS Email,
CONCAT(u.first_name,' ',u.last_name) AS Created_By,
DATE_FORMAT(s.created_at,'%d-%m-%Y %H:%i') AS Created_At,
DATE_FORMAT(s.updated_at,'%d-%m-%Y %H:%i') AS Updated_At
FROM supplier s
LEFT JOIN users u ON u.id = s.created_by
$dateFilter
";

        break;

        break;


    case "orders":

        $query = "
SELECT
p.product_name AS Product,
s.supplier_name AS Supplier,
ps.quantity_order AS Qty_Ordered,
ps.quantity_received AS Qty_Received,
ps.quantity_remaining AS Qty_Remaining,
UPPER(ps.stats) AS Status,
CONCAT(u.first_name,' ',u.last_name) AS Ordered_By,
DATE_FORMAT(ps.created_at,'%d-%m-%Y %H:%i') AS Created_At,
DATE_FORMAT(ps.updated_at,'%d-%m-%Y %H:%i') AS Updated_At
FROM productsupplier ps
LEFT JOIN products p ON p.id = ps.product
LEFT JOIN supplier s ON s.id = ps.supplier
LEFT JOIN users u ON u.id = ps.created_by
$dateFilter
";

        break;


    case "deliveries":

        $query = "
SELECT
order_id AS Order_ID,
quantity_received AS Quantity,
DATE_FORMAT(date_received,'%d-%m-%Y %H:%i') AS Date_Received
FROM delivery_history
";

        break;


    default:
        die("Invalid Report");
}

/* =========================
   FETCH DATA
========================= */

$stmt = $conn->prepare($query);
$stmt->execute();
$rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

/* =========================
   EXPORT EXCEL
========================= */

if ($format == "excel") {

    $data = [];

    /* REPORT TITLE */

    $data[] = ["VyaparTrack Inventory Report"];
    $data[] = ["Report Type: " . ucfirst($type)];
    $data[] = ["Generated On: " . date("d-m-Y H:i")];
    $data[] = []; // empty row

    /* TABLE HEADER */

    if (!empty($rows)) {
        $data[] = array_keys($rows[0]);
    }

    /* TABLE DATA */

    foreach ($rows as $row) {
        $data[] = array_values($row);
    }

    $xlsx = SimpleXLSXGen::fromArray($data);

    /* BASIC STYLING */

    $xlsx->setDefaultFont('Calibri');
    $xlsx->downloadAs($type . "_report.xlsx");

    exit;
}

/* ========================
   PROFESSIONAL PDF EXPORT
======================== */

if ($format == "pdf") {

    class PDF extends FPDF
    {

        function Header()
        {

            $logoWidth = 35;
            $pageWidth = $this->GetPageWidth();

            /* CENTER LOGO */

            $x = ($pageWidth - $logoWidth) / 2;

            $this->Image('../images/logo.png', $x, 8, $logoWidth);

            /* SPACE BELOW LOGO */

            $this->Ln(15);

            /* TITLE */

            $this->SetFont('Arial', 'B', 16);
            $this->Cell(0, 10, 'VyaparTrack Inventory System', 0, 1, 'C');

            $this->SetFont('Arial', '', 11);
            $this->Cell(0, 6, 'Inventory Report', 0, 1, 'C');

            $this->Ln(5);
        }

        function Footer()
        {

            $this->SetY(-15);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 10, 'Page ' . $this->PageNo() . '/{nb}', 0, 0, 'C');
        }
    }

    $pdf = new PDF('L', 'mm', 'A4');
    $pdf->AliasNbPages();
    $pdf->AddPage();

    $pdf->SetFont('Arial', 'B', 9);

    /* COLUMN WIDTHS */

    $widths = [30, 35, 80, 35, 45, 35, 35];

    /* TABLE HEADER */

    if (!empty($rows)) {

        $i = 0;

        foreach (array_keys($rows[0]) as $column) {
            $pdf->SetFillColor(230, 230, 230);
            $pdf->Cell($widths[$i], 10, $column, 1, 0, 'C', true);
            $i++;
        }

        $pdf->Ln();

        $pdf->SetFont('Arial', '', 9);

        /* TABLE DATA */

        foreach ($rows as $row) {

            $xStart = $pdf->GetX();
            $yStart = $pdf->GetY();

            /* IMAGE COLUMN */

            $imageName = $row['Image'] ?? '';

            $imageFile = "../uploads/products/" . $imageName;

            $pdf->Cell($widths[0], 20, '', 1);

            if (!empty($imageName) && file_exists($imageFile)) {

                $ext = strtolower(pathinfo($imageFile, PATHINFO_EXTENSION));

                if ($ext == 'webp') {

                    $webp = imagecreatefromwebp($imageFile);

                    $tempImage = "../uploads/products/temp_" . $row['Product'] . ".png";

                    imagepng($webp, $tempImage);

                    imagedestroy($webp);

                    $pdf->Image($tempImage, $xStart + 2, $yStart + 2, 15);
                } else {

                    $pdf->Image($imageFile, $xStart + 2, $yStart + 2, 15);
                }
            } else {

                $pdf->SetXY($xStart + 2, $yStart + 7);
                $pdf->SetFont('Arial', 'I', 8);
                $pdf->Cell(30, 5, 'No Image');
            }
            $pdf->SetXY($xStart + $widths[0], $yStart);

            /* PRODUCT */

            $pdf->Cell($widths[1], 20, $row['Product'], 1);

            /* DESCRIPTION (WRAPPED) */

            $xCurrent = $pdf->GetX();
            $yCurrent = $pdf->GetY();

            $pdf->MultiCell($widths[2], 5, $row['Description'], 1);

            $yAfterDesc = $pdf->GetY();

            $pdf->SetXY($xCurrent + $widths[2], $yCurrent);

            /* SUPPLIER */

            $pdf->Cell($widths[3], 20, $row['Supplier'], 1);

            /* CREATED BY */

            $pdf->Cell($widths[4], 20, $row['Created_By'], 1);

            /* CREATED AT */

            $pdf->Cell($widths[5], 20, $row['Created_At'], 1);

            /* UPDATED AT */
            $pdf->Cell($widths[6], 20, $row['Updated_At'], 1, 0, 'C');

            $pdf->Ln();
        }
    }

    $pdf->Output($type . '_report.pdf', 'D');

    exit;
}
