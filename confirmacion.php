<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Confirmación</title>
</head>
<body>
    <h1>¡Registro exitoso!</h1>
    <p>El carnet de socio ha sido generado correctamente.</p>
    <?php
    // Obtener el ID de la persona desde la URL
    $id_persona = $_GET['id_persona']; // Asegúrate de que esté definido y sea seguro

    // Redirigir a descargar_carnet.php pasando el ID de la persona
    echo "<p><a href='descargar_carnet.php?id_persona=$id_persona' target='_blank'>Descargar e imprimir el carnet de socio</a></p>";
    ?>
</body>
</html>
