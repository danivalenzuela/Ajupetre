<?php
require('vendor/setasign/fpdf/fpdf.php');

if (isset($_GET['dni'])) {
    // Conectar a la base de datos (modifica según tus credenciales)
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "personas_db";

    $conexion = new mysqli($servername, $username, $password, $dbname);

    // Verificar la conexión
    if ($conexion->connect_error) {
        die("Conexión fallida: " . $conexion->connect_error);
    }

    // Obtener el DNI de la URL
    $dni = $_GET['dni'];

    // Obtener datos del usuario
    $sql_obtener_usuario = "SELECT * FROM personas WHERE dni = '$dni'";
    $resultado_usuario = $conexion->query($sql_obtener_usuario);

    if ($resultado_usuario->num_rows > 0) {
        $row_usuario = $resultado_usuario->fetch_assoc();

        // Crear HTML con Bootstrap
        echo '<!DOCTYPE html>
        <html lang="es">
        <head>
        <a href="http://localhost/sist/index.html" target="_self" class="btn btn-secondary m-2">
        Volver a pantalla principal
    </a>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
            <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/css/bootstrap.min.css">
            <title>Carnet</title>
        </head>
        <body>
            <div class="container mt-5">
                <div class="card" style="width: 18rem;">
                    <div class="card-header bg-primary text-white">
                        A.Ju.Pe.Tre
                    </div>
                    <div class="card-body">
                        <h5 class="card-title">Nombre: ' . $row_usuario['nombre_apellido'] . '</h5>
                        <p class="card-text">DNI: ' . $row_usuario['dni'] . '</p>
                        <p class="card-text">Nro. Afiliado: ' . $row_usuario['numero_afiliado'] . '</p>';
                        
        // Agregar la foto si existe
        $foto = $row_usuario['foto'];
        if (file_exists($foto)) {
            $rutaImagen = '/sist/fotos/' . basename($foto); // Ajustar la ruta según la estructura de tu proyecto
            echo '<img src="' . $rutaImagen . '" class="card-img-bottom" alt="Foto">';
        } else {
            echo '<p class="card-text">No Foto</p>';
        }

        echo '          </div>
                </div>
            </div>
            <script src="https://code.jquery.com/jquery-3.3.1.slim.min.js"></script>
            <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.14.7/umd/popper.min.js"></script>
            <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.3.1/js/bootstrap.min.js"></script>
        </body>
        </html>';

        // Cerrar la conexión
        $conexion->close();
        exit();
    } else {
        echo "No se encontró ningún usuario con el DNI proporcionado.";
    }

    // Cerrar la conexión
    $conexion->close();
} else {
    echo "No se proporcionó el DNI.";
}
?>
