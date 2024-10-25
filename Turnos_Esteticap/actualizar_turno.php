<?php
$conexion = new mysqli('localhost', 'tu_usuario', 'tu_contraseña', 'epturnos');

if ($conexion->connect_error) {
    die("Conexión fallida: " . $conexion->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $turno_numero = $_POST['turno_numero'];
    $accion = $_POST['accion'];

    if ($accion === 'pasar') {
        // Actualizar estado a no atendido (2)
        $query = "UPDATE turno SET estado = 2 WHERE num_turno = ? AND estado = 1";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param('i', $turno_numero);
        $stmt->execute();
    } elseif ($accion === 'atender') {
        // Actualizar estado a atendido (3)
        $query = "UPDATE turno SET estado = 3 WHERE num_turno = ? AND estado = 1";
        $stmt = $conexion->prepare($query);
        $stmt->bind_param('i', $turno_numero);
        $stmt->execute();
    }

    // Redirigir a la vista de siguientes turnos
    header("Location: siguientesTurnos.php");
    exit();
}

$conexion->close();
?>
