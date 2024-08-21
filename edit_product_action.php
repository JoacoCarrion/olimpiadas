<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    header('Location: login.php');
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "olimpiadas3");

if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

// Comprobar si se recibieron datos del formulario
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = $_POST['producto_id'];
    $codigo_producto = $_POST['codigo_producto'];
    $nombre = $_POST['nombre'];
    $descripcion = $_POST['descripcion'];
    $precio = $_POST['precio'];

    $update_query = "UPDATE Productos SET codigo_producto = ?, nombre = ?, descripcion = ?, precio = ? WHERE producto_id = ?";
    $stmt = $mysqli->prepare($update_query);

    if ($stmt) {
        $stmt->bind_param("sssdi", $codigo_producto, $nombre, $descripcion, $precio, $producto_id);
        if ($stmt->execute()) {
            echo "Producto actualizado con éxito.";
        } else {
            echo "Error al actualizar el producto: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error en la preparación de la consulta: " . $mysqli->error;
    }
} else {
    echo "Solicitud no válida.";
}

$mysqli->close();
?>
