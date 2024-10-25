<?php
session_start();

// Incluir la conexión a la base de datos
include("services/dbcon.php");
$conexion = conectar();

// Verificar si el usuario está autenticado y es el id_usuario = 1
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_usuario'] != 1) {
    header("Location: http://localhost/Turnos_Estetica/login.php");
    exit();
}

// Mensaje de error
$mensaje_error = '';

// Verificar si se está agregando un nuevo servicio
if (isset($_POST['add_service'])) {
    $nombre_serv = $_POST['nombre_serv'];
    $costo = $_POST['costo'];

    if (!empty($nombre_serv) && !empty($costo)) {
        $stmt = $conexion->prepare("INSERT INTO servicios (nombre_serv, costo, activo) VALUES (:nombre_serv, :costo, 1)");
        $stmt->bindParam(':nombre_serv', $nombre_serv);
        $stmt->bindParam(':costo', $costo);
        $stmt->execute();
    }
}

// Verificar si se está editando un servicio
if (isset($_POST['edit_service'])) {
    $id_servicio = $_POST['id_servicio'];
    $nombre_serv = $_POST['nombre_serv'];
    $costo = $_POST['costo'];
    $activo = isset($_POST['activo']) ? 1 : 0; // Obtener estado activo

    if (!empty($nombre_serv) && !empty($costo)) {
        $stmt = $conexion->prepare("UPDATE servicios SET nombre_serv = :nombre_serv, costo = :costo, activo = :activo WHERE id_servicio = :id_servicio");
        $stmt->bindParam(':nombre_serv', $nombre_serv);
        $stmt->bindParam(':costo', $costo);
        $stmt->bindParam(':activo', $activo);
        $stmt->bindParam(':id_servicio', $id_servicio);
        $stmt->execute();
    }
}

// Verificar si se está eliminando un servicio
if (isset($_GET['delete'])) {
    $id_servicio = $_GET['delete'];

    // Comprobar si hay registros asociados al servicio
    $stmtVerificar = $conexion->prepare("SELECT COUNT(*) FROM ventas WHERE id_servicio = :id_servicio");
    $stmtVerificar->bindParam(':id_servicio', $id_servicio);
    $stmtVerificar->execute();
    $registros = $stmtVerificar->fetchColumn();

    if ($registros > 0) {
        $mensaje_error = '<div style="color: red;">No se puede borrar el servicio porque tiene registros asociados.</div>';
    } else {
        // Eliminar el servicio de forma permanente
        $stmt = $conexion->prepare("DELETE FROM servicios WHERE id_servicio = :id_servicio");
        $stmt->bindParam(':id_servicio', $id_servicio);
        $stmt->execute();
    }
}

// Obtener todos los servicios, incluyendo los inactivos
$stmt = $conexion->prepare("SELECT * FROM servicios");
$stmt->execute();
$servicios = $stmt->fetchAll();

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>CRUD Servicios</title>
    <link rel="stylesheet" type="text/css" href="styles/styles.css">
</head>
<body>

    <h1>CRUD Servicios</h1>

    <!-- Mostrar mensaje de error -->
    <?php if (!empty($mensaje_error)) echo $mensaje_error; ?>

    <!-- Formulario para Crear o Editar Servicio -->
    <form action="" method="POST">
        <input type="hidden" name="id_servicio" id="id_servicio"> <!-- Campo oculto para el ID del servicio -->
        <label for="nombre_serv">Nombre del Servicio:</label>
        <input type="text" name="nombre_serv" id="nombre_serv" required>
        <br><br>
        <label for="costo">Costo:</label>
        <input type="number" name="costo" id="costo" step="0.01" required>
        <br><br>
        <label>
            <input type="checkbox" name="activo" id="activo" value="1">
            Activo
        </label>
        <br><br>
        <!-- Botón de agregar o editar -->
        <button type="submit" name="add_service">Agregar Servicio</button>
        <button type="submit" name="edit_service" style="display: none;">Actualizar Servicio</button>
    </form>

    <!-- Tabla de Servicios -->
    <h2>Servicios Registrados</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Nombre</th>
                <th>Costo</th>
                <th>Estado</th>
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($servicios as $servicio): ?>
                <tr>
                    <td><?php echo htmlspecialchars($servicio['nombre_serv']); ?></td>
                    <td>$<?php echo number_format($servicio['costo'], 2); ?></td>
                    <td><?php echo $servicio['activo'] ? 'Activo' : 'Inactivo'; ?></td>
                    <td>
                        <button onclick="editService(<?php echo $servicio['id_servicio']; ?>, '<?php echo addslashes($servicio['nombre_serv']); ?>', <?php echo $servicio['costo']; ?>, <?php echo $servicio['activo']; ?>)">Editar</button>
                        <a href="?delete=<?php echo $servicio['id_servicio']; ?>" onclick="return confirm('¿Estás seguro de eliminar este servicio?')">Eliminar</a>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <a href="menu.php" class="link-menu">
                <i class="fa-solid fa-bars"></i>
            </a>
    <script>
        function editService(id_servicio, nombre_serv, costo, activo) {
            // Rellenar el formulario con los datos del servicio
            document.getElementById('id_servicio').value = id_servicio;
            document.getElementById('nombre_serv').value = nombre_serv;
            document.getElementById('costo').value = costo;

            // Marcar el checkbox si está activo
            document.getElementById('activo').checked = activo;

            // Cambiar el botón de agregar a editar
            document.querySelector('button[name="add_service"]').style.display = 'none';
            document.querySelector('button[name="edit_service"]').style.display = 'inline';
        }
    </script>

</body>
</html>
