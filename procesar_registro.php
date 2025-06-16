<?php
// Conexión a la base de datos
$conn = new mysqli("localhost", "root", "", "economia");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener datos del formulario
$tipo = $_POST['tipo'];
$categoria = $_POST['categoria'];
$monto = $_POST['monto'];
$fecha = $_POST['fecha'];
$plan_id = $_POST['plan_id'] ?? null;
$cuota = $_POST['cuota'] ?? null;

// Insertar en la tabla registros
$sql = "INSERT INTO registros (tipo, categoria, monto, fecha) VALUES ('$tipo', '$categoria', $monto, '$fecha')";

if ($conn->query($sql) === TRUE) {
    // Si se seleccionó un plan de ahorro, actualizar su progreso
    if ($categoria === 'planes_ahorro' && $plan_id && $cuota) {
        $update_sql = "UPDATE planes_ahorro SET progreso = progreso + $cuota WHERE id = $plan_id";
        if ($conn->query($update_sql) === TRUE) {
            echo "Progreso del plan de ahorro actualizado en soles.";
        } else {
            echo "Error al actualizar el progreso del plan: " . $conn->error;
        }
    }
    echo "Registro guardado exitosamente en soles.";
} else {
    echo "Error: " . $sql . "<br>" . $conn->error;
}

$conn->close();
?>
