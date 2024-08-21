<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "olimpiadas3");

if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: login.php');
    exit();
}

// Aquí puedes agregar el código para manejar las funcionalidades del panel de administración

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="css/styles.css">
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
        <p>Desde aquí puedes gestionar los productos y pedidos.</p>
        <!-- Agregar funcionalidades del panel de administración aquí -->
    </main>
    <footer>
        <p>&copy; 2024 Tienda en Línea</p>
    </footer>
</body>
</html>
