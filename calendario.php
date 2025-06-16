<?php
$conn = new mysqli("localhost", "root", "", "economia");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener eventos desde la base de datos
$eventos = $conn->query("SELECT * FROM eventos");

// Cerrar conexión
$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Eventos</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/menu.js?v=<?php echo time(); ?>" defer></script> <!-- Incluir el archivo JS -->
</head>
<body>
    <header>
        <h1>Eventos</h1>
        <nav>
            <ul>
                <li><a href="index.php">Inicio</a></li>
                <li>
                    <a href="#" id="toggleMenu">Opciones</a>
                    <ul class="submenu">
                        <li><a href="ingresos_gastos.php">Ingresos y Gastos</a></li>
                        <li><a href="analisis.php">Análisis</a></li>
                        <li><a href="calendario.php">Eventos</a></li>
                        <li><a href="planes.php">Planes de Ahorro</a></li>
                        <li><a href="consultas.php">Consultas</a></li>
                    </ul>
                </li>
                <li><a href="logout.php">Cerrar Sesión</a></li>
            </ul>
        </nav>
    </header>
    <main>
        <section>
            <h2>Crear Nuevo Evento</h2>
            <form action="procesar_evento.php" method="POST">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" required>
                <label for="descripcion">Descripción:</label>
                <textarea id="descripcion" name="descripcion"></textarea>
                <label for="fecha">Fecha:</label>
                <input type="date" id="fecha" name="fecha" required>
                <label for="color">Color:</label>
                <input type="color" id="color" name="color" value="#007bff">
                <button type="submit">Crear Evento</button>
            </form>
        </section>
        <section>
            <h2>Lista de Eventos</h2>
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($evento = $eventos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $evento['titulo']; ?></td>
                            <td><?php echo $evento['descripcion']; ?></td>
                            <td><?php echo $evento['fecha']; ?></td>
                            <td><?php echo (strtotime($evento['fecha']) < time()) ? 'Completado' : 'Pendiente'; ?></td>
                            <td>
                                <a href="editar_evento.php?id=<?php echo $evento['id']; ?>">Editar</a>
                                <a href="eliminar_evento.php?id=<?php echo $evento['id']; ?>" onclick="return confirm('¿Estás seguro de eliminar este evento?');">Eliminar</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>
    <footer>
        <!-- Footer content -->
    </footer>
</body>
</html>
