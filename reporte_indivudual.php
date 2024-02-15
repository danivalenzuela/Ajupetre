<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "personas_db");

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Consultar usuarios
$sql_usuarios = "SELECT * FROM personas";
$resultado_usuarios = $conexion->query($sql_usuarios);

if ($resultado_usuarios === false) {
    die("Error en la consulta: " . $conexion->error);
}

// Generar reporte para cada usuario
while ($row_usuario = $resultado_usuarios->fetch_assoc()) {
    $id_persona = $row_usuario['id'];
    $nombre_apellido = $row_usuario['nombre_apellido'];

    // Consultar pagos realizados por el usuario
    $sql_pagos_realizados = "SELECT * FROM pagos_realizados WHERE persona_id = $id_persona";
    $resultado_pagos_realizados = $conexion->query($sql_pagos_realizados);

    // Consultar pagos pendientes por el usuario
    $sql_pagos_pendientes = "SELECT * FROM pagos_pendientes WHERE persona_id = $id_persona";
    $resultado_pagos_pendientes = $conexion->query($sql_pagos_pendientes);

    // Generar archivo HTML para el usuario
    $archivo_usuario = fopen("reporte_$nombre_apellido.html", "w") or die("No se pudo crear el archivo.");
    fwrite($archivo_usuario, "<h2>Reporte para $nombre_apellido</h2>");

    // Mostrar pagos realizados
    fwrite($archivo_usuario, "<h3>Pagos Realizados:</h3>");
    if ($resultado_pagos_realizados->num_rows > 0) {
        while ($row_pago_realizado = $resultado_pagos_realizados->fetch_assoc()) {
            fwrite($archivo_usuario, "Mes: " . $row_pago_realizado['mes'] . ", Monto: $ " . $row_pago_realizado['monto'] . "<br>");
        }
    } else {
        fwrite($archivo_usuario, "No hay pagos realizados.<br>");
    }

    // Mostrar pagos pendientes
    fwrite($archivo_usuario, "<h3>Pagos Pendientes:</h3>");
    if ($resultado_pagos_pendientes->num_rows > 0) {
        while ($row_pago_pendiente = $resultado_pagos_pendientes->fetch_assoc()) {
            fwrite($archivo_usuario, "Mes: " . $row_pago_pendiente['mes'] . "<br>");
        }
    } else {
        fwrite($archivo_usuario, "No hay pagos pendientes.<br>");
    }

    fclose($archivo_usuario);
}

echo "Se han generado los reportes individuales para cada usuario.";

$conexion->close();
?>
