<?php
// Verificar si se han enviado los datos del formulario
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los nuevos precios por mes desde el formulario
    $enero = $_POST["enero"];
    // Repetir para los demás meses

    // Conexión a la base de datos
    $conexion = new mysqli("localhost", "root", "", "personas_db");

    // Comprobar conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Actualizar el precio del mes en la base de datos
    $sql_actualizar_precio = "UPDATE montos_por_mes SET monto = ? WHERE mes = 'enero'";
    
    // Preparar la consulta
    $stmt = $conexion->prepare($sql_actualizar_precio);
    
    // Verificar si la preparación de la consulta fue exitosa
    if ($stmt === false) {
        echo "Error al preparar la consulta: " . $conexion->error . "<br>";
    } else {
        // Asociar parámetros
        $stmt->bind_param("d", $enero);
        
        // Ejecutar la consulta preparada
        if ($stmt->execute()) {
            echo "Precio de enero actualizado correctamente.<br>";
        } else {
            echo "Error al actualizar el precio de enero: " . $stmt->error . "<br>";
        }
        
        // Cerrar la consulta preparada
        $stmt->close();
    }

    // Cerrar la conexión
    $conexion->close();
}
?>
