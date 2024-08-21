<?php
session_start();

// Verifica que el usuario esté autenticado y sea administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Conéctate a la base de datos
$mysqli = new mysqli("localhost", "root", "", "olimpiadas3");

if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

// Procesar la solicitud para marcar una venta como cobrada
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['venta_id'])) {
    $venta_id = intval($_POST['venta_id']);
    $update_query = "UPDATE ventas SET cobrado = 'Cobrado' WHERE venta_id = ?";
    $update_stmt = $mysqli->prepare($update_query);
    $update_stmt->bind_param("i", $venta_id);

    if ($update_stmt->execute()) {
        echo "Venta marcada como cobrada.";
    } else {
        echo "Error al actualizar la venta: " . $update_stmt->error;
    }

    $update_stmt->close();
}

// Consulta para obtener los datos de la tabla ventas
$query = "SELECT venta_id, pedido_id, fecha_pedido, plata, cobrado FROM ventas";
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
    <title>Estado de Ventas</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        table {
            width: 100%;
            border-collapse: collapse;
        }
        table, th, td {
            border: 1px solid black;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 5px 10px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 12px;
            margin: 2px 1px;
            cursor: pointer;
        }
    </style>
</head>
<body>
    <header>
        <h1>Estado de Ventas</h1>
        <nav>
            <a href="admin.php">Volver al Panel de Administración</a>
        </nav>
    </header>
    <main>
        <h2>Listado de Ventas</h2>
        <table>
            <thead>
                <tr>
                    <th>Venta ID</th>
                    <th>Pedido ID</th>
                    <th>Fecha del Pedido</th>
                    <th>Plata</th>
                    <th>Cobrado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['venta_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['pedido_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['fecha_pedido']); ?></td>
                        <td><?php echo htmlspecialchars($row['plata']); ?></td>
                        <td><?php echo htmlspecialchars($row['cobrado']); ?></td>
                        <td>
                            <?php if ($row['cobrado'] === 'a Cobrar'): ?>
                                <form action="" method="post" style="display:inline;">
                                    <input type="hidden" name="venta_id" value="<?php echo htmlspecialchars($row['venta_id']); ?>">
                                    <button type="submit" class="button">Cobrado</button>
                                </form>
                            <?php endif; ?>
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
