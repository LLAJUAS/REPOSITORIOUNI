<?php
function obtenerConexion() {
    static $conexion = null; 
    if ($conexion === null) {
        $host = '127.0.0.1'; 
        $port = 3306;
        $database = 'unifranz_db';
        $username = 'root';
        $password = '62397902'; /*(ponen su conexión en el caso que tengan)
*/
        $conexion = new mysqli($host, $username, $password, $database, $port);
        if ($conexion->connect_error) {
            die("Error de conexión: " . $conexion->connect_error);
        }
        $conexion->set_charset("utf8"); 
    }
    return $conexion;
}
?>
