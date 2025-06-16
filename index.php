<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    echo "Sesión no iniciada. Redirigiendo al login...";
    header("Location: login.php"); // Redirigir al login si no hay sesión iniciada // Asegurarse de detener la ejecución después de redirigir
    exit;
}

echo "Sesión iniciada correctamente. Cargando página principal...";

$conn = new mysqli("localhost", "root", "", "economia");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener fondo de pantalla
$fondo = "#f4f4f9"; // Valor predeterminado
$result = $conn->query("SELECT fondo FROM configuraciones WHERE pagina = 'index.php'");
if ($result && $row = $result->fetch_assoc()) {
    $fondo = $row['fondo'];
}

// Verificar si el fondo es una imagen o un color
$isImage = strpos($fondo, 'uploads/') === 0 || filter_var($fondo, FILTER_VALIDATE_URL);

// Verificar el progreso de los planes de ahorro
$planes = $conn->query("SELECT progreso, monto_total FROM planes_ahorro WHERE estado = 'activo'");
$total_progreso = 0;
$total_monto = 0;

while ($plan = $planes->fetch_assoc()) {
    $total_progreso += $plan['progreso'];
    $total_monto += $plan['monto_total'];
}

// Determinar si se está ahorrando bien
$ahorro_bien = $total_monto > 0 && ($total_progreso / $total_monto) >= 0.8;

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Economía Personal</title>
    <link rel="stylesheet" href="assets/css/style.css?v=<?php echo time(); ?>">
    <script src="assets/js/menu.js?v=<?php echo time(); ?>" defer></script> <!-- Incluir el archivo JS -->
    <style>
        body {
            <?php if ($isImage): ?>
                background-image: url('<?php echo $fondo; ?>');
            <?php else: ?>
                background-color: <?php echo $fondo; ?>;
            <?php endif; ?>
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
        }
    </style>
</head>
<body>
    <header>
        <h1>Economía Personal</h1>
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
        <h2>Bienvenido, <?php echo htmlspecialchars($_SESSION['usuario_nombre']); ?>.</h2>
        <p>
            <?php if ($ahorro_bien): ?>
                😊 ¡Estás ahorrando bien y cumpliendo tus planes de ahorro!
            <?php else: ?>
                😟 Necesitas mejorar tu ahorro y cumplir con tus planes.
            <?php endif; ?>
        </p>
        <p>Selecciona una opción del menú para comenzar.</p>
    </main>
    <footer>
        <p>&copy; 2023 Economía Personal</p>
    </footer>
</body>
</html>
