<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Registro de Persona</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
</head>
<body>
    <a href="http://localhost/sist/index.html" target="_self" class="btn btn-secondary m-2">
        Volver a pantalla principal
    </a>
    <div class="container mt-4">
        <h2>Registro de Persona</h2>
        <form action="procesar_registro.php" method="POST" enctype="multipart/form-data" class="form-registro">
            <div class="form-group">
                <label for="nombre_apellido">Nombre y Apellido:</label>
                <input type="text" id="nombre_apellido" name="nombre_apellido" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="dni">DNI:</label>
                <input type="text" id="dni" name="dni" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="fecha_nacimiento">Fecha de Nacimiento:</label>
                <input type="date" id="fecha_nacimiento" name="fecha_nacimiento" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="direccion">Dirección:</label>
                <input type="text" id="direccion" name="direccion" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="telefono">Teléfono:</label>
                <input type="text" id="telefono" name="telefono" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="email">Email:</label>
                <input type="email" id="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="fecha_afiliacion">Fecha de Afiliación:</label>
                <input type="date" id="fecha_afiliacion" name="fecha_afiliacion" class="form-control" required>
            </div>
            <div class="form-group">
                <label for="foto">Foto:</label>
                <input type="file" id="foto" name="foto" class="form-control" required>
            </div>
            <input type="submit" value="Registrar" class="btn btn-primary">
        </form>
    </div>
    <div class="consulta-container text-center mt-4">
        <a href="http://localhost/sist/consultar_usuario.php" class="btn btn-info" target="_self">
            ¡Consulta pago pendientes por Afiliado!
        </a> 
    </div>
    <script src="https://code.jquery.com/jquery-3.2.1.slim.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js"></script>
</body>
</html>
