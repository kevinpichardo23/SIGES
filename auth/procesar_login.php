<?php

session_start();

require_once("../config/conexion.php");

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $correo = trim($_POST["correo"] ?? "");
    $contrasena = $_POST["contrasena"] ?? "";

    $sql = "SELECT id, nombre, correo, contraseña, rol
            FROM usuarios
            WHERE correo = :correo
            LIMIT 1";

    $stmt = $conexion->prepare($sql);
    $stmt->execute(["correo" => $correo]);

    $usuario = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($usuario && password_verify($contrasena, $usuario["contraseña"])) {

        session_regenerate_id(true);

        $_SESSION["id_usuario"] = $usuario["id"];
        $_SESSION["nombre"] = $usuario["nombre"];
        $_SESSION["correo"] = $usuario["correo"];
        $_SESSION["rol"] = $usuario["rol"];

        header("Location: ../dashboard.php");
        exit();

    } else {

        header("Location: login.php?error=1");
        exit();

    }

} else {

    header("Location: login.php");
    exit();

}

?>