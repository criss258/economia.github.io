<?php
session_start();
$conn = new mysqli("localhost", "root", "", "economia");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $correo = $_POST['correo'];
    $contrasena = $_POST['contrasena'];

    $sql = "SELECT * FROM usuarios WHERE correo = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $correo);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();

    if ($usuario) {
        echo "Usuario encontrado. Verificando contraseña...";
    } else {
        echo "Usuario no encontrado.";
    }
 // Asegurarse de detener la ejecución después de redirigir
    // Verificar la contraseña ingresada con la almacenada en la base de datos
    if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
        echo "Contraseña correcta. Iniciando sesión...";
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['usuario_nombre'] = $usuario['nombre'];
        header("Location: index.php"); // Redirigir al sistema principal
        exit;
    } else {
        $error = "Correo o contraseña incorrectos.";
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de Sesión</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <main>
        <h1>Inicio de Sesión</h1>
        <form method="POST" action="login.php">
            <label for="correo">Correo:</label>
            <input type="email" id="correo" name="correo" required>
            <label for="contrasena">Contraseña:</label>
            <input type="password" id="contrasena" name="contrasena" required>
            <?php if ($error): ?>
                <p style="color: red;"><?php echo $error; ?></p>
            <?php endif; ?>
            <button type="submit">Iniciar Sesión</button>
        </form>
        <p><a href="recuperar_contrasena.php">¿Olvidaste tu contraseña?</a></p>
    </main>
</body>
</html>
