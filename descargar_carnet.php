<?php
require_once('tcpdf/tcpdf.php');

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$database = "personas_db";

// Crear conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $database);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Obtener el ID de la persona desde la URL
$id_persona = $_GET['id_persona']; // Asegúrate de que esté definido y sea seguro

// Obtener información de la persona desde la base de datos
$sql = "SELECT nombre_apellido, dni FROM personas WHERE id = $id_persona";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();

    // Crear un nuevo objeto TCPDF
    $pdf = new TCPDF();
    
    // Agregar una página
    $pdf->AddPage();
    
    // Configurar contenido (cambiar según tus necesidades)
    $pdf->SetFillColor(255, 255, 0); // Amarillo
    $pdf->Rect(10, 10, 110, 70, 'F'); // Rectángulo amarillo

    $content = "Nombre: " . $row['nombre_apellido'] . "\n";
    $content .= "DNI: " . $row['dni'] . "\n";

    // Agregar contenido al PDF
    $pdf->writeHTML($content, true, false, true, false, '');

    // Enviar el PDF al navegador
    $pdf->Output('carnet.pdf', 'I');

    // Cerrar la conexión
    $conn->close();
} else {
    echo "No se encontró información para el ID de persona proporcionado.";
}
?>
