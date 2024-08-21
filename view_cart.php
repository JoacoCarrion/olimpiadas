<?php
session_start();

if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

// Conexión a la base de datos
$servername = "localhost";
$username = "root"; // Cambia esto según tu configuración
$password = ""; // Cambia esto según tu configuración
$dbname = "olimpiadas3"; // Usar la base de datos olimpiadas3

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Obtener ID del usuario
$usuario_id = $_SESSION['usuario_id'];

// Obtener productos en el carrito
$sql = "SELECT p.codigo_producto, p.descripcion, p.precio, c.cantidad 
        FROM carrito c 
        JOIN productos p ON c.producto_id = p.producto_id 
        WHERE c.usuario_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $usuario_id);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Carrito de Compras</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Carrito de Compras</h1>
        <nav>
            <a href="logout.php">Cerrar sesión</a>
        </nav>
    </header>
    <main>
        <h2>Productos en tu Carrito</h2>
        <table>
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Cantidad</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['codigo_producto']); ?></td>
                    <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                    <td><?php echo htmlspecialchars($row['precio']); ?></td>
                    <td><?php echo htmlspecialchars($row['cantidad']); ?></td>
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
        <a href="checkout.php">Proceder al Pago</a>
    </main>
    <footer>
        <p>&copy; 2024 Tienda en Línea</p>
    </footer>
</body>
</html>

<?php
$stmt->close();
$conn->close();
?>
