<?php
// Conexión a la base de datos
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "uni";

$conn = new mysqli($servername, $username, $password, $dbname);
if ($conn->connect_error) {
    die("La conexión falló: " . $conn->connect_error);
}

// Mensaje de resultado
$mensaje = "";

// Guardar inscripción si se envió el formulario
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['registrar_inscripcion'])) {
    $codigoAlumno = $_POST['alumno'] ?? '';
    $codigoCarrera = $_POST['carrera'] ?? '';

    if (!empty($codigoAlumno) && !empty($codigoCarrera)) {

        // Verificar si el alumno ya está inscrito en esa carrera
        $check = $conn->prepare("SELECT * FROM inscripciones WHERE CodigoAlumno=? AND CodigoCarrera=?");
        $check->bind_param("ss", $codigoAlumno, $codigoCarrera);
        $check->execute();
        $checkResult = $check->get_result();

        if ($checkResult->num_rows > 0) {
            $mensaje = "⚠️ Este alumno ya está inscrito en esta carrera.";
        } else {
            // Insertar en inscripciones con la fecha actual
            $fechaActual = date("Y-m-d"); // formato YYYY-MM-DD
            $stmtInsert = $conn->prepare("INSERT INTO inscripciones (CodigoAlumno, CodigoCarrera, Fecha) VALUES (?, ?, ?)");
            $stmtInsert->bind_param("sss", $codigoAlumno, $codigoCarrera, $fechaActual);

            if ($stmtInsert->execute()) {
                $mensaje = "✅ Inscripción registrada correctamente.";
            } else {
                $mensaje = "❌ Error al registrar: " . $stmtInsert->error;
            }

            $stmtInsert->close();
        }

        $check->close();
    } else {
        $mensaje = "⚠️ Debe seleccionar un alumno y una carrera.";
    }
}

// Consultar alumnos y carreras para el formulario
$alumnos_result = $conn->query("SELECT Codigo, Nombre, Apellidos FROM alumnos");
$carreras_result = $conn->query("SELECT Codigo, Nombre FROM carreras");
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Inscripción</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <h1>Registro de Inscripción</h1>

    <?php if ($mensaje): ?>
        <p style="font-weight:bold; color:darkblue;"><?php echo $mensaje; ?></p>
    <?php endif; ?>

    <form action="registro.php" method="POST">
        <!-- Selección de Alumno -->
        <label for="alumno">Seleccione un alumno</label><br>
        <select name="alumno" id="alumno" onchange="document.getElementById('carrerasDiv').style.display='block';" required>
            <option value="">Seleccione un alumno</option>
            <?php
            if ($alumnos_result && $alumnos_result->num_rows > 0) {
                while ($alumno = $alumnos_result->fetch_assoc()) {
                    echo "<option value='" . htmlspecialchars($alumno['Codigo']) . "'>"
                        . htmlspecialchars($alumno['Nombre'] . " " . $alumno['Apellidos'])
                        . "</option>";
                }
            } else {
                echo "<option value=''>No hay alumnos registrados</option>";
            }
            ?>
        </select><br><br>

        <!-- Selección de Carrera -->
        <div id="carrerasDiv" style="display:none;">
            <h2>Seleccione una carrera</h2>
            <table>
                <tr>
                    <th>Seleccionar</th>
                    <th>Código</th>
                    <th>Nombre</th>
                </tr>
                <?php
                if ($carreras_result && $carreras_result->num_rows > 0) {
                    while ($carrera = $carreras_result->fetch_assoc()) {
                        echo "<tr>";
                        echo "<td><input type='radio' name='carrera' value='" . htmlspecialchars($carrera['Codigo']) . "' required></td>";
                        echo "<td>" . htmlspecialchars($carrera['Codigo']) . "</td>";
                        echo "<td>" . htmlspecialchars($carrera['Nombre']) . "</td>";
                        echo "</tr>";
                    }
                } else {
                    echo "<tr><td colspan='3'>No hay carreras registradas</td></tr>";
                }
                ?>
            </table>
        </div>

        <br>
        <input type="submit" value="Registrar Inscripción" name="registrar_inscripcion">
    </form>

    <?php $conn->close(); ?>
</body>
</html>
