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

        $query = " SELECT 
    s.supplier_name AS 'Supplier Name',
    s.supplier_location AS 'Supplier Location',
    s.email AS 'Contact Details',
    GROUP_CONCAT(DISTINCT p.product_name SEPARATOR '\n') AS 'Products',
    CONCAT(u.first_name,' ',u.last_name) AS 'Created By',
    s.created_at AS 'Created At',
    s.updated_at AS 'Updated At'
FROM supplier s
LEFT JOIN users u 
    ON s.created_by = u.id
LEFT JOIN productsupplier ps 
    ON s.id = ps.supplier
LEFT JOIN products p 
    ON ps.product = p.id
GROUP BY s.id
ORDER BY s.id";
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
   DYNAMIC PDF EXPORT
======================== */

if ($format == "pdf") {

    class PDF extends FPDF
    {
        function Header()
        {
            $logoWidth = 35;
            $pageWidth = $this->GetPageWidth();

            $x = ($pageWidth - $logoWidth) / 2;
            $this->Image('../images/logo.png', $x, 8, $logoWidth);

            $this->Ln(15);

            $this->SetFont('Arial', 'B', 16);
            $this->Cell(0, 10, 'VyaparTrack Inventory System', 0, 1, 'C');

            $this->SetFont('Arial', '', 11);
            $this->Cell(0, 6, ucfirst($GLOBALS['type']) . ' Report', 0, 1, 'C');

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


    /* ----------------------
COLUMN WIDTHS
----------------------- */

    $columnCount = count($rows[0]);

    /* Custom widths for products report */
    if ($type == "products") {
        $widths = [35, 40, 65, 40, 40, 35, 35];
    } else {

        /* dynamic widths for other reports */
        $pageWidth = 277;
        $cellWidth = $pageWidth / $columnCount;

        $widths = [];
        for ($i = 0; $i < $columnCount; $i++) {
            $widths[] = $cellWidth;
        }
    }


    /* ----------------------
CENTER TABLE
----------------------- */

    $tableWidth = array_sum($widths);
    $pageRealWidth = $pdf->GetPageWidth();
    $centerX = ($pageRealWidth - $tableWidth) / 2;

    $pdf->SetX($centerX);


    /* ----------------------
TABLE HEADER
----------------------- */

    $i = 0;

    foreach (array_keys($rows[0]) as $column) {

        $pdf->SetFillColor(230, 230, 230);
        $pdf->Cell($widths[$i], 10, $column, 1, 0, 'C', true);

        $i++;
    }

    $pdf->Ln();

    $pdf->SetFont('Arial', '', 9);


    /* ----------------------
TABLE ROWS
----------------------- */

    foreach ($rows as $row) {

        $pdf->SetX($centerX);

        $lineHeight = 6;

        /* Row height calculation */
        $desc = $row['Description'] ?? '';
        $nbLines = ceil($pdf->GetStringWidth($desc) / ($widths[2] ?? 40));

        if ($nbLines < 1) $nbLines = 1;

        $rowHeight = $nbLines * $lineHeight;

        if ($rowHeight < 20) $rowHeight = 20;

        $yStart = $pdf->GetY();

        $colIndex = 0;

        foreach ($row as $key => $value) {

            $width = $widths[$colIndex] ?? 40;


            /* IMAGE COLUMN */

            if ($type == 'products' && $key == 'Image') {

                $imageFile = "../uploads/products/" . $value;

                $pdf->Cell($width, $rowHeight, '', 1);

                if (!empty($value) && file_exists($imageFile)) {

                    $ext = strtolower(pathinfo($imageFile, PATHINFO_EXTENSION));

                    if ($ext == 'webp') {

                        $webp = imagecreatefromwebp($imageFile);
                        $tempImage = "../uploads/products/temp_" . uniqid() . ".png";

                        imagepng($webp, $tempImage);
                        imagedestroy($webp);

                        $pdf->Image(
                            $tempImage,
                            $pdf->GetX() - $width + ($width / 2) - 7,
                            $yStart + ($rowHeight / 2) - 7,
                            15
                        );

                        unlink($tempImage);
                    } else {

                        $pdf->Image(
                            $imageFile,
                            $pdf->GetX() - $width + ($width / 2) - 7,
                            $yStart + ($rowHeight / 2) - 7,
                            15
                        );
                    }
                }
            }


            /* DESCRIPTION COLUMN */ elseif ($key == 'Description') {

                $text = substr($value, 0, 50); // limit length to keep row height clean

                $pdf->Cell($width, $rowHeight, $text, 1, 0, 'L');
            }


            /* NORMAL CELLS */ else {

                $pdf->Cell($width, $rowHeight, $value, 1, 0, 'C');
            }

            $colIndex++;
        }

        $pdf->Ln($rowHeight);
    }


    /* ----------------------
FILE NAME
----------------------- */

    $filename = $type . '_report_' . date('Ymd_His') . '.pdf';

    $pdf->Output($filename, 'D');

    exit;
}
