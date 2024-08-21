<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "olimpiadas3");

if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

$result = $mysqli->query("SELECT producto_id, nombre, descripcion, precio FROM Productos");

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
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f4f4f4;
        }
        
        header {
            background-color: #333;
            color: #fff;
            padding: 10px 0;
            text-align: center;
        }

        header a {
            color: #fff;
            text-decoration: none;
            padding: 0 10px;
        }

        header a.button {
            background-color: #007bff;
            color: #fff;
            padding: 5px 10px;
            border-radius: 5px;
        }

        main {
            padding: 20px;
        }

        .product-list {
            list-style-type: none; /* Quita los puntos de la lista */
            padding: 0; /* Elimina el padding por defecto */
            margin: 0; /* Elimina el margen por defecto */
        }

        .product-item {
            border: 1px solid #ccc; /* Borde alrededor de cada producto */
            padding: 10px; /* Espaciado interno */
            margin-bottom: 10px; /* Espaciado entre productos */
            border-radius: 5px; /* Bordes redondeados */
            background-color: #fff; /* Fondo blanco para los productos */
        }

        .product-item h3 {
            margin: 0 0 10px; /* Margen debajo del título */
        }

        .product-item p {
            margin: 0 0 10px; /* Margen debajo del párrafo */
        }

        .product-item form {
            margin: 0; /* Elimina el margen por defecto en el formulario */
        }

        footer {
            background-color: #333;
            color: #fff;
            text-align: center;
            padding: 10px 0;
            position: fixed;
            width: 100%;
            bottom: 0;
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
        <ul class="product-list">
        <?php while ($row = $result->fetch_assoc()): ?>
            <li class="product-item">
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
            </li>
        <?php endwhile; ?>
        </ul>
    </main>
    <footer>
        <p>&copy; 2024 Tienda en Línea</p>
    </footer>
</body>
</html>

<?php
$mysqli->close();
?>
