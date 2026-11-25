<?php
session_start();

if (
    empty($_SESSION["id_usuario"]) ||
    ($_SESSION["rol"] ?? "") !== "administrador"
) {
    header("Location: ../auth/login.php");
    exit;
}

require_once "../config/conexion.php";

$mensaje = "";

function limpiar($dato) {
    return htmlspecialchars((string)$dato, ENT_QUOTES, "UTF-8");
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $accion = $_POST["accion"] ?? "";

    try {

        if ($accion === "guardar" || $accion === "actualizar") {

            $usuarioId = trim($_POST["usuario_id"] ?? "");
            $usuarioId = $usuarioId !== "" ? $usuarioId : null;

            if ($usuarioId !== null) {

                $idCliente = $accion === "actualizar"
                    ? (int)($_POST["id"] ?? 0)
                    : 0;

                $consultaUsuario = $conexion->prepare(
                    "SELECT COUNT(*)
                     FROM usuarios u
                     WHERE u.id = ?
                     AND u.rol = 'cliente'
                     AND NOT EXISTS (
                         SELECT 1
                         FROM clientes c
                         WHERE c.usuario_id = u.id
                         AND c.id <> ?
                     )"
                );

                $consultaUsuario->execute([
                    $usuarioId,
                    $idCliente
                ]);

                if (!$consultaUsuario->fetchColumn()) {
                    throw new Exception(
                        "La cuenta seleccionada no está disponible."
                    );
                }
            }
        }

        if ($accion === "guardar") {

            $sql = "INSERT INTO clientes
                    (usuario_id, nombre, apellido, identificacion, correo, telefono, direccion)
                    VALUES (?, ?, ?, ?, ?, ?, ?)";

            $consulta = $conexion->prepare($sql);

            $consulta->execute([
                $usuarioId,
                trim($_POST["nombre"]),
                trim($_POST["apellido"]),
                trim($_POST["identificacion"]) ?: null,
                trim($_POST["correo"]),
                trim($_POST["telefono"]),
                trim($_POST["direccion"])
            ]);

            $mensaje = "Cliente registrado correctamente.";
        }

        if ($accion === "actualizar") {

            $sql = "UPDATE clientes SET
                    usuario_id = ?,
                    nombre = ?,
                    apellido = ?,
                    identificacion = ?,
                    correo = ?,
                    telefono = ?,
                    direccion = ?
                    WHERE id = ?";

            $consulta = $conexion->prepare($sql);

            $consulta->execute([
                $usuarioId,
                trim($_POST["nombre"]),
                trim($_POST["apellido"]),
                trim($_POST["identificacion"]) ?: null,
                trim($_POST["correo"]),
                trim($_POST["telefono"]),
                trim($_POST["direccion"]),
                $_POST["id"]
            ]);

            $mensaje = "Cliente actualizado correctamente.";
        }

        // ELIMINAR CLIENTE
        if ($accion === "eliminar") {

            $consulta = $conexion->prepare(
                "DELETE FROM clientes WHERE id = ?"
            );

            $consulta->execute([$_POST["id"]]);

            $mensaje = "Cliente eliminado correctamente.";
        }

    } catch (PDOException $e) {

        $mensaje = "No se pudo completar la operación. Verifica los datos y que el cliente no tenga solicitudes asociadas.";

    } catch (Exception $e) {

        $mensaje = $e->getMessage();

    }
}

$clienteEditar = null;

if (isset($_GET["editar"])) {

    $consulta = $conexion->prepare(
        "SELECT * FROM clientes WHERE id = ?"
    );

    $consulta->execute([$_GET["editar"]]);

    $clienteEditar = $consulta->fetch(PDO::FETCH_ASSOC);
}

$idClienteActual = $clienteEditar["id"] ?? 0;

$consultaUsuarios = $conexion->prepare(
    "SELECT u.id, u.nombre, u.correo
     FROM usuarios u
     LEFT JOIN clientes c ON c.usuario_id = u.id
     WHERE u.rol = 'cliente'
     AND (c.id IS NULL OR c.id = ?)
     ORDER BY u.nombre"
);

$consultaUsuarios->execute([$idClienteActual]);

$usuariosCliente = $consultaUsuarios->fetchAll(PDO::FETCH_ASSOC);

// BUSCAR CLIENTES
$busqueda = trim($_GET["buscar"] ?? "");

if ($busqueda !== "") {

    $consulta = $conexion->prepare(
        "SELECT * FROM clientes
         WHERE nombre LIKE ?
         OR apellido LIKE ?
         OR correo LIKE ?
         ORDER BY id DESC"
    );

    $termino = "%$busqueda%";

    $consulta->execute([
        $termino,
        $termino,
        $termino
    ]);

} else {

    $consulta = $conexion->query(
        "SELECT * FROM clientes ORDER BY id DESC"
    );

}

$clientes = $consulta->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de Clientes | TechServices</title>

    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>

    <?php
        $paginaActual = "clientes";
        $rol="administrador";
        $rutaBase = "../";
        include "../includes/sidebar.php";
    ?>

    <main class="main-content">

        <header class="header">
            <div>
                <h1>Gestión de Clientes</h1>
                <p>Administración de los clientes de la empresa.</p>
            </div>
        </header>

        <?php if ($mensaje !== ""): ?>
            <div class="section">
                <strong><?php echo limpiar($mensaje); ?></strong>
            </div>
        <?php endif; ?>

        <!-- FORMULARIO DE CLIENTES -->

        <section class="section">

            <h2>
                <?php echo $clienteEditar ? "Editar cliente" : "Registrar nuevo cliente"; ?>
            </h2>

            <form method="POST" action="index.php">

                <input
                    type="hidden"
                    name="accion"
                    value="<?php echo $clienteEditar ? "actualizar" : "guardar"; ?>"
                >

                <?php if ($clienteEditar): ?>
                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo limpiar($clienteEditar["id"]); ?>"
                    >
                <?php endif; ?>

                <label>Nombre:</label>
                <input
                    type="text"
                    name="nombre"
                    required
                    value="<?php echo limpiar($clienteEditar["nombre"] ?? ""); ?>"
                    placeholder="Ingrese el nombre"
                >

                <label>Apellido:</label>
                <input
                    type="text"
                    name="apellido"
                    required
                    value="<?php echo limpiar($clienteEditar["apellido"] ?? ""); ?>"
                    placeholder="Ingrese el apellido"
                >

                <label>Identificación:</label>
                <input
                    type="text"
                    name="identificacion"
                    value="<?php echo limpiar($clienteEditar["identificacion"] ?? ""); ?>"
                    placeholder="Cédula o identificación"
                >

                <label>Correo electrónico:</label>
                <input
                    type="email"
                    name="correo"
                    required
                    value="<?php echo limpiar($clienteEditar["correo"] ?? ""); ?>"
                    placeholder="ejemplo@correo.com"
                >

                <label>Cuenta de usuario:</label>

                <select name="usuario_id">

                    <option value="">Sin cuenta asociada</option>

                    <?php foreach ($usuariosCliente as $usuario): ?>

                        <option
                            value="<?php echo limpiar($usuario["id"]); ?>"
                            <?php echo (string)($clienteEditar["usuario_id"] ?? "") === (string)$usuario["id"] ? "selected" : ""; ?>
                        >
                            <?php echo limpiar($usuario["nombre"] . " - " . $usuario["correo"]); ?>
                        </option>

                    <?php endforeach; ?>

                </select>

                <label>Teléfono:</label>
                <input
                    type="text"
                    name="telefono"
                    required
                    value="<?php echo limpiar($clienteEditar["telefono"] ?? ""); ?>"
                    placeholder="Ingrese el teléfono"
                >

                <label>Dirección:</label>
                <textarea
                    name="direccion"
                    rows="3"
                    required
                    placeholder="Ingrese la dirección"
                ><?php echo limpiar($clienteEditar["direccion"] ?? ""); ?></textarea>

                <button type="submit" class="btn">
                    <?php echo $clienteEditar ? "Guardar cambios" : "Registrar cliente"; ?>
                </button>

                <?php if ($clienteEditar): ?>
                    <a href="index.php" class="btn btn-danger">
                        Cancelar
                    </a>
                <?php endif; ?>

            </form>

        </section>

        <!-- BUSCADOR -->

        <section class="section">

            <h2>Clientes registrados</h2>

            <form method="GET" action="index.php">

                <label>Buscar por nombre, apellido o correo:</label>

                <input
                    type="text"
                    name="buscar"
                    value="<?php echo limpiar($busqueda); ?>"
                    placeholder="Escriba para buscar..."
                >

                <button type="submit" class="btn">
                    Buscar
                </button>

                <a href="index.php" class="btn">
                    Mostrar todos
                </a>

            </form>

            <br>

            <p>Total de resultados: <?php echo count($clientes); ?></p>

            <br>

            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Teléfono</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($clientes as $cliente): ?>

                        <tr>

                            <td><?php echo limpiar($cliente["id"]); ?></td>

                            <td>
                                <?php
                                echo limpiar(
                                    $cliente["nombre"] . " " . $cliente["apellido"]
                                );
                                ?>
                            </td>

                            <td><?php echo limpiar($cliente["correo"]); ?></td>

                            <td><?php echo limpiar($cliente["telefono"]); ?></td>

                            <td><?php echo limpiar($cliente["estado"]); ?></td>

                            <td>

                                <a
                                    href="index.php?editar=<?php echo limpiar($cliente["id"]); ?>"
                                    class="btn"
                                >
                                    Editar
                                </a>

                                <form
                                    method="POST"
                                    action="index.php"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar este cliente?');"
                                    style="display:inline;"
                                >

                                    <input
                                        type="hidden"
                                        name="accion"
                                        value="eliminar"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?php echo limpiar($cliente["id"]); ?>"
                                    >

                                    <button type="submit" class="btn btn-danger">
                                        Eliminar
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    <?php if (count($clientes) === 0): ?>

                        <tr>
                            <td colspan="6">
                                No se encontraron clientes registrados.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </section>

    </main>

</body>
</html>