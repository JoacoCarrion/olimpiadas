<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = $_POST['producto_id'];

    // Verifica si el carrito ya existe en la sesión, si no, créalo
    if (!isset($_SESSION['carrito'])) {
        $_SESSION['carrito'] = [];
    }

    // Agrega el producto al carrito
    if (isset($_SESSION['carrito'][$producto_id])) {
        $_SESSION['carrito'][$producto_id]++;
    } else {
        $_SESSION['carrito'][$producto_id] = 1;
    }

    // Redirige a cart.php
    header('Location: cart.php');
    exit();
} else {
    header('Location: productos.php');
    exit();
}
