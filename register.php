<?php
session_start();
$mysqli = new mysqli("localhost", "root", "", "olimpiadas3");

if ($mysqli->connect_error) {
    die("Conexión fallida: " . $mysqli->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nombre_usuario = $_POST['nombre_usuario'] ?? '';
    $contrasena = $_POST['contrasena'] ?? '';
    $correo_electronico = $_POST['correo_electronico'] ?? '';

    if (!empty($nombre_usuario) && !empty($contrasena) && !empty($correo_electronico)) {
        $contrasena_hash = password_hash($contrasena, PASSWORD_DEFAULT);

        $query = "INSERT INTO Clientes (nombre_usuario, contrasena, correo_electronico, tipo_usuario) VALUES (?, ?, ?, 'cliente')";
        $stmt = $mysqli->prepare($query);
        $stmt->bind_param('sss', $nombre_usuario, $contrasena_hash, $correo_electronico);

        if ($stmt->execute()) {
            header('Location: login.php');
            exit();
        } else {
            $error = 'Error al registrar el usuario: ' . $stmt->error;
        }
    } else {
        $error = 'Por favor, completa todos los campos.';
    }
}

$mysqli->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrar</title>
    <link rel="stylesheet" href="css/styles.css">
</head>
<body>
    <header>
        <h1>Registrarse</h1>
    </header>
    <main>
        <form action="register.php" method="post">
            <label for="nombre_usuario">Nombre de usuario:</label>
            <input type="text" id="nombre_usuario" name="nombre_usuario" required>
            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>
            <label for="correo_electronico">Correo electrónico:</label>
            <input type="email" id="correo_electronico" name="correo_electronico" required>
            <button type="submit">Registrarse</button>
        </form>
        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>
        <p>¿Ya tienes una cuenta? <a href="login.php" class="button">Inicia sesión aquí</a></p>
    </main>
    <footer>
        <p>&copy; 2024 Tienda en Línea</p>
    </footer>
</body>
</html>
