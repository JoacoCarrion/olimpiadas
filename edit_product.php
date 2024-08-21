<?php
session_start();

// Verificar si el usuario está autenticado y tiene el rol de administrador
if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Conectar a la base de datos
$mysqli = new mysqli("localhost", "root", "", "olimpiadas3");

// Comprobar la conexión
if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

// Obtener la lista de productos
$query = "SELECT producto_id, codigo_producto, nombre, descripcion, precio FROM Productos";
$result = $mysqli->query($query);

if ($result === FALSE) {
    die("Error en la consulta: " . $mysqli->error);
}

// Manejar la eliminación del producto
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['eliminar'])) {
    $producto_id = intval($_POST['producto_id']);
    $query = "DELETE FROM Productos WHERE producto_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $stmt->close();
    header("Location: ".$_SERVER['PHP_SELF']);
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Productos</title>
    <link rel="stylesheet" href="css/styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <style>
        /* Estilos CSS */
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        header {
            background-color: #333;
            color: #fff;
            padding: 1rem;
            text-align: center;
        }
        header nav a {
            color: #fff;
            margin: 0 15px;
            text-decoration: none;
        }
        main {
            padding: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .edit-button, .delete-button {
            border: none;
            padding: 8px 16px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 14px;
            margin: 4px 2px;
            cursor: pointer;
        }
        .edit-button {
            background-color: #4CAF50;
            color: white;
        }
        .edit-button:hover {
            background-color: #45a049;
        }
        .delete-button {
            background-color: #f44336;
            color: white;
        }
        .delete-button:hover {
            background-color: #e53935;
        }
        #edit-form {
            display: none; /* Oculta el formulario al cargar la página */
            background-color: #fff;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            max-width: 600px;
            margin: 0 auto;
        }
        form label {
            display: block;
            margin-bottom: 8px;
        }
        form input[type="text"], 
        form input[type="number"], 
        form textarea {
            width: calc(100% - 20px);
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ccc;
            border-radius: 4px;
        }
        form button {
            background-color: #4CAF50;
            color: white;
            border: none;
            padding: 10px 20px;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            border-radius: 4px;
            cursor: pointer;
        }
        form button:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>
    <header>
        <h1>Editar Productos</h1>
        <nav>
            <a href="logout.php">Cerrar sesión</a>
            <a href="admin.php">Panel de Administración</a>
        </nav>
    </header>
    <main>
        <h2>Lista de Productos</h2>
        <table>
            <thead>
                <tr>
                    <th>ID Producto</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Descripción</th>
                    <th>Precio</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody id="product-list">
                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['producto_id']); ?></td>
                        <td><?php echo htmlspecialchars($row['codigo_producto']); ?></td>
                        <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                        <td><?php echo htmlspecialchars($row['descripcion']); ?></td>
                        <td><?php echo htmlspecialchars($row['precio']); ?></td>
                        <td>
                            <button class="edit-button" data-id="<?php echo htmlspecialchars($row['producto_id']); ?>">Editar</button>
                            <form action="" method="POST" style="display:inline;">
                                <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($row['producto_id']); ?>">
                                <button type="submit" name="eliminar" class="delete-button" onclick="return confirm('¿Estás seguro de que quieres eliminar este producto?');">Eliminar</button>
                            </form>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>

        <h2>Editar Producto</h2>
        <form id="edit-form">
            <input type="hidden" id="producto_id" name="producto_id">
            <label for="codigo_producto">Código del Producto:</label>
            <input type="text" id="codigo_producto" name="codigo_producto" required>
            
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
            
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea>
            
            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" step="0.01" required>
            
            <button type="submit">Actualizar Producto</button>
        </form>
    </main>
    <footer>
        <p>&copy; 2024 Tienda en Línea</p>
    </footer>

    <script>
        $(document).ready(function() {
            // Maneja el clic en el botón de editar
            $('.edit-button').on('click', function() {
                var producto_id = $(this).data('id');

                // Obtener los datos del producto
                $.ajax({
                    url: 'get_product.php',
                    method: 'GET',
                    data: { producto_id: producto_id },
                    success: function(response) {
                        var product = JSON.parse(response);
                        $('#producto_id').val(product.producto_id);
                        $('#codigo_producto').val(product.codigo_producto);
                        $('#nombre').val(product.nombre);
                        $('#descripcion').val(product.descripcion);
                        $('#precio').val(product.precio);

                        // Mostrar el formulario con animación de deslizamiento hacia abajo
                        $('#edit-form').slideDown();
                    },
                    error: function() {
                        alert('Error al obtener los datos del producto.');
                    }
                });
            });

            // Maneja el envío del formulario
            $('#edit-form').on('submit', function(event) {
                event.preventDefault();

                $.ajax({
                    url: 'edit_product_action.php',
                    method: 'POST',
                    data: $(this).serialize(),
                    success: function(response) {
                        alert(response); // Mostrar respuesta del servidor
                        $('#edit-form').slideUp(); // Ocultar el formulario
                        location.reload(); // Recarga la lista de productos
                    },
                    error: function() {
                        alert('Error al actualizar el producto.');
                    }
                });
            });
        });
    </script>
</body>
</html>

<?php
$mysqli->close();
?>
