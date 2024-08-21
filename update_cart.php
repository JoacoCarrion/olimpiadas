<?php
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $producto_id = $_POST['producto_id'];
    $action = $_POST['action'];

    if (isset($_SESSION['carrito'][$producto_id])) {
        if ($action === 'increase') {
            $_SESSION['carrito'][$producto_id]++;
        } elseif ($action === 'decrease') {
            if ($_SESSION['carrito'][$producto_id] > 1) {
                $_SESSION['carrito'][$producto_id]--;
            } else {
                unset($_SESSION['carrito'][$producto_id]);
            }
        }
    }

    // Redirigir de vuelta al carrito
    header('Location: cart.php');
    exit();
} else {
    header('Location: cart.php');
    exit();
}
