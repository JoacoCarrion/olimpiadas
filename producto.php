<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "olimpiadas3");

// Verificar la conexión a la base de datos
if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

// Obtener los productos
$result = $mysqli->query("SELECT producto_id, nombre, descripcion, precio FROM Productos");

// Verificar si la consulta tuvo éxito
if ($result === FALSE) {
    die("Error en la consulta: " . $mysqli->error);
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Productos</title>
    <link rel="stylesheet" href="css/styles.css">
    <style>
        .carousel {
            display: flex;
            flex-direction: column;
            align-items: center;
        }
        .carousel-item {
            border: 1px solid #ccc;
            padding: 10px;
            margin: 10px 0;
            width: 80%;
            max-width: 600px;
            text-align: center;
            background-color: #f9f9f9;
        }
        .carousel-item h3 {
            margin: 0 0 10px;
        }
        .carousel-item p {
            margin: 5px 0;
        }
        .button {
            text-decoration: none;
            color: #007bff;
        }
    </style>
</head>
<body>
    <header>
        <h1>Lista de Productos</h1>
        <?php if (isset($_SESSION['usuario'])): ?>
            <p>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario']); ?>! <a href="logout.php">Cerrar sesión</a></p>
        <?php else: ?>
            <p><a href="login.php" class="button">Iniciar sesión</a> para agregar productos al carrito</p>
        <?php endif; ?>
    </header>
    <main>
        <h2>Productos Disponibles</h2>
        <div class="carousel">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="carousel-item">
                <h3><?php echo htmlspecialchars($row['nombre']); ?></h3>
                <p><?php echo htmlspecialchars($row['descripcion']); ?></p>
                <p>Precio: $<?php echo htmlspecialchars($row['precio']); ?></p>
                <?php if (isset($_SESSION['usuario'])): ?>
                    <form action="add_to_cart.php" method="post">
                        <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($row['producto_id']); ?>">
                        <button type="submit">Agregar al Carrito</button>
                    </form>
                <?php else: ?>
                    <p><em>Inicia sesión para agregar al carrito.</em></p>
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
        </div>
    </main>
    <footer>
        <p>&copy; 2024 Tienda en Línea</p>
    </footer>
</body>
</html>

<?php
$mysqli->close();
?>
