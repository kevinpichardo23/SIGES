<?php

$host = "localhost";
$basedatos = "siges";
$usuario = "root";
$contrasena = "";

try {

    $conexion = new PDO(
        "mysql:host=$host;dbname=$basedatos;charset=utf8mb4",
        $usuario,
        $contrasena
    );

    $conexion->setAttribute(
        PDO::ATTR_ERRMODE,
        PDO::ERRMODE_EXCEPTION
    );

} catch (PDOException $e) {

    die("Error de conexión: " . $e->getMessage());

}

?>