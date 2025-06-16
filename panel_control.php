<?php
session_start();

// Verificar si la carpeta 'uploads' existe, si no, crearla
$uploadsDir = __DIR__ . '/uploads';
if (!is_dir($uploadsDir)) {
    mkdir($uploadsDir, 0777, true); // Crear la carpeta con permisos de escritura
}

$conn = new mysqli("localhost", "root", "", "economia");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['nombre'])) {
        $nombre = $_POST['nombre'];
        $correo = $_POST['correo'];
        $contrasena = password_hash($_POST['contrasena'], PASSWORD_BCRYPT); // Encriptar la contraseña

        $sql = "INSERT INTO usuarios (nombre, correo, contrasena) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $nombre, $correo, $contrasena);

        if ($stmt->execute()) { 
            $mensaje = "Usuario creado exitosamente.";
        } else {
            $mensaje = "Error al crear el usuario: " . $conn->error;
        }
    } elseif (isset($_POST['pagina'])) {
        $pagina = $_POST['pagina'];
        $fondo = '';

        // Si se sube una imagen
        if (isset($_FILES['imagen']) && $_FILES['imagen']['error'] === UPLOAD_ERR_OK) {
            $nombreArchivo = basename($_FILES['imagen']['name']);
            $rutaDestino = "uploads/" . $nombreArchivo;

            // Mover el archivo subido a la carpeta 'uploads'
            if (move_uploaded_file($_FILES['imagen']['tmp_name'], $rutaDestino)) {
                $fondo = $rutaDestino; // Guardar la ruta de la imagen como fondo
            } else {
                $mensaje_fondo = "Error al subir la imagen.";
            }
        }

        // Si se selecciona un color
        if (isset($_POST['color']) && !empty($_POST['color'])) {
            $fondo = $_POST['color']; // Guardar el color como fondo
        }

        if (!empty($fondo)) {
            $sql = "INSERT INTO configuraciones (pagina, fondo) VALUES (?, ?)
                    ON DUPLICATE KEY UPDATE fondo = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sss", $pagina, $fondo, $fondo);

            if ($stmt->execute()) {
                $mensaje_fondo = "Fondo actualizado exitosamente.";
            } else {
                $mensaje_fondo = "Error al actualizar el fondo: " . $conn->error;
            }
        } else {
            $mensaje_fondo = "Por favor, selecciona un color o sube una imagen.";
        }
    }
}

$usuarios = $conn->query("SELECT * FROM usuarios");
$configuraciones = $conn->query("SELECT * FROM configuraciones");
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Control</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Panel de Control</h1>
        <a href="logout.php">Cerrar Sesión</a>
    </header>
    <main>
        <section>
            <h2>Crear Nuevo Usuario</h2>
            <form method="POST" action="panel_control.php">
                <label for="nombre">Nombre:</label>
                <input type="text" id="nombre" name="nombre" required>
                <label for="correo">Correo:</label>
                <input type="email" id="correo" name="correo" required>
                <label for="contrasena">Contraseña:</label>
                <input type="password" id="contrasena" name="contrasena" required>
                <button type="submit">Crear Usuario</button>
            </form>
            <?php if (isset($mensaje)): ?>
                <p><?php echo $mensaje; ?></p>
            <?php endif; ?>
        </section>
        <section>
            <h2>Lista de Usuarios</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($usuario = $usuarios->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $usuario['id']; ?></td>
                            <td><?php echo $usuario['nombre']; ?></td>
                            <td><?php echo $usuario['correo']; ?></td>
                            <td>
                                <a href="restablecer_contrasena.php?id=<?php echo $usuario['id']; ?>">Restablecer Contraseña</a>
                                <a href="eliminar_usuario.php?id=<?php echo $usuario['id']; ?>">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
        <section>
            <h2>Configurar Fondo de Pantalla</h2>
            <form method="POST" action="panel_control.php" enctype="multipart/form-data">
                <label for="pagina">Página:</label>
                <select id="pagina" name="pagina" required>
                    <option value="index.php">Inicio</option>
                    <option value="ingresos_gastos.php">Ingresos y Gastos</option>
                    <option value="analisis.php">Análisis</option>
                    <option value="calendario.php">Calendario</option>
                    <option value="planes.php">Planes de Ahorro</option>
                    <option value="consultas.php">Consultas</option>
                </select>

                <label for="color">Seleccionar Color:</label>
                <input type="color" id="color" name="color">

                <label for="imagen">Subir Imagen:</label>
                <input type="file" id="imagen" name="imagen" accept="image/*">

                <button type="submit">Actualizar Fondo</button>
            </form>
            <?php if (isset($mensaje_fondo)): ?>
                <p><?php echo $mensaje_fondo; ?></p>
            <?php endif; ?>
            <h3>Fondos Actuales</h3>
            <table>
                <thead>
                    <tr>
                        <th>Página</th>
                        <th>Fondo</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($config = $configuraciones->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $config['pagina']; ?></td>
                            <td>
                                <?php if (strpos($config['fondo'], 'uploads/') === 0): ?>
                                    <img src="<?php echo $config['fondo']; ?>" alt="Fondo" style="max-width: 100px; max-height: 100px;">
                                <?php else: ?>
                                    <div style="width: 100px; height: 100px; background: <?php echo $config['fondo']; ?>;"></div>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
