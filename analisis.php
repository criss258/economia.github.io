<?php
$conn = new mysqli("localhost", "root", "", "economia");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener datos de ingresos, gastos y ahorro por día, mes y año
$filtro = $_GET['filtro'] ?? 'dia';
$labels = [];
$ingresos = [];
$gastos = [];
$ahorro = [];

if ($filtro === 'dia') {
    $query = "SELECT DATE(fecha) AS periodo, 
                     SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) AS ingresos, 
                     SUM(CASE WHEN tipo = 'gasto' THEN monto ELSE 0 END) AS gastos 
              FROM registros 
              WHERE fecha >= CURDATE() - INTERVAL 7 DAY 
              GROUP BY DATE(fecha)";
} elseif ($filtro === 'mes') {
    $query = "SELECT DATE_FORMAT(fecha, '%Y-%m') AS periodo, 
                     SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) AS ingresos, 
                     SUM(CASE WHEN tipo = 'gasto' THEN monto ELSE 0 END) AS gastos 
              FROM registros 
              WHERE fecha >= CURDATE() - INTERVAL 6 MONTH 
              GROUP BY DATE_FORMAT(fecha, '%Y-%m')";
} else { // Año
    $query = "SELECT YEAR(fecha) AS periodo, 
                     SUM(CASE WHEN tipo = 'ingreso' THEN monto ELSE 0 END) AS ingresos, 
                     SUM(CASE WHEN tipo = 'gasto' THEN monto ELSE 0 END) AS gastos 
              FROM registros 
              GROUP BY YEAR(fecha)";
}

$result = $conn->query($query);

while ($row = $result->fetch_assoc()) {
    $labels[] = $row['periodo'];
    $ingresos[] = $row['ingresos'];
    $gastos[] = $row['gastos'];
    $ahorro[] = $row['ingresos'] - $row['gastos'];
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Análisis</title>
    <link rel="stylesheet" href="assets/css/style.css">
    <script src="assets/js/menu.js?v=<?php echo time(); ?>" defer></script> <!-- Incluir el archivo JS -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <header>
        <h1>Análisis</h1>
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
        <h2>Gráfico de Ingresos, Gastos y Ahorro</h2>
        <div>
            <label for="filtro">Filtrar por:</label>
            <select id="filtro" onchange="cambiarFiltro()">
                <option value="dia" <?php echo $filtro === 'dia' ? 'selected' : ''; ?>>Día</option>
                <option value="mes" <?php echo $filtro === 'mes' ? 'selected' : ''; ?>>Mes</option>
                <option value="anio" <?php echo $filtro === 'anio' ? 'selected' : ''; ?>>Año</option>
            </select>
        </div>
        <canvas id="grafico"></canvas>
    </main>
    <footer>
        <!-- Footer content -->
    </footer>
    <script>
        const labels = <?php echo json_encode($labels); ?>;
        const ingresos = <?php echo json_encode($ingresos); ?>;
        const gastos = <?php echo json_encode($gastos); ?>;
        const ahorro = <?php echo json_encode($ahorro); ?>;

        const ctx = document.getElementById('grafico').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Ingresos',
                        data: ingresos,
                        borderColor: 'green',
                        fill: false
                    },
                    {
                        label: 'Gastos',
                        data: gastos,
                        borderColor: 'red',
                        fill: false
                    },
                    {
                        label: 'Ahorro',
                        data: ahorro,
                        borderColor: 'blue',
                        fill: false
                    }
                ]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return `${context.dataset.label}: S/${context.raw}`;
                            }
                        }
                    }
                },
                scales: {
                    x: {
                        title: {
                            display: true,
                            text: 'Tiempo'
                        }
                    },
                    y: {
                        title: {
                            display: true,
                            text: 'Valores Monetarios ($)'
                        },
                        beginAtZero: true
                    }
                }
            }
        });

        function cambiarFiltro() {
            const filtro = document.getElementById('filtro').value;
            window.location.href = `analisis.php?filtro=${filtro}`;
        }
    </script>
</body>
</html>
