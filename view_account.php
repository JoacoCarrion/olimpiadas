<?php
require('fpdf186/fpdf.php'); // Asegúrate de ajustar la ruta según la ubicación de tu archivo FPDF

// Conectar a la base de datos
$mysqli = new mysqli("localhost", "root", "", "olimpiadas3");

if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

// Consulta para obtener los datos necesarios
$query = "
    SELECT c.cliente_id, c.nombre_usuario, p.pedido_id, p.estado
    FROM clientes c
    JOIN pedidos p ON c.cliente_id = p.cliente_id
";

$result = $mysqli->query($query);

if ($result === FALSE) {
    die("Error en la consulta: " . $mysqli->error);
}

// Crear instancia de FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 12);

// Títulos de las columnas
$pdf->Cell(30, 10, 'Cliente ID', 1);
$pdf->Cell(60, 10, 'Nombre Usuario', 1);
$pdf->Cell(40, 10, 'Pedido ID', 1);
$pdf->Cell(30, 10, 'Estado', 1);
$pdf->Ln();

// Datos de los pedidos
$pdf->SetFont('Arial', '', 12);

while ($row = $result->fetch_assoc()) {
    $pdf->Cell(30, 10, $row['cliente_id'], 1);
    $pdf->Cell(60, 10, $row['nombre_usuario'], 1);
    $pdf->Cell(40, 10, $row['pedido_id'], 1);
    $pdf->Cell(30, 10, $row['estado'], 1);
    $pdf->Ln();
}

// Cerrar la conexión a la base de datos
$mysqli->close();

// Output del PDF
$pdf->Output();
?>
