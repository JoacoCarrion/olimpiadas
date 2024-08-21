<?php
session_start();

if (!isset($_SESSION['usuario']) || $_SESSION['tipo_usuario'] !== 'admin') {
    echo json_encode(['error' => 'No autorizado']);
    exit();
}

$mysqli = new mysqli("localhost", "root", "", "olimpiadas3");

if ($mysqli->connect_error) {
    echo json_encode(['error' => 'Conexión fallida: ' . $mysqli->connect_error]);
    exit();
}

if (isset($_GET['producto_id'])) {
    $producto_id = $_GET['producto_id'];
    $query = "SELECT producto_id, codigo_producto, nombre, descripcion, precio FROM Productos WHERE producto_id = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param("i", $producto_id);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $product = $result->fetch_assoc();
        echo json_encode($product);
    } else {
        echo json_encode(['error' => 'Producto no encontrado']);
    }
    $stmt->close();
} else {
    echo json_encode(['error' => 'ID de producto no proporcionado']);
}

$mysqli->close();
?>
