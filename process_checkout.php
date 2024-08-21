<?php
session_start();

// Verifica la ruta a la biblioteca FPDF
$fpdf_path = 'fpdf186/fpdf.php'; // Ajusta la ruta si es necesario

if (!file_exists($fpdf_path)) {
    die("La biblioteca FPDF no se encuentra en la ruta especificada.");
}

require($fpdf_path);

// Conéctate a la base de datos
$mysqli = new mysqli("localhost", "root", "", "olimpiadas3");

if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

// Verifica si hay productos en el carrito
if (!isset($_SESSION['carrito']) || empty($_SESSION['carrito'])) {
    die("No hay productos en el carrito.");
}

// Obtener el carrito de la sesión y el ID del cliente
$productos_en_carrito = $_SESSION['carrito'];
$cliente_id = $_SESSION['cliente_id'] ?? null;

if (!$cliente_id) {
    die("No se ha encontrado un cliente asociado a esta compra.");
}

// Si el formulario se ha enviado (es decir, el botón de realizar compra se ha presionado)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Insertar un nuevo pedido en la base de datos
    $query = "INSERT INTO pedidos (cliente_id, fecha_pedido, estado) VALUES (?, NOW(), 'Pendiente')";
    $stmt = $mysqli->prepare($query);

    if ($stmt) {
        $stmt->bind_param("i", $cliente_id);
        if ($stmt->execute()) {
            $pedido_id = $stmt->insert_id; // Obtener el ID del nuevo pedido

            // Crear un PDF con la factura
            $pdf = new FPDF();
            $pdf->AddPage();
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(0, 10, 'Factura de Compra', 0, 1, 'C');
            $pdf->Ln(10);

            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(80, 10, 'Producto', 1);
            $pdf->Cell(30, 10, 'Cantidad', 1);
            $pdf->Cell(30, 10, 'Precio', 1);
            $pdf->Cell(30, 10, 'Subtotal', 1);
            $pdf->Ln();

            $pdf->SetFont('Arial', '', 12);
            $total = 0;
            foreach ($productos_en_carrito as $producto_id => $cantidad) {
                $query = "SELECT nombre, precio FROM Productos WHERE producto_id = ?";
                $detalle_stmt = $mysqli->prepare($query);
                $detalle_stmt->bind_param("i", $producto_id);
                $detalle_stmt->execute();
                $result = $detalle_stmt->get_result();
                $producto = $result->fetch_assoc();
                $subtotal = $producto['precio'] * $cantidad;
                $total += $subtotal;

                // Añadir cada producto al PDF
                $pdf->Cell(80, 10, $producto['nombre'], 1);
                $pdf->Cell(30, 10, $cantidad, 1);
                $pdf->Cell(30, 10, '$' . number_format($producto['precio'], 2), 1);
                $pdf->Cell(30, 10, '$' . number_format($subtotal, 2), 1);
                $pdf->Ln();

                // Insertar el detalle del pedido en la tabla detalle_pedido
                $detalle_query = "INSERT INTO detalle_pedido (pedido_id, producto_id, cantidad, precio_unitario) VALUES (?, ?, ?, ?)";
                $detalle_stmt = $mysqli->prepare($detalle_query);
                if ($detalle_stmt) {
                    $detalle_stmt->bind_param("iiid", $pedido_id, $producto_id, $cantidad, $producto['precio']);
                    $detalle_stmt->execute();
                    $detalle_stmt->close();
                } else {
                    echo "Error al preparar la consulta de detalles: " . $mysqli->error;
                }
            }

            // Total en el PDF
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(140, 10, 'Total', 1);
            $pdf->Cell(30, 10, '$' . number_format($total, 2), 1);

            // Output del PDF al navegador
            $pdf->Output('I', 'factura.pdf');

            // Insertar datos en la tabla ventas
            $ventas_query = "INSERT INTO ventas (pedido_id, fecha_pedido, plata) VALUES (?, NOW(), ?)";
            $ventas_stmt = $mysqli->prepare($ventas_query);
            if ($ventas_stmt) {
                $ventas_stmt->bind_param("id", $pedido_id, $total);
                if (!$ventas_stmt->execute()) {
                    echo "Error al insertar en ventas: " . $ventas_stmt->error;
                }
                $ventas_stmt->close();
            } else {
                echo "Error en la preparación de la consulta de ventas: " . $mysqli->error;
            }

            // Actualizar el estado del pedido a 'Realizado'
            $update_pedido_query = "UPDATE pedidos SET estado = 'Realizado' WHERE pedido_id = ?";
            $update_pedido_stmt = $mysqli->prepare($update_pedido_query);
            if ($update_pedido_stmt) {
                $update_pedido_stmt->bind_param("i", $pedido_id);
                if (!$update_pedido_stmt->execute()) {
                    echo "Error al actualizar el estado del pedido: " . $update_pedido_stmt->error;
                }
                $update_pedido_stmt->close();
            } else {
                echo "Error en la preparación de la consulta de actualización de estado: " . $mysqli->error;
            }

            // Vaciar el carrito después de la compra
            $_SESSION['carrito'] = [];
        } else {
            echo "Error al procesar el pedido: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error en la preparación de la consulta: " . $mysqli->error;
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <style>
        /* Estilos aquí */
    </style>
</head>
<body>
    <header>
        <h1>Tu Carrito de Compras</h1>
    </header>
    <main>
        <h2>Productos en tu carrito</h2>
        <?php if (empty($productos_en_carrito)): ?>
            <p>No has agregado productos al carrito.</p>
        <?php else: ?>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Precio</th>
                        <th>Cantidad</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($productos_en_carrito as $producto_id => $cantidad): ?>
                        <tr>
                            <?php
                            $query = "SELECT nombre, precio FROM Productos WHERE producto_id = ?";
                            $detalle_stmt = $mysqli->prepare($query);
                            $detalle_stmt->bind_param("i", $producto_id);
                            $detalle_stmt->execute();
                            $result = $detalle_stmt->get_result();
                            $producto = $result->fetch_assoc();
                            ?>
                            <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                            <td>$<?php echo htmlspecialchars($producto['precio']); ?></td>
                            <td><?php echo htmlspecialchars($cantidad); ?></td>
                            <td>$<?php echo htmlspecialchars($producto['precio'] * $cantidad); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
            <form action="" method="post">
                <button type="submit" class="btn-checkout">Realizar Compra</button>
            </form>
        <?php endif; ?>
        <a href="index.php">Seguir comprando</a>
    </main>
    <footer>
        <p>&copy; 2024 Tienda en Línea</p>
    </footer>
</body>
</html>
