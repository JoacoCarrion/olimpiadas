<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: login.php');
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Conexión a la base de datos
    $servername = "localhost";
    $username = "root"; // Cambia esto según tu configuración
    $password = ""; // Cambia esto según tu configuración
    $dbname = "olimpiadas3"; // Usar la base de datos olimpiadas3

    // Crear conexión
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Verificar conexión
    if ($conn->connect_error) {
        die("Conexión fallida: " . $conn->connect_error);
    }

    // Obtener datos del formulario
    $codigo_producto = $_POST['codigo_producto'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];

    // Preparar y ejecutar la consulta
    $stmt = $conn->prepare("INSERT INTO Productos (codigo_producto, nombre, descripcion, precio) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssd", $codigo_producto, $nombre, $descripcion, $precio);

    if ($stmt->execute()) {
        echo "Producto agregado exitosamente.";
    } else {
        echo "Error al agregar el producto: " . $stmt->error;
    }

    // Cerrar conexión
    $stmt->close();
    $conn->close();
}
?>
