    <?php
    session_start();

    // Conéctate a la base de datos
    $mysqli = new mysqli("localhost", "root", "", "olimpiadas3");

    if ($mysqli->connect_error) {
        die("Conexión fallida: " . $mysqli->connect_error);
    }

    $productos_en_carrito = $_SESSION['carrito'] ?? [];

    $productos = [];

    if (!empty($productos_en_carrito)) {
        // Crear una lista de los IDs de productos en el carrito
        $ids = implode(',', array_keys($productos_en_carrito));

        // Obtener los detalles de los productos
        $query = "SELECT producto_id, nombre, precio FROM Productos WHERE producto_id IN ($ids)";
        $result = $mysqli->query($query);

        while ($row = $result->fetch_assoc()) {
            $productos[] = $row;
        }
    }
    ?>

    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Carrito de Compras</title>
        <style>
            body {
                font-family: Arial, sans-serif;
                background-color: #f4f4f4;
                margin: 0;
                padding: 0;
            }
            header {
                background-color: #333;
                color: #fff;
                padding: 10px 0;
                text-align: center;
            }
            main {
                padding: 20px;
            }
            table {
                width: 100%;
                border-collapse: collapse;
            }
            table, th, td {
                border: 1px solid #ddd;
            }
            th, td {
                padding: 8px;
                text-align: center;
            }
            th {
                background-color: #333;
                color: #fff;
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
            .quantity-buttons {
                display: flex;
                justify-content: center;
                align-items: center;
            }
            .quantity-buttons button {
                padding: 10px 20px; /* Tamaño más grande */
                margin: 0 10px; /* Espaciado entre los botones */
                background-color: #007bff;
                color: #fff;
                border: none;
                cursor: pointer;
                font-size: 16px; /* Fuente más grande */
                border-radius: 5px; /* Bordes redondeados */
                transition: background-color 0.3s ease;
            }
            .quantity-buttons button:hover {
                background-color: #0056b3; /* Color de fondo al pasar el ratón */
            }
            .btn-checkout {
                display: block;
                width: 200px;
                padding: 15px;
                margin: 20px auto;
                text-align: center;
                background-color: #28a745;
                color: #fff;
                border: none;
                border-radius: 5px;
                font-size: 18px;
                cursor: pointer;
                transition: background-color 0.3s ease;
            }
            .btn-checkout:hover {
                background-color: #218838; /* Color de fondo al pasar el ratón */
            }
        </style>
    </head>
    <body>
        <header>
            <h1>Tu Carrito de Compras</h1>
        </header>
        <main>
            <h2>Productos en tu carrito</h2>
            <?php if (empty($productos)): ?>
                <p>No has agregado productos al carrito.</p>
            <?php else: ?>
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Precio</th>
                            <th>Cantidad</th>
                            <th>Subtotal</th>
                            <th>Acciones</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($productos as $producto): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($producto['nombre']); ?></td>
                                <td>$<?php echo htmlspecialchars($producto['precio']); ?></td>
                                <td><?php echo htmlspecialchars($productos_en_carrito[$producto['producto_id']]); ?></td>
                                <td>$<?php echo htmlspecialchars($producto['precio'] * $productos_en_carrito[$producto['producto_id']]); ?></td>
                                <td>
                                    <div class="quantity-buttons">
                                        <form action="update_cart.php" method="post" style="display: inline;">
                                            <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($producto['producto_id']); ?>">
                                            <input type="hidden" name="action" value="decrease">
                                            <button type="submit">-</button>
                                        </form>
                                        <form action="update_cart.php" method="post" style="display: inline;">
                                            <input type="hidden" name="producto_id" value="<?php echo htmlspecialchars($producto['producto_id']); ?>">
                                            <input type="hidden" name="action" value="increase">
                                            <button type="submit">+</button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
                <p><strong>Total de productos: <?php echo array_sum($productos_en_carrito); ?></strong></p>
                <form action="process_checkout.php" method="post">
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

    <?php
    $mysqli->close();
    ?>
