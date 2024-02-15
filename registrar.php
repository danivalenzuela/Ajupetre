<?php
// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "personas";

// Crear conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Función para registrar una persona
function registrarPersona($nombre_apellido, $dni, $fecha_nacimiento, $direccion, $telefono, $email, $fecha_afiliacion, $conn) {
    // Preparar la consulta SQL
    $stmt = $conn->prepare("INSERT INTO personas (nombre_apellido, dni, fecha_nacimiento, direccion, telefono, email, fecha_afiliacion) VALUES (?, ?, ?, ?, ?, ?, ?)");
    if (!$stmt) {
        die("Error al preparar la consulta: " . $conn->error);
    }
    
    // Bind parameters
    $stmt->bind_param("sssssss", $nombre_apellido, $dni, $fecha_nacimiento, $direccion, $telefono, $email, $fecha_afiliacion);
    if (!$stmt->execute()) {
        die("Error al ejecutar la consulta: " . $stmt->error);
    }

    // Cerrar la consulta
    $stmt->close();

    // Asociar el abono mensual de $1000 por los 12 meses del año
    $id_persona = $conn->insert_id;
    for ($mes = 1; $mes <= 12; $mes++) {
        $fecha_pago = date("Y-m-d", mktime(0, 0, 0, $mes, 1, date("Y")));
        $stmt = $conn->prepare("INSERT INTO pagos (id_persona, fecha_pago, monto) VALUES (?, ?, 1000)");
        if (!$stmt) {
            die("Error al preparar la consulta de pagos: " . $conn->error);
        }
        $stmt->bind_param("is", $id_persona, $fecha_pago);
        if (!$stmt->execute()) {
            die("Error al ejecutar la consulta de pagos: " . $stmt->error);
        }
        $stmt->close();
    }

    echo "Persona registrada con éxito.";
}
