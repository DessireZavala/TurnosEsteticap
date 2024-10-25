<?php

// Define la constante __ROOT__ si no está definida
if (!defined('__ROOT__')) {
    define('__ROOT__', dirname(dirname(__FILE__)));  // Define la raíz del proyecto
}

// Incluye el archivo de configuración de la base de datos
require_once(__ROOT__ . "/config/db.php");
$dbc = null;

// Función para establecer la conexión con la base de datos
function conectar(){
    try {
        $dbc = new PDO(
            'mysql:host=' . DBHOST .
            ';port=' . DBPORT .
            ';dbname=' . DBNAME,
            DBUSUARIOS,
            DBPASSWORD,
            array(PDO::ATTR_PERSISTENT => false)
        );

        // Opcional: Log para verificar la conexión
        echo "<script>console.log('Conexion establecida')</script>";
        return $dbc;
    } catch (PDOException $e) {
        // Opcional: Log de error
        echo "<script>console.log('Error de conexión: " . $e->getMessage() . "')</script>";
        return null;
    }
}

// Función para cerrar la conexión
function desconectar($dbc){
    $dbc = null;
    return $dbc;
}

?>
