<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Registrar Pagos</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css">
    <style>
        body {
            padding: 20px;
        }

        .container {
            max-width: 800px;
        }

        .mt-4 {
            margin-top: 1.5rem !important;
        }
    </style>
</head>

<body class="container mt-4">
    <a href="http://localhost/sist/index.html" class="btn btn-primary" target="_self">Volver a pantalla principal</a>

    <?php
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['meses'])) {
        $id_persona = $_POST['id_persona'];
        $meses_abonados = $_POST['meses'];

        // Conexión a la base de datos
        $conexion = new mysqli("localhost", "root", "", "personas_db");

        // Comprobar conexión
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }

        // Eliminar los meses abonados de la tabla pagos_pendientes
        foreach ($meses_abonados as $mes) {
            $sql_delete_mes = "DELETE FROM pagos_pendientes WHERE persona_id = $id_persona AND mes = '$mes'";
            if ($conexion->query($sql_delete_mes) === FALSE) {
                die("Error al eliminar los meses abonados: " . $conexion->error);
            }
        }

        // Insertar los pagos realizados en la tabla pagos_realizados
        foreach ($meses_abonados as $mes) {
            // Verificar si el pago ya fue realizado para evitar duplicados
            $sql_check_pago_realizado = "SELECT * FROM pagos_realizados WHERE persona_id = $id_persona AND mes = '$mes'";
            $resultado_check = $conexion->query($sql_check_pago_realizado);
            if ($resultado_check === FALSE) {
                die("Error al verificar el pago realizado: " . $conexion->error);
            }
            if ($resultado_check->num_rows == 0) {
                // Obtener el monto correspondiente al mes de la tabla montos_por_mes
                $sql_select_monto = "SELECT monto FROM montos_por_mes WHERE mes = '$mes'";
                $resultado_monto = $conexion->query($sql_select_monto);
                if ($resultado_monto->num_rows > 0) {
                    $row = $resultado_monto->fetch_assoc();
                    $monto = $row['monto'];

                    // Insertar el pago realizado con el monto correspondiente
                    $sql_insert_pago_realizado = "INSERT INTO pagos_realizados (persona_id, mes, monto) VALUES ($id_persona, '$mes', $monto)";
                    if ($conexion->query($sql_insert_pago_realizado) === FALSE) {
                        die("Error al insertar el pago realizado: " . $conexion->error);
                    }
                }
            }
        }

        // Mostrar pagos realizados
        echo "<h3 class='mt-4'>Pagos Realizados:</h3>";
        echo "<table class='table table-bordered'>";
        echo "<thead class='thead-dark'><tr><th>Mes</th><th>Monto</th></tr></thead>";
        echo "<tbody>";
        foreach ($meses_abonados as $mes) {
            // Obtener el monto correspondiente al mes
            $sql_select_monto = "SELECT monto FROM montos_por_mes WHERE mes = '$mes'";
            $resultado_monto = $conexion->query($sql_select_monto);
            if ($resultado_monto->num_rows > 0) {
                $row = $resultado_monto->fetch_assoc();
                $monto = $row['monto'];
                echo "<tr><td>$mes</td><td>$ $monto</td></tr>";
            }
        }
        echo "</tbody></table>";

        echo "<button class='btn btn-primary' onclick='imprimirComprobante()'>Imprimir</button>";

        echo "<p class='mt-4'>Pagos actualizados correctamente.</p>";

        $conexion->close();
    }
    ?>

    <script src='https://code.jquery.com/jquery-3.2.1.slim.min.js'></script>
    <script src='https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.12.9/umd/popper.min.js'></script>
    <script src='https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js'></script>

    <script>
        function imprimirComprobante() {
            window.print();
        }
    </script>
</body>

</html>
