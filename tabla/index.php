<?php
$mensaje = "";

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['agregar_alumno'])) {
    $servername = "localhost";
    $username   = "root";
    $password   = "";
    $dbname     = "uni";

    $conn = new mysqli($servername, $username, $password, $dbname);

    if ($conn->connect_error) {
        die("La conexión falló: " . $conn->connect_error);
    }

    // Recibir valores de forma segura
    $codigo    = $_POST['codigo'] ?? '';
    $nombre    = $_POST['nombre'] ?? '';
    $apellidos = $_POST['apellidos'] ?? '';
    $edad      = $_POST['edad'] ?? '';
    $telefono  = $_POST['telefono'] ?? '';

    // Validar que no estén vacíos
    if ($codigo && $nombre && $apellidos && $edad && $telefono) {
        // Usar prepared statement para más seguridad
        $sql = "INSERT INTO alumnos (Codigo, Nombre, Apellidos, Edad, Telefono) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);

        if ($stmt) {
            $stmt->bind_param("sssis", $codigo, $nombre, $apellidos, $edad, $telefono);

            if ($stmt->execute()) {
                $mensaje = "✅ Nuevo registro creado con éxito";
            } else {
                $mensaje = "❌ Error al insertar: " . $stmt->error;
            }

            $stmt->close();
        } else {
            $mensaje = "❌ Error al preparar la consulta: " . $conn->error;
        }
    } else {
        $mensaje = "⚠️ Todos los campos son obligatorios.";
    }

    $conn->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="style.css">
    <title>Registro de inscripción</title>
</head>
<body>
    <h1>Agregar Alumno</h1>

    <!-- Mostrar mensaje de PHP -->
    <?php if ($mensaje): ?>
        <p style="color: darkblue; font-weight: bold;"><?php echo $mensaje; ?></p>
    <?php endif; ?>

    <form action="index.php" method="POST">
        <label for="codigo">Matrícula</label>
        <input type="text" id="codigo" name="codigo" required><br><br>

        <label for="nombre">Nombre</label>
        <input type="text" id="nombre" name="nombre" required><br><br>
        
        <label for="apellidos">Apellidos</label>
        <input type="text" id="apellidos" name="apellidos" required><br><br>

        <label for="edad">Edad</label>
        <input type="number" min="14" max="100" id="edad" name="edad" required><br><br>

        <label for="telefono">Teléfono</label>
        <input type="tel" id="telefono" name="telefono" required><br><br>

        <input type="submit" value="Agregar Alumno" name="agregar_alumno">
    </form>
</body>
</html>
