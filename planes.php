<?php
$conn = new mysqli("localhost", "root", "", "economia");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener planes desde la base de datos
$filtro = $_GET['filtro'] ?? 'todos';
$where = $filtro === 'todos' ? '' : "WHERE estado = '$filtro'";
$planes = $conn->query("SELECT * FROM planes_ahorro $where");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Planes de Ahorro</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/menu.js?v=<?php echo time(); ?>" defer></script> <!-- Incluir el archivo JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <h1>Planes de Ahorro</h1>
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
            <h2>Lista de Planes de Ahorro</h2>
            <label for="filtro">Filtrar por:</label>
            <select id="filtro" onchange="filtrarPlanes()">
                <option value="todos">Todos</option>
                <option value="activo">Activos</option>
                <option value="completado">Completados</option>
            </select>
            <table>
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Monto Total</th>
                        <th>Progreso</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="lista-planes">
                    <?php while ($plan = $planes->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $plan['titulo']; ?></td>
                            <td>S/<?php echo $plan['monto_total']; ?></td>
                            <td>
                                <progress value="<?php echo $plan['progreso']; ?>" max="<?php echo $plan['monto_total']; ?>"></progress>
                                <?php echo round(($plan['progreso'] / $plan['monto_total']) * 100); ?>%
                            </td>
                            <td><?php echo $plan['estado']; ?></td>
                            <td>
                                <button onclick="editarPlan(<?php echo $plan['id']; ?>)">Editar</button>
                                <button onclick="eliminarPlan(<?php echo $plan['id']; ?>)">Eliminar</button>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
        <section>
            <h2>Crear Nuevo Plan de Ahorro</h2>
            <form action="procesar_plan.php" method="POST">
                <label for="titulo">Título:</label>
                <input type="text" id="titulo" name="titulo" required>
                <label for="monto">Monto a Ahorrar (S/):</label>
                <input type="number" id="monto" name="monto" required>
                <label for="tiempo">Tiempo de Ahorro:</label>
                <select id="tiempo" name="tiempo" required>
                    <option value="dias">Días</option>
                    <option value="meses">Meses</option>
                    <option value="anios">Años</option>
                </select>
                <label for="frecuencia">Frecuencia de Depósito:</label>
                <select id="frecuencia" name="frecuencia" required>
                    <option value="diario">Diario</option>
                    <option value="semanal">Semanal</option>
                    <option value="mensual">Mensual</option>
                </select>
                <label for="fecha_inicio">Fecha de Inicio:</label>
                <input type="date" id="fecha_inicio" name="fecha_inicio" required>
                <label for="fecha_fin">Fecha de Fin:</label>
                <input type="date" id="fecha_fin" name="fecha_fin" required>
                <button type="submit">Crear Plan</button>
            </form>
        </section>
        <section>
            <h2>Progreso del Ahorro</h2>
            <canvas id="grafico-progreso"></canvas>
        </section>
    </main>
    <footer>
        <!-- Footer content -->
    </footer>
    <script>
        function filtrarPlanes() {
            const filtro = document.getElementById('filtro').value;
            window.location.href = `planes.php?filtro=${filtro}`;
        }

        function eliminarPlan(id) {
            if (confirm("¿Estás seguro de que deseas eliminar este plan?")) {
                fetch(`eliminar_plan.php?id=${id}`, { method: 'GET' })
                    .then(response => response.text())
                    .then(data => {
                        alert(data);
                        location.reload();
                    });
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>
