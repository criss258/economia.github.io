<?php
$conn = new mysqli("localhost", "root", "", "economia");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$titulo = $_POST['titulo'];
$descripcion = $_POST['descripcion'];
$fecha = $_POST['fecha'];
$color = $_POST['color'];

$sql = "INSERT INTO eventos (titulo, descripcion, fecha, color) 
        VALUES ('$titulo', '$descripcion', '$fecha', '$color')";

if ($conn->query($sql) === TRUE) {
    echo "Evento creado exitosamente.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
header("Location: calendario.php");
?>
