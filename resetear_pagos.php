<?php
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['resetear_todos'])) {
    // Conexión a la base de datos
    $conexion = new mysqli("localhost", "root", "", "personas_db");

    // Comprobar conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Obtener el año actual
    $ano_actual = date("Y");

    // Obtener todas las personas
    $sql_obtener_personas = "SELECT id FROM personas";
    $result = $conexion->query($sql_obtener_personas);

    if ($result->num_rows > 0) {
        // Iterar sobre todas las personas y restablecer los meses totales
        while ($row = $result->fetch_assoc()) {
            $id_persona = $row['id'];

            // Eliminar los pagos realizados y los pagos pendientes asociados a la persona para el año actual
            $sql_delete_pagos_realizados = "DELETE FROM pagos_realizados WHERE persona_id = $id_persona AND YEAR(fecha_pago) = $ano_actual";
            $sql_delete_pagos_pendientes = "DELETE FROM pagos_pendientes WHERE persona_id = $id_persona";

            if ($conexion->query($sql_delete_pagos_realizados) === FALSE || $conexion->query($sql_delete_pagos_pendientes) === FALSE) {
                echo "Error al restablecer los meses para la persona con ID $id_persona: " . $conexion->error;
            }
        }

        echo "Se han restablecido los meses totales para todos los usuarios del año actual correctamente.";
    } else {
        echo "No se encontraron usuarios en la base de datos.";
    }

    // Cerrar la conexión
    $conexion->close();
}
?>
