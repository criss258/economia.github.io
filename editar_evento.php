<?php
$conn = new mysqli("localhost", "root", "", "economia");

if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

$id = $_GET['id'];
$evento = $conn->query("SELECT * FROM eventos WHERE id = $id")->fetch_assoc();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $titulo = $_POST['titulo'];
    $descripcion = $_POST['descripcion'];
    $fecha = $_POST['fecha'];
    $color = $_POST['color'];

    $sql = "UPDATE eventos SET titulo = '$titulo', descripcion = '$descripcion', fecha = '$fecha', color = '$color' WHERE id = $id";

    if ($conn->query($sql) === TRUE) {
        header("Location: calendario.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Editar Evento</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <h1>Editar Evento</h1>
    </header>
    <main>
        <form action="" method="POST">
            <label for="titulo">Título:</label>
            <input type="text" id="titulo" name="titulo" value="<?php echo $evento['titulo']; ?>" required>
            <label for="descripcion">Descripción:</label>
            <textarea id="descripcion" name="descripcion"><?php echo $evento['descripcion']; ?></textarea>
            <label for="fecha">Fecha:</label>
            <input type="date" id="fecha" name="fecha" value="<?php echo $evento['fecha']; ?>" required>
            <label for="color">Color:</label>
            <input type="color" id="color" name="color" value="<?php echo $evento['color']; ?>">
            <button type="submit">Guardar Cambios</button>
        </form>
    </main>
</body>
</html>
