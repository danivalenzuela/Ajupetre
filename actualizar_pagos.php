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
    echo "<h3>Pagos Realizados:</h3>";
    echo "<table border='1'>";
    echo "<tr><th>Mes</th><th>Monto</th></tr>";
    foreach ($meses_abonados as $mes) {
        // Obtener el monto correspondiente al mes
        $sql_select_monto = "SELECT monto FROM montos_por_mes WHERE mes = '$mes'";
        $resultado_monto = $conexion->query($sql_select_monto);
        if ($resultado_monto->num_rows > 0) {
            $row = $resultado_monto->fetch_assoc();
            $monto = $row['monto'];
            echo "<tr><td>$mes</td><td>$monto</td></tr>";
        }
    }
    echo "</table>";

    echo "<button onclick='imprimirComprobante()'>Imprimir</button>";

    echo "Pagos actualizados correctamente.";

    $conexion->close();
}
?>

<script>
function imprimirComprobante() {
    window.print();
}
</script>
