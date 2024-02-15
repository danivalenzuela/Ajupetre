<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Reporte de Cobros</title>
    <link rel="stylesheet" href="styles.css">
    <script>
        function imprimirReporte() {
            window.print();
        }
    </script>
    <style>
        /* Estilos CSS para ocultar el botón de impresión en la versión impresa */
        @media print {
            .btn-imprimir {
                display: none;
            }
        }
    </style>
</head>
<body>
    <h1>Reporte de Cobros</h1>
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

    // Generar el reporte de total cobrado por usuario
    echo "<h2>Total Cobrado por Usuario:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Nombre_Apellido</th><th>Total_Cobrado</th></tr>";
    while ($row = $resultado_total_cobrado->fetch_assoc()) {
        echo "<tr><td>{$row['nombre_apellido']}</td><td>{$row['total_cobrado']}</td></tr>";
    }
    echo "</table>";

    // Consultar los cobros por mes y usuario
    $sql_cobros_por_mes = "SELECT p.nombre_apellido, pr.mes, pr.monto FROM personas p JOIN pagos_realizados pr ON p.id = pr.persona_id";
    $resultado_cobros_por_mes = $conexion->query($sql_cobros_por_mes);

    if ($resultado_cobros_por_mes === false) {
        die("Error en la consulta: " . $conexion->error);
    }

    // Generar el reporte de cobros por mes y usuario
    echo "<h2>Cobros por Mes y Usuario:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Nombre_Apellido</th><th>Mes</th><th>Monto</th></tr>";
    while ($row = $resultado_cobros_por_mes->fetch_assoc()) {
        echo "<tr><td>{$row['nombre_apellido']}</td><td>{$row['mes']}</td><td>{$row['monto']}</td></tr>";
    }
    echo "</table>";

    // Consultar los meses pendientes de pago por usuario
    $sql_meses_pendientes = "SELECT p.nombre_apellido, m.mes FROM personas p JOIN montos_por_mes m ON p.id = m.id WHERE NOT EXISTS (SELECT 1 FROM pagos_mensuales pm WHERE pm.persona_id = p.id AND pm.mes = m.mes)";
    $resultado_meses_pendientes = $conexion->query($sql_meses_pendientes);

    if ($resultado_meses_pendientes === false) {
        die("Error en la consulta: " . $conexion->error);
    }

    // Generar el reporte de meses pendientes de pago por usuario
    echo "<h2>Meses Pendientes de Pago por Usuario:</h2>";
    echo "<table border='1'>";
    echo "<tr><th>Nombre_Apellido</th><th>Mes Pendiente</th></tr>";
    while ($row = $resultado_meses_pendientes->fetch_assoc()) {
        echo "<tr><td>{$row['nombre_apellido']}</td><td>{$row['mes']}</td></tr>";
    }
    echo "</table>";

    // Cerrar la conexión
    $conexion->close();
    ?>

    <!-- Botón para imprimir el reporte -->
    <button class="btn-imprimir" onclick="imprimirReporte()">Imprimir Reporte</button>
</body>
</html>
