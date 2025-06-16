<?php
$conn = new mysqli("localhost", "root", "", "economia");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$titulo = $_POST['titulo'];
$monto_total = $_POST['monto'];
$frecuencia = $_POST['frecuencia'];
$fecha_inicio = $_POST['fecha_inicio'];
$fecha_fin = $_POST['fecha_fin'];

$sql = "INSERT INTO planes_ahorro (titulo, monto_total, frecuencia, fecha_inicio, fecha_fin) 
        VALUES ('$titulo', $monto_total, '$frecuencia', '$fecha_inicio', '$fecha_fin')";

if ($conn->query($sql) === TRUE) {
    echo "Plan creado exitosamente en soles.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
header("Location: planes.php");
?>
