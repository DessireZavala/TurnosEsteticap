<?php 
session_start(); // Iniciar la sesión

// Verificar si el usuario está autenticado y si tiene el usuario adecuado
if (!isset($_SESSION['id_usuario']) || $_SESSION['id_usuario'] !== 2) {
	$mensaje = '<div class="alerta">No tienes permiso para acceder a esta página. Serás redirigido al login.</div>';
    // Si no está autenticado o no es el usuario adecuado, redirigir al login
    header("Location: http://localhost/Turnos_Estetica/login.php");
    exit(); // Detener la ejecución del script
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Index</title>
</head>
<body class="body-log">
<div class="logo-circulo">
		<img src="assets/eplogoblanco.png" alt="Logo">
	</div>

    <div class="contenedor-principal">
    <h1>Menú de opciones</h1>

    <p>Bienvenido: <?php echo isset($_SESSION['usuario']) ? htmlspecialchars($_SESSION['usuario']) : 'Usuario desconocido'; ?></p> <!-- Mensaje de bienvenida -->

    <div>
    <a href="registroCli.php">Registro de Cliente y Servicios</a><br>
    
    <div>
    <a href="siguientesTurnos.php">Visualizador de Turnos</a><br>
    </div>

    <div>
    <a href="atender.php">Atender</a><br>
    </div>

    <div>
    <a href="logout.php">Cerrar Sesión</a><br>
    </div>
    </div>


    <script src="js/funcionesGenerales.js"></script>

	<script>
		agregarEvento(window, 'load', iniciarReset, false);

		function iniciarReset() {

			var resetear = document.getElementById('reset');
			agregarEvento(resetear, 'click', function(e) {

				if (e) {

					e.preventDefault();

					id = e.target.id;

				}

				var datos = "registrar=reset-turnos";

				funcion = procesarReseteo;
				fichero = "consultas/registrar.php";

				conectarViaPost(funcion, fichero, datos);

			}, false);

			function procesarReseteo() {

				if (conexion.readyState == 4) {

					var data = JSON.parse(conexion.responseText);

					if (data.status == "correcto") {

						alert(data.mensaje);

					} else {

						console.log(data.mensaje);

					}

				} else {

					console.log('cargando');
				}

			}

		}
	</script>


</body>
</html>
