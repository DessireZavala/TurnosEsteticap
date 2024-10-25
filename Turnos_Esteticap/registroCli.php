<?php
session_start();

// Verificar si el usuario está autenticado y tiene el rol adecuado
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_usuario'] !== 2) {
    $mensaje = '<div class="alerta">No tienes permiso para acceder a esta página. Serás redirigido al login.</div>';
    header("location: http://localhost/Turnos_Estetica/login.php");
    exit();
}

// Incluir la conexión a la base de datos
include("services/dbcon.php");
$conexion = conectar();

// Obtener solo los servicios activos
$stmt = $conexion->prepare("SELECT * FROM servicios WHERE activo = 1");
$stmt->execute();
$servicios = $stmt->fetchAll();

$mensaje = '';
$totalCosto = 0;

if (!empty($_POST["registro"])) {
    if (empty($_POST["nombre_clientes"]) || empty($_POST["ap_clientes"]) || empty($_POST["am_clientes"])) {
        $mensaje = '<div class="alerta">Uno de los campos está vacío</div>';
    } else {
        $nombre = $_POST["nombre_clientes"];
        $apat = $_POST["ap_clientes"];
        $amat = $_POST["am_clientes"];

        // Iniciar la transacción
        $conexion->beginTransaction();

        try {
            // Verificar si ya existe el cliente
            $stmtVerificar = $conexion->prepare("SELECT id_cliente FROM clientes WHERE nombre_clientes = :nombre AND ap_clientes = :apat AND am_clientes = :amat");
            $stmtVerificar->bindParam(':nombre', $nombre);
            $stmtVerificar->bindParam(':apat', $apat);
            $stmtVerificar->bindParam(':amat', $amat);
            $stmtVerificar->execute();
            $clienteExistente = $stmtVerificar->fetch(PDO::FETCH_ASSOC);

            if ($clienteExistente) {
                $idclientes = $clienteExistente['id_cliente'];
            } else {
                $stmtclientes = $conexion->prepare("INSERT INTO clientes (nombre_clientes, ap_clientes, am_clientes) VALUES (:nombre, :apat, :amat)");
                $stmtclientes->bindParam(':nombre', $nombre);
                $stmtclientes->bindParam(':apat', $apat);
                $stmtclientes->bindParam(':amat', $amat);
                $stmtclientes->execute();
                $idclientes = $conexion->lastInsertId();
            }

            // Crear el turno
            $stmtTurno = $conexion->prepare("INSERT INTO turno (id_rep, fecha_turno) VALUES (2, CURDATE())");
            $stmtTurno->execute();
            $idTurno = $conexion->lastInsertId();

            // Verificar el estado del turno antes de registrar ventas
            $estadoTurno = 3; // Solo queremos registrar si el estado es "atendido"

            if ($idclientes && $idTurno) {
                // Arreglo para almacenar los servicios seleccionados
                $serviciosSeleccionados = [];

                if (!empty($_POST['servicios'])) {
                    foreach ($_POST['servicios'] as $servicio) {
                        $stmtServicio = $conexion->prepare("SELECT id_servicio, costo FROM servicios WHERE nombre_serv = :servicio AND activo = 1");
                        $stmtServicio->bindParam(':servicio', $servicio);
                        $stmtServicio->execute();
                        $fila = $stmtServicio->fetch(PDO::FETCH_ASSOC);

                        if ($fila) {
                            $idServicio = $fila['id_servicio'];
                            $serviciosSeleccionados[] = $servicio; // Guardar el servicio en el arreglo
                            $totalCosto += $fila['costo']; // Sumar el costo del servicio

                            // Solo insertar en ventas si el estado del turno es 3 (atendido)
                            if ($estadoTurno === 3) {
                                $stmtVenta = $conexion->prepare("INSERT INTO ventas (id_cliente, id_servicio, id_usuario, id_turno) VALUES (:idCliente, :idServicio, 2, :idTurno)");
                                $stmtVenta->bindParam(':idCliente', $idclientes);
                                $stmtVenta->bindParam(':idServicio', $idServicio);
                                $stmtVenta->bindParam(':idTurno', $idTurno);
                                $stmtVenta->execute();
                            }
                        }
                    }
                }

                $conexion->commit();
                $mensaje = '<div class="bien">Registro exitoso</div>';
                echo "<script>alert('Total a pagar: $$totalCosto');</script>"; // Mostrar el alert
            } else {
                $mensaje = '<div class="alerta">Error al registrar el cliente</div>';
            }
        } catch (PDOException $e) {
            $conexion->rollBack();
            $mensaje = '<div class="alerta">Error: ' . $e->getMessage() . '</div>';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Cliente</title>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">
</head>
<body class="body-log">

    <h1>Registro de Cliente</h1>

    <form action="" method="POST" class="formulario">

        <!-- Mostrar el mensaje de éxito o error debajo del encabezado -->
        <?php if (!empty($mensaje)) echo $mensaje; ?>

        <!-- Formulario de registro del cliente y servicios -->
        <div class="form-group">
            <label for="nombre">Nombre completo:</label>
            <input type="text" id="nombre" name="nombre_clientes" placeholder="Ingresa el Nombre" required>
            <br><br>
            <label for="apat">Apellido Paterno:</label>
            <input type="text" id="apat" name="ap_clientes" placeholder="Ingresa el Apellido Paterno" required>
            <br><br>
            <label for="amat">Apellido Materno:</label>
            <input type="text" id="amat" name="am_clientes" placeholder="Ingresa el Apellido Materno" required>
            <br><br>
        </div>

        <div class="form-group">
            <label>Selecciona uno o más servicios:</label>
            <br>
            <?php foreach ($servicios as $servicio): ?>
                <div class="service-option">
                    <input type="checkbox" id="servicio_<?php echo $servicio['id_servicio']; ?>" name="servicios[]" value="<?php echo $servicio['nombre_serv']; ?>">
                    <label for="servicio_<?php echo $servicio['id_servicio']; ?>">
                        <span><?php echo $servicio['nombre_serv']; ?></span>
                        <br>
                        <span>$<?php echo number_format($servicio['costo'], 2); ?></span>
                    </label>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Botón de enviar -->
        <input class="boton" type="submit" value="Registrar" name="registro">
    </form>

    <a href="menu.php" class="link-menu">
        <i class="fa-solid fa-bars"></i>
    </a>

    <script>
        const serviceOptions = document.querySelectorAll('.service-option');
        serviceOptions.forEach(option => {
            const checkbox = option.querySelector('input[type="checkbox"]');
            checkbox.addEventListener('change', function() {
                option.classList.toggle('selected', checkbox.checked);
            });
        });
    </script>

</body>
</html>
