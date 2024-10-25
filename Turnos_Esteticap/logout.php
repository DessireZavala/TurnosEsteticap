<?php
session_start(); // Iniciar la sesión

// Destruir todas las variables de la sesión
session_unset();

// Destruir la sesión
session_destroy();

// Limpiar la caché para evitar que se pueda regresar usando el navegador
// Esto es para evitar que la página anterior quede en caché
header("Cache-Control: no-store, no-cache, must-revalidate, max-age=0");
header("Cache-Control: post-check=0, pre-check=0", false);
header("Pragma: no-cache");


// Redirigir al usuario a la página de login
header('Location: index.php');
exit;
?>
