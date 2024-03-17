<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Cobros</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <link rel="stylesheet" href="styles.css">
    <style>
        /* Estilos CSS para ocultar el botón de impresión en la versión impresa */
        @media print {
            .btn-imprimir {
                display: none;
            }
        }
    </style>
</head>
<body class="container mt-4">
    <a href="http://localhost/sist/index.html" target="_self" class="btn btn-primary mb-3">
        Volver a pantalla principal
    </a>
    <h1 class="mt-4">Reporte de Cobros</h1>

    <!-- Aquí va el código PHP para generar el reporte -->
    <?php
    // Conexión a la base de datos
    $conexion = new mysqli("localhost", "root", "", "personas_db");

    // Comprobar conexión
    if ($conexion->connect_error) {
        die("Error de conexión: " . $conexion->connect_error);
    }

    // Consultar el total cobrado por cada usuario
    $sql_total_cobrado = "SELECT p.nombre_apellido, SUM(pr.monto) AS total_cobrado FROM personas p JOIN pagos_realizados pr ON p.id = pr.persona_id GROUP BY p.nombre_apellido";
    $resultado_total_cobrado = $conexion->query($sql_total_cobrado);

    if ($resultado_total_cobrado === false) {
        die("Error en la consulta: " . $conexion->error);
    }

    // Calcular el total cobrado (suma de todo lo cobrado por usuario)
    $total_cobrado_global = 0;
    while ($row = $resultado_total_cobrado->fetch_assoc()) {
        $total_cobrado_global += $row['total_cobrado'];
    }

    // Mostrar el total cobrado global
    echo "<h2>Total Recaudado: $ $total_cobrado_global</h2>";

    // Regresar el puntero del resultado al inicio para mostrar la tabla
    mysqli_data_seek($resultado_total_cobrado, 0);

    // Generar el reporte de total cobrado por usuario
    echo "<h2>Total Cobrado por Usuario:</h2>";
    echo "<table class='table table-bordered'>";
    echo "<thead class='thead-dark'><tr><th>Nombre_Apellido</th><th>Total_Cobrado</th></tr></thead>";
    echo "<tbody>";
    while ($row = $resultado_total_cobrado->fetch_assoc()) {
        echo "<tr><td>{$row['nombre_apellido']}</td><td>$ {$row['total_cobrado']}</td></tr>";
    }
    echo "</tbody></table>";

    // Consultar los cobros por mes y usuario
    $sql_cobros_por_mes = "SELECT p.nombre_apellido, pr.mes, SUM(pr.monto) AS total_monto FROM personas p JOIN pagos_realizados pr ON p.id = pr.persona_id GROUP BY p.nombre_apellido, pr.mes";
    $resultado_cobros_por_mes = $conexion->query($sql_cobros_por_mes);

    if ($resultado_cobros_por_mes === false) {
        die("Error en la consulta: " . $conexion->error);
    }

    // Generar el reporte de cobros por mes y usuario
    echo "<h2>Cobros por Mes y Usuario:</h2>";
    echo "<table class='table table-bordered'>";
    echo "<thead class='thead-dark'><tr><th>Nombre_Apellido</th><th>Mes</th><th>Total_Monto</th></tr></thead>";
    echo "<tbody>";
    while ($row = $resultado_cobros_por_mes->fetch_assoc()) {
        echo "<tr><td>{$row['nombre_apellido']}</td><td>{$row['mes']}</td><td>$ {$row['total_monto']}</td></tr>";
    }
    echo "</tbody></table>";

    // Consultar los meses pendientes de pago por usuario
    $sql_meses_pendientes = "SELECT p.nombre_apellido, GROUP_CONCAT(m.mes ORDER BY m.mes) AS meses_pendientes
                             FROM personas p 
                             CROSS JOIN (
                                 SELECT 'enero' AS mes UNION
                                 SELECT 'febrero' AS mes UNION
                                 SELECT 'marzo' AS mes UNION
                                 SELECT 'abril' AS mes UNION
                                 SELECT 'mayo' AS mes UNION
                                 SELECT 'junio' AS mes UNION
                                 SELECT 'julio' AS mes UNION
                                 SELECT 'agosto' AS mes UNION
                                 SELECT 'septiembre' AS mes UNION
                                 SELECT 'octubre' AS mes UNION
                                 SELECT 'noviembre' AS mes UNION
                                 SELECT 'diciembre' AS mes
                             ) m
                             LEFT JOIN pagos_realizados pr ON p.id = pr.persona_id AND m.mes = pr.mes
                             WHERE pr.persona_id IS NULL
                             GROUP BY p.nombre_apellido";
    $resultado_meses_pendientes = $conexion->query($sql_meses_pendientes);

    if ($resultado_meses_pendientes === false) {
        die("Error en la consulta: " . $conexion->error);
    }

    // Generar el reporte de meses pendientes de pago por usuario
    echo "<h2>Meses Pendientes de Pago por Usuario:</h2>";
    echo "<table class='table table-bordered'>";
    echo "<thead class='thead-dark'><tr><th>Nombre_Apellido</th><th>Meses Pendientes</th></tr></thead>";
    echo "<tbody>";
    while ($row = $resultado_meses_pendientes->fetch_assoc()) {
        echo "<tr><td>{$row['nombre_apellido']}</td><td>{$row['meses_pendientes']}</td></tr>";
    }
    echo "</tbody></table>";

    // Cerrar la conexión
    $conexion->close();
    ?>

    <!-- Botón para imprimir el reporte -->
    <button class='btn btn-primary btn-imprimir mt-3' onclick='imprimirReporte()'>Imprimir Reporte</button>

    <script src='https://code.jquery.com/jquery-3.2.1.slim.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js'></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js'></script>
</body>
</html>
