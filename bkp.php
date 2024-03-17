consultar_usuario.php:
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Consulta de Usuario y Pagos Pendientes</title>
    <link rel="stylesheet" href="styles.css">
</head>
<body>
<a href="http://localhost/sist/index.html" target="_self">
        Volver a pantalla principal
    </a> 
    <br>
    <h2>Consulta de Usuario y Pagos Pendientes</h2>
    <form action="consultar_usuario.php" method="POST">
        <label for="id_persona">ID de la Persona:</label><br>
        <input type="text" id="id_persona" name="id_persona"><br>
        <input type="submit" value="Consultar por ID">
    </form>
    <form action="consultar_usuario.php" method="POST">
        <label for="nombre_apellido">Nombre y apellido de afiliado:</label><br>
        <input type="text" id="nombre_apellido" name="nombre_apellido"><br>
        <input type="submit" value="Consultar por Nombre y Apellido">
    </form>
    <form action="consultar_usuario.php" method="POST">
        <label for="dni">DNI de la Persona:</label><br>
        <input type="text" id="dni" name="dni"><br>
        <input type="submit" value="Consultar por DNI">
    </form>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST") {
        // Conexión a la base de datos
        $conexion = new mysqli("localhost", "root", "", "personas_db");

        // Comprobar conexión
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        // Función para obtener los meses abonados por el usuario
        function obtener_meses_abonados($conexion, $id_persona) {
            $sql_meses_abonados = "SELECT mes FROM pagos_realizados WHERE persona_id = $id_persona";
            $resultado_meses = $conexion->query($sql_meses_abonados);
            $meses_abonados = array();
            if ($resultado_meses->num_rows > 0) {
                while ($row = $resultado_meses->fetch_assoc()) {
                    $meses_abonados[] = $row['mes'];
                }
            }
            return $meses_abonados;
        }

        // Manejo de consultas por ID
        if (isset($_POST['id_persona'])) {
            $id_persona = $_POST['id_persona'];
            $sql_select_persona = "SELECT * FROM personas WHERE id = $id_persona";
        }
        // Manejo de consultas por Nombre y Apellido
        elseif (isset($_POST['nombre_apellido'])) {
            $nombre_apellido = $_POST['nombre_apellido'];
            $sql_select_persona = "SELECT * FROM personas WHERE nombre_apellido LIKE '%$nombre_apellido%'";
        }
        // Manejo de consultas por DNI
        elseif (isset($_POST['dni'])) {
            $dni = $_POST['dni'];
            $sql_select_persona = "SELECT * FROM personas WHERE dni = '$dni'";
        } else {
            echo "<p>No se proporcionó ningún parámetro de búsqueda válido.</p>";
            exit;
        }

        // Ejecutar consulta
        $resultado_persona = $conexion->query($sql_select_persona);

        if ($resultado_persona === false) {
            die("Error en la consulta: " . $conexion->error);
        }

        if ($resultado_persona->num_rows > 0) {
            $row_persona = $resultado_persona->fetch_assoc();
            echo "<h3>Información de la Persona:</h3>";
            echo "Nombre y Apellido: " . $row_persona['nombre_apellido'] . "<br>";
            echo "DNI: " . $row_persona['dni'] . "<br>";
            // Mostrar otros campos de información aquí...

            // Mostrar los 12 meses pendientes de pago con checkboxes
            echo "<h3>Pagos Pendientes:</h3>";
            echo "<form action='actualizar_pagos.php' method='POST'>";
            $meses_abonados = obtener_meses_abonados($conexion, $row_persona['id']);
            $meses_pendientes = array_diff(["enero", "febrero", "marzo", "abril", "mayo", "junio", "julio", "agosto", "septiembre", "octubre", "noviembre", "diciembre"], $meses_abonados);
            foreach ($meses_pendientes as $mes) {
                echo "<input type='checkbox' name='meses[]' value='$mes'>$mes<br>";
            }
            echo "<input type='hidden' name='id_persona' value='{$row_persona['id']}'>";
            echo "<input type='submit' value='Actualizar Pagos'>";
            echo "</form>";
        } else {
            echo "<p>No se encontró ninguna persona con el criterio de búsqueda proporcionado.</p>";
        }

        $conexion->close();
    }
    ?>
</body>
</html>


actualizar_pagos.php:

<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['meses'])) {
    $id_persona = $_POST['id_persona'];
    $meses_abonados = $_POST['meses'];

    // Conexión a la base de datos
    $conexion = new mysqli("localhost", "root", "", "personas_db");

    // Comprobar conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Eliminar los meses abonados de la tabla pagos_pendientes
    foreach ($meses_abonados as $mes) {
        $sql_delete_mes = "DELETE FROM pagos_pendientes WHERE persona_id = $id_persona AND mes = '$mes'";
        $conexion->query($sql_delete_mes);
    }

    // Insertar los pagos realizados en la tabla pagos_realizados
    foreach ($meses_abonados as $mes) {
        // Verificar si el pago ya fue realizado para evitar duplicados
        $sql_check_pago_realizado = "SELECT * FROM pagos_realizados WHERE persona_id = $id_persona AND mes = '$mes'";
        $resultado_check = $conexion->query($sql_check_pago_realizado);
        if ($resultado_check->num_rows == 0) {
            // Obtener el monto correspondiente al mes de la tabla montos_por_mes
            $sql_select_monto = "SELECT monto FROM montos_por_mes WHERE mes = '$mes'";
            $resultado_monto = $conexion->query($sql_select_monto);
            if ($resultado_monto->num_rows > 0) {
                $row = $resultado_monto->fetch_assoc();
                $monto = $row['monto'];

                // Insertar el pago realizado con el monto correspondiente
                $sql_insert_pago_realizado = "INSERT INTO pagos_realizados (persona_id, mes, monto) VALUES ($id_persona, '$mes', $monto)";
                $conexion->query($sql_insert_pago_realizado);
            }
        }
    }

    // Mostrar pagos realizados
    echo "<h3>Pagos Realizados:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Mes</th><th>Monto</th></tr>";
    foreach ($meses_abonados as $mes) {
        // Obtener el monto correspondiente al mes
        $sql_select_monto = "SELECT monto FROM montos_por_mes WHERE mes = '$mes'";
        $resultado_monto = $conexion->query($sql_select_monto);
        if ($resultado_monto->num_rows > 0) {
            $row = $resultado_monto->fetch_assoc();
            $monto = $row['monto'];
            echo "<tr><td>$mes</td><td>$monto</td></tr>";
        }
    }
    echo "</table>";

    echo "<button onclick='imprimirComprobante()'>Imprimir</button>";

    echo "Pagos actualizados correctamente.";

    $conexion->close();
}
?>

<script>
function imprimirComprobante() {
    window.print();
}
</script>


resetear_pagos.php:

<html>
    <head>
    <link rel="stylesheet" href="styles.css">
    </head>
    <body>
    <img src="C:\xampp\htdocs\sist\emogi.jpeg" />    
    </body>
<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['resetear_todos'])) {
    // Conexión a la base de datos
    $conexion = new mysqli("localhost", "root", "", "personas_db");

    // Comprobar conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Obtener todas las personas
    $sql_obtener_personas = "SELECT id FROM personas";
    $result = $conexion->query($sql_obtener_personas);

    if ($result->num_rows > 0) {
        // Iterar sobre todas las personas y restablecer los meses totales
        while ($row = $result->fetch_assoc()) {
            $id_persona = $row['id'];

            // Eliminar los pagos realizados y los pagos pendientes asociados a la persona
            $sql_delete_pagos_realizados = "DELETE FROM pagos_realizados WHERE persona_id = $id_persona";
            $sql_delete_pagos_pendientes = "DELETE FROM pagos_pendientes WHERE persona_id = $id_persona";

            if ($conexion->query($sql_delete_pagos_realizados) === FALSE || $conexion->query($sql_delete_pagos_pendientes) === FALSE) {
                echo "Error al restablecer los meses para la persona con ID $id_persona: " . $conexion->error;
            }
        }

        echo "Se han restablecido los meses totales para todos los usuarios correctamente.";
    } else {
        echo "No se encontraron usuarios en la base de datos.";
    }

    // Cerrar la conexión
    $conexion->close();
}
?>


</html>