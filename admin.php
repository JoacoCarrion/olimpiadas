<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: login.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
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
    </style>
</head>
<body>
    <header>
        <h1>Panel de Administración</h1>
        <nav>
            <a href="logout.php">Cerrar sesión</a>
        </nav>
    </header>
    <main>
        <h2>Bienvenido al Panel de Administración</h2>
        <p>Aquí puedes gestionar productos, pedidos, y otros aspectos del sistema.</p>

        <h3>Agregar Producto</h3>
        <form action="add_product.php" method="post">
            <label for="codigo_producto">Código del Producto:</label>
            <input type="text" id="codigo_producto" name="codigo_producto" required>
            
            <label for="nombre">Nombre:</label>
            <input type="text" id="nombre" name="nombre" required>
            
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion" required></textarea>
            
            <label for="precio">Precio:</label>
            <input type="number" id="precio" name="precio" step="0.01" required>
            
            <button type="submit">Agregar Producto</button>
        </form>

        <h3>Consultar Lista de Productos</h3>
        <a href="edit_product.php" class="button">Ver Productos</a>

        <h3>Ver Estado de Pedidos Pendientes</h3>
        <a href="pending_orders.php" class="button">Ver Pedidos Pendientes</a>

        <h3>Ver Estado de Cuenta</h3>
        <a href="view_account.php" class="button">Ver Facturas</a>

        <h3>Ventas</h3>
        <a href="ventas.php" class="button">Ventas</a>

        <h3>A Cobrar</h3>
        <a href="cobrar.php" class="button">A Cobrar</a>
    </main>
</body>
</html>
        