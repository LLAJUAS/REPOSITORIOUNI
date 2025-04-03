<?php
$conexion = conexionPHP();

if ($conexion) {
    echo "Conexión exitosa a la base de datos.";
} else {
    echo "Error en la conexión.";
}

function conexionPHP(){
    $server = "localhost";
    $user = "root";
    $pass = "62397902";
    $db = "unifranz_db";

    $conectar = mysqli_connect($server, $user, $pass, $db);

    if (!$conectar) {
        die("Error en la conexión: " . mysqli_connect_error());
    }

    return $conectar;
}
?>
