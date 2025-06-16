<?php
$conn = new mysqli("localhost", "root", "", "economia");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Variables para filtros
$periodo = $_GET['periodo'] ?? 'dia';
$categoria = $_GET['categoria'] ?? 'todas';
$fuente_ingreso = $_GET['fuente_ingreso'] ?? 'todas';

// Consultas dinámicas basadas en filtros
$where = "1=1";
if ($periodo === 'semana') {
    $where .= " AND fecha >= CURDATE() - INTERVAL 7 DAY";
} elseif ($periodo === 'mes') {
    $where .= " AND MONTH(fecha) = MONTH(CURDATE()) AND YEAR(fecha) = YEAR(CURDATE())";
} elseif ($periodo === 'anio') {
    $where .= " AND YEAR(fecha) = YEAR(CURDATE())";
}

if ($categoria !== 'todas') {
    $where .= " AND categoria = '$categoria'";
}

if ($fuente_ingreso !== 'todas') {
    $where .= " AND categoria = '$fuente_ingreso'";
}

// Consultas para mostrar resultados
$gastos = $conn->query("SELECT * FROM registros WHERE tipo='gasto' AND $where");
$ingresos = $conn->query("SELECT * FROM registros WHERE tipo='ingreso' AND $where");

// Categorías disponibles
$categorias = $conn->query("SELECT DISTINCT categoria FROM registros");

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Consultas</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/menu.js?v=<?php echo time(); ?>" defer></script> <!-- Incluir el archivo JS -->
</head>
<body>
    <header>
        <h1>Consultas</h1>
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
            <h2>Formulario de Consultas</h2>
            <form method="GET" action="consultas.php">
                <label for="periodo">Filtrar por período:</label>
                <select id="periodo" name="periodo">
                    <option value="dia" <?php echo $periodo === 'dia' ? 'selected' : ''; ?>>Día</option>
                    <option value="semana" <?php echo $periodo === 'semana' ? 'selected' : ''; ?>>Semana</option>
                    <option value="mes" <?php echo $periodo === 'mes' ? 'selected' : ''; ?>>Mes</option>
                    <option value="anio" <?php echo $periodo === 'anio' ? 'selected' : ''; ?>>Año</option>
                </select>

                <label for="categoria">Clasificar por categoría:</label>
                <select id="categoria" name="categoria">
                    <option value="todas">Todas</option>
                    <?php while ($cat = $categorias->fetch_assoc()): ?>
                        <option value="<?php echo $cat['categoria']; ?>" <?php echo $categoria === $cat['categoria'] ? 'selected' : ''; ?>>
                            <?php echo ucfirst($cat['categoria']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <label for="fuente_ingreso">Filtrar ingresos por fuente:</label>
                <select id="fuente_ingreso" name="fuente_ingreso">
                    <option value="todas">Todas</option>
                    <?php while ($cat = $categorias->fetch_assoc()): ?>
                        <option value="<?php echo $cat['categoria']; ?>" <?php echo $fuente_ingreso === $cat['categoria'] ? 'selected' : ''; ?>>
                            <?php echo ucfirst($cat['categoria']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>

                <button type="submit">Consultar</button>
                <button type="button" onclick="exportar('pdf')">Exportar a PDF</button>
                <button type="button" onclick="exportar('excel')">Exportar a Excel</button>
            </form>
        </section>
        <section>
            <h2>Resultados de Gastos</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Categoría</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($gasto = $gastos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $gasto['id']; ?></td>
                            <td><?php echo $gasto['categoria']; ?></td>
                            <td>S/<?php echo $gasto['monto']; ?></td>
                            <td><?php echo $gasto['fecha']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
        <section>
            <h2>Resultados de Ingresos</h2>
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Categoría</th>
                        <th>Monto</th>
                        <th>Fecha</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($ingreso = $ingresos->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $ingreso['id']; ?></td>
                            <td><?php echo $ingreso['categoria']; ?></td>
                            <td>S/<?php echo $ingreso['monto']; ?></td>
                            <td><?php echo $ingreso['fecha']; ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>
    <footer>
        <!-- Footer content -->
    </footer>
    <script>
        function exportar(tipo) {
            alert(`Exportando reporte en formato ${tipo.toUpperCase()}.`);
            // Aquí puedes implementar la lógica para exportar a PDF o Excel.
        }
    </script>
</body>
</html>