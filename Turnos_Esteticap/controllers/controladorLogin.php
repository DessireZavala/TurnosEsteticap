<?php
session_start(); // Iniciar la sesión

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Obtener valores del formulario
    $usuario = $_POST['usuario'];
    $password = $_POST['password'];

    if (empty($usuario) || empty($password)) {
        echo '<div class="alerta">Por favor, ingresa todos los campos.</div>';
    } else {
        // Conexión a la base de datos
        include_once '../services/dbcon.php'; // Ruta ajustada para incluir la conexión
        $conexion = conectar();

        // Consulta para verificar el usuario y la contraseña
        $stmt = $conexion->prepare("SELECT id_usuario, password, id_rol, usuario FROM usuarios WHERE usuario = :usuario LIMIT 1");
        $stmt->bindParam(':usuario', $usuario);
        $stmt->execute();
        $usuarioEncontrado = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($usuarioEncontrado) {
            // Verificar la contraseña
            if (hash('sha256', $password) === $usuarioEncontrado['password']) {
                // Iniciar sesión
                $_SESSION['id_usuario'] = $usuarioEncontrado['id_usuario'];
                $_SESSION['id_rol'] = $usuarioEncontrado['id_rol']; // Guardar el rol en la sesión
                $_SESSION['usuario'] = $usuarioEncontrado['usuario']; // Guardar el nombre en la sesión

                // Redirigir según el rol del usuario
                if ($_SESSION['id_rol'] == 1) {
                    header('Location: ../serviciosCrud.php'); // Redirigir al CRUD de servicios para superusuarios
                } elseif ($_SESSION['id_rol'] == 2) {
                    header('Location: ../menu.php'); // Redirigir al menú de páginas o index
                } else {
                    // Si no es un usuario esperado, redirigir al login o página de error
                    header('Location: ../login.php');
                }
                exit; // Asegurarse de que el código se detenga aquí
            } else {
                echo '<div class="alerta">Contraseña incorrecta.</div>';
            }
        } else {
            echo '<div class="alerta">El usuario no existe.</div>';
        }
    }
}
?>
