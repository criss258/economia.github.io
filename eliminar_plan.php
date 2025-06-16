<?php
$conn = new mysqli("localhost", "root", "", "economia");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$id = $_GET['id'];

$sql = "DELETE FROM planes_ahorro WHERE id = $id";

if ($conn->query($sql) === TRUE) {
    echo "Plan eliminado exitosamente.";
} else {
    echo "Error: " . $conn->error;
}

$conn->close();
?>
