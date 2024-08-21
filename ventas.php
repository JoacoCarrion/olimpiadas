<?php
require('fpdf186/fpdf.php'); // Ajusta la ruta según la ubicación de tu archivo FPDF

// Conectar a la base de datos
$mysqli = new mysqli("localhost", "root", "", "olimpiadas3");

if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

// Consulta para obtener los datos de ventas
$query = "SELECT venta_id, pedido_id, fecha_pedido, plata FROM ventas";
$result = $mysqli->query($query);

if ($result === FALSE) {
    die("Error en la consulta: " . $mysqli->error);
}

// Crear instancia de FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Títulos de las columnas
$pdf->Cell(40, 10, 'Venta ID', 1);
$pdf->Cell(40, 10, 'Pedido ID', 1);
$pdf->Cell(60, 10, 'Fecha Pedido', 1);
$pdf->Cell(40, 10, 'Plata', 1);
$pdf->Ln();

// Datos de las ventas
$pdf->SetFont('Arial', '', 12);

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(40, 10, $row['venta_id'], 1);
    $pdf->Cell(40, 10, $row['pedido_id'], 1);
    $pdf->Cell(60, 10, $row['fecha_pedido'], 1);
    $pdf->Cell(40, 10, '$' . number_format($row['plata'], 2), 1);
    $pdf->Ln();
}

// Cerrar la conexión a la base de datos
$mysqli->close();

// Output del PDF
$pdf->Output();
?>
