<?php 
session_start(); // Inicia la sesión

// Incluir la conexión a la base de datos
include 'services/dbcon.php';
$conexion = conectar();

// Verificar si el usuario está autenticado y si tiene el usuario adecuado
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_usuario'] !== 2) {
    $mensaje = '<div class="alerta">No tienes permiso para acceder a esta página. Serás redirigido al login.</div>';
    // Si no está autenticado o no es el usuario adecuado, redirigir al login
    header("Location: http://localhost/Turnos_Estetica/login.php");
    exit(); // Detener la ejecución del script
}


// Obtener el turno atendido desde la sesión
$turno_atendido = isset($_SESSION['turno_atendido']) ? $_SESSION['turno_atendido'] : null;
$servicio_atendido = isset($_SESSION['servicio_atendido']) ? $_SESSION['servicio_atendido'] : null;

// Obtener todos los turnos del día actual agrupados por num_turno
$sql_turnos = "SELECT t.num_turno, GROUP_CONCAT(s.nombre_serv SEPARATOR ', ') AS servicios 
               FROM turno t 
               JOIN ventas v ON t.id_turno = v.id_turno 
               JOIN servicios s ON v.id_servicio = s.id_servicio 
               WHERE t.estado = 1 AND t.fecha_turno = CURDATE() 
               GROUP BY t.num_turno 
               ORDER BY t.num_turno ASC";

$result_turnos = $conexion->query($sql_turnos);
$turnos = [];
while ($row = $result_turnos->fetch(PDO::FETCH_ASSOC)) {
    $turnos[] = $row;
}

// Cerrar conexión
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siguientes Turnos</title>
    <link rel="stylesheet" type="text/css" href="styles/styles2turnos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body>

<div class="contenedor-turno">
    <div class="turno-actual">TURNO A PASAR</div>
    
    <?php
    // Mostrar los valores
echo "<script>alert('Turno atendido: " . json_encode($turno_atendido) . "\\nServicio atendido: " . json_encode($servicio_atendido) . "');</script>";
?>
    <?php if ($turno_atendido && $servicio_atendido): ?>
        <div class="turno-atendido"><?php echo $turno_atendido . " " . $servicio_atendido; ?></div>
        
    <?php endif; ?>
    
    <?php if (count($turnos) > 0): ?>
        <div class="despues">Siguientes</div>
        <div class="siguientes">
            <?php foreach ($turnos as $index => $turno): ?>
                <div class="siguiente-turno <?php echo $index === 2 ? 'highlight' : ''; ?>">
                    <div class="numero"><?php echo $turno['num_turno']; ?></div>
                    <?php if (!empty($turno['servicios'])): ?>
                        <div class="servicio"><?php echo $turno['servicios']; ?></div>
                    <?php else: ?>
                        <div class="servicio">No hay servicios asociados</div>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="numero-turno">No hay turnos</div>
    <?php endif; ?>
</div>

<script>
    // Actualizar la vista cada 5 segundos
    setInterval(function() {
        location.reload();
    }, 5000);
</script>

</body>
</html>
