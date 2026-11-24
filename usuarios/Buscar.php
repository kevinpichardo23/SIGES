<?php
require_once "../config/conexion.php";

header("Content-Type: application/json; charset=utf-8");

$busqueda = trim($_GET["buscar"] ?? "");

try {
    if ($busqueda !== "") {
        $sql = "SELECT id, nombre, correo, rol
                FROM usuarios
                WHERE nombre LIKE ? OR correo LIKE ?
                ORDER BY id DESC";

        $termino = "%$busqueda%";
        $consulta = $conexion->prepare($sql);
        $consulta->execute([$termino, $termino]);
    } else {
        $consulta = $conexion->query(
            "SELECT id, nombre, correo, rol
             FROM usuarios
             ORDER BY id DESC"
        );
    }

    echo json_encode(
        $consulta->fetchAll(PDO::FETCH_ASSOC),
        JSON_UNESCAPED_UNICODE
    );

} catch (PDOException $e) {
    http_response_code(500);
    echo json_encode(["error" => "No se pudieron consultar los usuarios."]);
}