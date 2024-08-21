<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "olimpiadas3");

if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = $_POST['nombre_usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';

    // Validación específica para el usuario admin
    if ($nombre_usuario === 'admin' && $contrasena === '1234') {
        $_SESSION['usuario'] = $nombre_usuario;
        $_SESSION['tipo_usuario'] = 'admin';
        header('Location: admin.php');
        exit();
    }

    // Validación para otros usuarios
    $query = "SELECT * FROM Clientes WHERE nombre_usuario = ?";
    $stmt = $mysqli->prepare($query);
    $stmt->bind_param('s', $nombre_usuario);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        if (password_verify($contrasena, $user['contrasena'])) {
            $_SESSION['usuario'] = $nombre_usuario;
            $_SESSION['tipo_usuario'] = $user['tipo_usuario'];
            $_SESSION['cliente_id'] = $user['cliente_id']; // Guardar cliente_id en la sesión

            if ($user['tipo_usuario'] === 'admin') {
                header('Location: admin.php');
            } else {
                header('Location: index.php');
            }
            exit();
        } else {
            $error = 'Contraseña incorrecta.';
        }
    } else {
        $error = 'Nombre de usuario no encontrado.';
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Iniciar sesión</h1>
    </header>
    <main>
        <form action="login.php" method="post">
            <label for="nombre_usuario">Nombre de usuario:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required>
            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>
            <button type="submit">Iniciar sesión</button>
        </form>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <p>¿No tienes una cuenta? <a href="register.php" class="button">Regístrate aquí</a></p>
    </main>
    <footer>
        <p>&copy; 2024 Tienda en Línea</p>
    </footer>
</body>
</html>
