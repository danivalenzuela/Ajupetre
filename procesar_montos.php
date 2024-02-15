<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "personas_db");

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener los datos del formulario
$mes = $_POST['mes'];
$nuevo_monto = $_POST['nuevo_monto'];

// Verificar si ya se han realizado pagos para este mes
$sql_verificar_pagos = "SELECT * FROM pagos_realizados WHERE mes = '$mes'";

$resultado_verificar = $conexion->query($sql_verificar_pagos);

if ($resultado_verificar->num_rows > 0) {
    echo "Ya se han realizado pagos para el mes de $mes. No se puede modificar el monto.";
} else {
    // Actualizar el monto del mes especificado en la base de datos
    $sql_actualizar_monto = "UPDATE pagos_mensuales SET monto = $nuevo_monto WHERE mes = '$mes'";

    if ($conexion->query($sql_actualizar_monto) === TRUE) {
        echo "Monto del mes $mes actualizado correctamente.";

        // Insertar el monto actualizado en la tabla de montos históricos
        $sql_insert_monto_historico = "INSERT INTO montos_historicos (mes, monto) VALUES ('$mes', $nuevo_monto)";
        $conexion->query($sql_insert_monto_historico);
    } else {
        echo "Error al actualizar el monto: " . $conexion->error;
    }
}

$conexion->close();
?>
