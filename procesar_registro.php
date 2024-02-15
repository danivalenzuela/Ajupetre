<?php
// Conexión a la base de datos
$conexion = new mysqli("localhost", "root", "", "personas_db");

// Comprobar conexión
if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

// Obtener los datos del formulario
$nombre_apellido = $_POST['nombre_apellido'];
$dni = $_POST['dni'];
$fecha_nacimiento = $_POST['fecha_nacimiento'];
$direccion = $_POST['direccion'];
$telefono = $_POST['telefono'];
$email = $_POST['email'];
$fecha_afiliacion = $_POST['fecha_afiliacion'];

// Insertar datos en la tabla personas
$sql_insert_persona = "INSERT INTO personas (nombre_apellido, dni, fecha_nacimiento, direccion, telefono, email, fecha_afiliacion)
VALUES ('$nombre_apellido', '$dni', '$fecha_nacimiento', '$direccion', '$telefono', '$email', '$fecha_afiliacion')";

if ($conexion->query($sql_insert_persona) === TRUE) {
    echo "Registro de persona exitoso";
} else {
    echo "Error al registrar persona: " . $conexion->error;
}

// Asociar el abono mensual
$persona_id = $conexion->insert_id; // Obtener el ID de la persona recién insertada

$meses = array();
$sql_select_meses = "SELECT * FROM montos_por_mes";
$resultado_meses = $conexion->query($sql_select_meses);
if ($resultado_meses->num_rows > 0) {
    while ($row_mes = $resultado_meses->fetch_assoc()) {
        $meses[$row_mes['mes']] = $row_mes['monto'];
    }
}

foreach ($meses as $mes => $monto) {
    $sql_insert_pago = "INSERT INTO pagos_mensuales (persona_id, mes, monto) VALUES ($persona_id, '$mes', $monto)";
    $conexion->query($sql_insert_pago);
}

echo "Se han asociado los abonos mensuales.";

$conexion->close();
?>
