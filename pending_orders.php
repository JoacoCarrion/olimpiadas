<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "olimpiadas3");

if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

// Manejar las solicitudes de cambio de estado del pedido
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pedido_id = intval($_POST['pedido_id']);
    $nuevo_estado = $_POST['estado'];

    if ($nuevo_estado === "Entregado") {
        // Verificar si el pedido ya está en pedidos_historico
        $check_query = "
            SELECT COUNT(*) AS total FROM pedidos_historico WHERE pedido_id = ?";
        $stmt_check = $mysqli->prepare($check_query);
        if (!$stmt_check) {
            die("Error en la preparación de la consulta: " . $mysqli->error);
        }
        $stmt_check->bind_param("i", $pedido_id);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();
        $row_check = $result_check->fetch_assoc();
        $stmt_check->close();

        if ($row_check['total'] == 0) {
            // Insertar en la tabla pedidos_historico si no existe
            $insert_query = "
                INSERT INTO pedidos_historico (pedido_id, cliente_id, fecha_pedido)
                SELECT pedido_id, cliente_id, fecha_pedido FROM Pedidos WHERE pedido_id = ?";
            $stmt_insert = $mysqli->prepare($insert_query);
            if (!$stmt_insert) {
                die("Error en la preparación de la consulta: " . $mysqli->error);
            }
            $stmt_insert->bind_param("i", $pedido_id);
            if (!$stmt_insert->execute()) {
                die("Error al insertar en pedidos_historico: " . $mysqli->error);
            }
            $stmt_insert->close();
        }
    }

    // Actualizar el estado del pedido en la tabla pedidos
    $query = "UPDATE Pedidos SET estado = ? WHERE pedido_id = ?";
    $stmt = $mysqli->prepare($query);
    if (!$stmt) {
        die("Error en la preparación de la consulta: " . $mysqli->error);
    }
    $stmt->bind_param("si", $nuevo_estado, $pedido_id);
    if (!$stmt->execute()) {
        die("Error al actualizar el estado: " . $mysqli->error);
    }
    $stmt->close();

    // Redirigir a la misma página para evitar reenvío del formulario
    header("Location: " . $_SERVER['PHP_SELF']);
    exit();
}

// Consulta para obtener todos los pedidos, excepto los entregados
$query = "SELECT pedido_id, cliente_id, fecha_pedido, estado FROM Pedidos WHERE estado != 'Entregado'";
$result = $mysqli->query($query);

if ($result === FALSE) {
    die("Error en la consulta: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pedidos</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .button {
            background-color: #f0f0f0;
            color: #333;
            border: 1px solid #ddd;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            cursor: pointer;
        }
        .anulado-button {
            background-color: #FFC107;
            color: white;
        }
        .entregado-button {
            background-color: #4CAF50;
            color: white;
        }
        .pendiente-button {
            background-color: #2196F3;
            color: white;
        }
    </style>
</head>
<body>
    <header>
        <h1>Pedidos</h1>
        <a href="admin.php" class="button">Volver al Panel de Administración</a>
    </header>
    <main>
        <h2>Pedidos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Pedido</th>
                    <th>ID Cliente</th>
                    <th>Fecha del Pedido</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo isset($row['pedido_id']) ? htmlspecialchars($row['pedido_id']) : 'N/A'; ?></td>
                        <td><?php echo isset($row['cliente_id']) ? htmlspecialchars($row['cliente_id']) : 'N/A'; ?></td>
                        <td><?php echo isset($row['fecha_pedido']) ? htmlspecialchars($row['fecha_pedido']) : 'N/A'; ?></td>
                        <td><?php echo isset($row['estado']) ? htmlspecialchars($row['estado']) : 'Pendiente'; ?></td>
                        <td>
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="pedido_id" value="<?php echo htmlspecialchars($row['pedido_id']); ?>">
                                <input type="hidden" name="estado" value="Anulado">
                                <button type="submit" class="button anulado-button">Anular</button>
                            </form>
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="pedido_id" value="<?php echo htmlspecialchars($row['pedido_id']); ?>">
                                <input type="hidden" name="estado" value="Entregado">
                                <button type="submit" class="button entregado-button">Entregado</button>
                            </form>
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="pedido_id" value="<?php echo htmlspecialchars($row['pedido_id']); ?>">
                                <input type="hidden" name="estado" value="Pendiente">
                                <button type="submit" class="button pendiente-button">Pendiente</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </main>
    <footer>
        <p>&copy; 2024 Tienda en Línea</p>
    </footer>
</body>
</html>

<?php
$mysqli->close();
?>
    