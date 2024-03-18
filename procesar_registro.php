<?php
require('vendor/setasign/fpdf/fpdf.php');

if ($_SERVER["REQUEST_METHOD"] == "POST") {
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

    // Obtener datos del formulario
    $nombre_apellido = $_POST["nombre_apellido"];
    $dni = $_POST["dni"];
    $numero_afiliado = obtenerNumeroAfiliado($conexion); // Nueva función para obtener el número de afiliado
    $foto = "C:/xampp/htdocs/sist/fotos/" . $_FILES["foto"]["name"];
    $fecha_afiliacion = $_POST["fecha_afiliacion"];
    $fecha_nacimiento = $_POST["fecha_nacimiento"];
    $direccion = $_POST["direccion"];
    $telefono = $_POST["telefono"];
    $email = $_POST["email"]; // Agregado campo email

    // Verificar si el DNI ya existe
    $sql_verificar_dni = "SELECT * FROM personas WHERE dni = '$dni'";
    $resultado_verificacion = $conexion->query($sql_verificar_dni);

    if ($resultado_verificacion->num_rows > 0) {
        // El DNI ya está registrado
        echo "<div class='alert alert-danger' role='alert'>";
        echo "YA EXISTE UN USUARIO REGISTRADO CON ESTE NUMERO DE DOCUMENTO. FAVOR DE VALIDAR DATOS";
        echo "</div>";
        echo "<a href='http://localhost/sist/registro.html' class='btn btn-primary'>Volver al formulario de registro</a>";
    } else {
        // Mover la foto al directorio deseado
        move_uploaded_file($_FILES["foto"]["tmp_name"], $foto);

        // Ajustar el tamaño de la imagen
        ajustarTamanoImagen($foto, 120, 160); // Ajusta según tus necesidades

        // Insertar datos en la base de datos
        $sql_insertar_persona = "INSERT INTO personas (nombre_apellido, dni, numero_afiliado,fecha_nacimiento,direccion,telefono,email, foto,fecha_afiliacion) VALUES ('$nombre_apellido', '$dni', '$numero_afiliado','$fecha_nacimiento','$direccion','$telefono','$email','$foto','$fecha_afiliacion')";

        if ($conexion->query($sql_insertar_persona) === TRUE) {
            // Registro exitoso, ahora generamos el carnet en PDF
            generarCarnetPDF($nombre_apellido, $dni, $numero_afiliado, $foto);

            // Redireccionar a la página de generación de carnet con el DNI
            header("Location: generar_carnet.php?dni=$dni");
            exit();
        } else {
            echo "<div class='alert alert-danger' role='alert'>";
            echo "Error en el registro: " . $conexion->error;
            echo "</div>";
        }
    }

    // Cerrar la conexión
    $conexion->close();
}

function obtenerNumeroAfiliado($conexion) {
    // Obtener el último número de afiliado y sumar 1
    $sql_ultimo_numero = "SELECT MAX(numero_afiliado) as ultimo_numero FROM personas";
    $resultado = $conexion->query($sql_ultimo_numero);

    if ($resultado->num_rows > 0) {
        $fila = $resultado->fetch_assoc();
        $ultimo_numero = $fila["ultimo_numero"];
        return $ultimo_numero + 1;
    } else {
        // Si no hay registros, empezar desde 1
        return 1;
    }
}

function generarCarnetPDF($nombre_apellido, $dni, $numero_afiliado, $foto) {
    // El código de generación de carnet permanece igual
    // ...
}

// Función para ajustar el tamaño de la imagen utilizando la librería GD
function ajustarTamanoImagen($imagen, $nuevoAncho, $nuevoAlto) {
    // ...
}
?>
