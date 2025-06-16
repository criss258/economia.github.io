<?php
$conn = new mysqli("localhost", "root", "", "economia");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener planes de ahorro activos
$planes_activos = $conn->query("SELECT id, titulo FROM planes_ahorro WHERE estado = 'activo'");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Ingresos y Gastos</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/menu.js?v=<?php echo time(); ?>" defer></script> <!-- Incluir el archivo JS -->
</head>
<body>
    <header>
        <h1>Registro de Ingresos y Gastos</h1>
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
        <form action="procesar_registro.php" method="POST">
            <label for="tipo">Tipo:</label>
            <select name="tipo" id="tipo" required>
                <option value="ingreso">Ingreso</option>
                <option value="gasto">Gasto</option>
            </select>
            <label for="categoria">Categoría:</label>
            <select name="categoria" id="categoria" required onchange="togglePlanOptions()">
                <option value="alimentacion">Alimentación</option>
                <option value="ahorro">Ahorro</option>
                <option value="entretenimiento">Entretenimiento</option>
                <option value="servicios">Servicios</option>
                <option value="estudios">Estudios</option>
                <option value="trabajo">Trabajo</option>
                <option value="planes_ahorro">Planes de Ahorro</option>
            </select>
            <div id="planes_ahorro_options" style="display: none;">
                <label for="plan_id">Seleccionar Plan de Ahorro:</label>
                <select name="plan_id" id="plan_id">
                    <option value="">Seleccione un plan</option>
                    <?php while ($plan = $planes_activos->fetch_assoc()): ?>
                        <option value="<?php echo $plan['id']; ?>"><?php echo $plan['titulo']; ?></option>
                    <?php endwhile; ?>
                </select>
                <label for="cuota">Monto de la Cuota:</label>
                <input type="number" name="cuota" id="cuota" step="0.01">
            </div>
            <label for="monto">Monto (S/):</label>
            <input type="number" name="monto" id="monto" required>
            <label for="fecha">Fecha:</label>
            <input type="date" name="fecha" id="fecha" required>
            <button type="submit">Registrar</button>
        </form>
    </main>
    <footer>
        <!-- Footer content -->
    </footer>
    <script>
        function togglePlanOptions() {
            const categoria = document.getElementById('categoria').value;
            const planOptions = document.getElementById('planes_ahorro_options');
            if (categoria === 'planes_ahorro') {
                planOptions.style.display = 'block';
            } else {
                planOptions.style.display = 'none';
            }
        }
    </script>
</body>
</html>
<?php $conn->close(); ?>