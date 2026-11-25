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

        if ($accion === "guardar") {

            $usuario_id = $_POST["usuario_id"] ?? "";
            $especialidad = trim($_POST["especialidad"] ?? "");
            $estado = $_POST["estado"] ?? "Disponible";

            if ($especialidad === "") {
                throw new Exception("La especialidad es obligatoria.");
            }

            if (!in_array($estado, ["Disponible", "Ocupado", "Inactivo"], true)) {
                throw new Exception("El estado seleccionado no es válido.");
            }

            $consulta = $conexion->prepare(
                "SELECT id FROM usuarios
                 WHERE id = ? AND rol = 'empleado'"
            );

            $consulta->execute([$usuario_id]);

            if (!$consulta->fetch()) {
                throw new Exception("La cuenta seleccionada no es válida.");
            }

            $consulta = $conexion->prepare(
                "SELECT id FROM empleados WHERE usuario_id = ?"
            );

            $consulta->execute([$usuario_id]);

            if ($consulta->fetch()) {
                throw new Exception("Esta cuenta ya tiene un registro de empleado.");
            }

            $sql = "INSERT INTO empleados (usuario_id, especialidad, estado)
                    VALUES (?, ?, ?)";

            $consulta = $conexion->prepare($sql);

            $consulta->execute([
                $usuario_id,
                $especialidad,
                $estado
            ]);

            $mensaje = "Empleado registrado correctamente.";
        }

        if ($accion === "actualizar") {

            $id = $_POST["id"] ?? "";
            $especialidad = trim($_POST["especialidad"] ?? "");
            $estado = $_POST["estado"] ?? "";

            if ($especialidad === "") {
                throw new Exception("La especialidad es obligatoria.");
            }

            if (!in_array($estado, ["Disponible", "Ocupado", "Inactivo"], true)) {
                throw new Exception("El estado seleccionado no es válido.");
            }

            $sql = "UPDATE empleados SET
                    especialidad = ?,
                    estado = ?
                    WHERE id = ?";

            $consulta = $conexion->prepare($sql);

            $consulta->execute([
                $especialidad,
                $estado,
                $id
            ]);

            $mensaje = "Empleado actualizado correctamente.";
        }

    } catch (PDOException $e) {

        $mensaje = "No se pudo completar la operación.";

    } catch (Exception $e) {

        $mensaje = $e->getMessage();

    }
}

$empleadoEditar = null;

if (isset($_GET["editar"])) {

    $consulta = $conexion->prepare(
        "SELECT empleados.id,
                empleados.usuario_id,
                empleados.especialidad,
                empleados.estado,
                usuarios.nombre,
                usuarios.correo
         FROM empleados
         INNER JOIN usuarios
         ON empleados.usuario_id = usuarios.id
         WHERE empleados.id = ?"
    );

    $consulta->execute([$_GET["editar"]]);

    $empleadoEditar = $consulta->fetch(PDO::FETCH_ASSOC);
}

$consulta = $conexion->query(
    "SELECT usuarios.id, usuarios.nombre, usuarios.correo
     FROM usuarios
     LEFT JOIN empleados
     ON usuarios.id = empleados.usuario_id
     WHERE usuarios.rol = 'empleado'
     AND empleados.id IS NULL
     ORDER BY usuarios.nombre ASC"
);

$usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

$consulta = $conexion->query(
    "SELECT empleados.id,
            empleados.usuario_id,
            empleados.especialidad,
            empleados.estado,
            usuarios.nombre,
            usuarios.correo
     FROM empleados
     INNER JOIN usuarios
     ON empleados.usuario_id = usuarios.id
     ORDER BY empleados.id DESC"
);

$empleados = $consulta->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de Empleados | TechServices</title>

    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>

    <?php
        $paginaActual = "empleados";
        $rol="administrador";
        $rutaBase = "../";
        include "../includes/sidebar.php";
    ?>

    <main class="main-content">

        <header class="header">
            <div>
                <h1>Gestión de Empleados</h1>
                <p>Administración de los empleados y sus especialidades.</p>
            </div>
        </header>

        <?php if ($mensaje !== ""): ?>

            <div class="section">
                <strong><?php echo limpiar($mensaje); ?></strong>
            </div>

        <?php endif; ?>

        <section class="section">

            <h2>
                <?php echo $empleadoEditar ? "Editar empleado" : "Registrar nuevo empleado"; ?>
            </h2>

            <form method="POST" action="index.php">

                <input
                    type="hidden"
                    name="accion"
                    value="<?php echo $empleadoEditar ? "actualizar" : "guardar"; ?>"
                >

                <?php if ($empleadoEditar): ?>

                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo limpiar($empleadoEditar["id"]); ?>"
                    >

                    <label>Cuenta de usuario:</label>

                    <input
                        type="text"
                        value="<?php echo limpiar($empleadoEditar["nombre"]); ?> (<?php echo limpiar($empleadoEditar["correo"]); ?>)"
                        disabled
                    >

                <?php else: ?>

                    <label>Cuenta de usuario:</label>

                    <select name="usuario_id" required>

                        <option value="">Seleccione una cuenta</option>

                        <?php foreach ($usuarios as $usuario): ?>

                            <option value="<?php echo limpiar($usuario["id"]); ?>">
                                <?php echo limpiar($usuario["nombre"]); ?>
                                (<?php echo limpiar($usuario["correo"]); ?>)
                            </option>

                        <?php endforeach; ?>

                    </select>

                <?php endif; ?>

                <label>Especialidad:</label>

                <input
                    type="text"
                    name="especialidad"
                    required
                    value="<?php echo limpiar($empleadoEditar["especialidad"] ?? ""); ?>"
                    placeholder="Ej. Desarrollo web, Redes, Soporte técnico"
                >

                <label>Estado:</label>

                <select name="estado" required>

                    <option
                        value="Disponible"
                        <?php echo ($empleadoEditar["estado"] ?? "Disponible") === "Disponible" ? "selected" : ""; ?>
                    >
                        Disponible
                    </option>

                    <option
                        value="Ocupado"
                        <?php echo ($empleadoEditar["estado"] ?? "") === "Ocupado" ? "selected" : ""; ?>
                    >
                        Ocupado
                    </option>

                    <option
                        value="Inactivo"
                        <?php echo ($empleadoEditar["estado"] ?? "") === "Inactivo" ? "selected" : ""; ?>
                    >
                        Inactivo
                    </option>

                </select>

                <button type="submit" class="btn">
                    <?php echo $empleadoEditar ? "Guardar cambios" : "Registrar empleado"; ?>
                </button>

                <?php if ($empleadoEditar): ?>

                    <a href="index.php" class="btn btn-danger">
                        Cancelar
                    </a>

                <?php endif; ?>

            </form>

        </section>

        <section class="section">

            <h2>Empleados registrados</h2>

            <p>Total de empleados: <?php echo count($empleados); ?></p>

            <br>

            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Especialidad</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($empleados as $empleado): ?>

                        <tr>

                            <td><?php echo limpiar($empleado["id"]); ?></td>

                            <td><?php echo limpiar($empleado["nombre"]); ?></td>

                            <td><?php echo limpiar($empleado["correo"]); ?></td>

                            <td><?php echo limpiar($empleado["especialidad"]); ?></td>

                            <td><?php echo limpiar($empleado["estado"]); ?></td>

                            <td>

                                <a
                                    href="index.php?editar=<?php echo limpiar($empleado["id"]); ?>"
                                    class="btn"
                                >
                                    Editar
                                </a>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    <?php if (count($empleados) === 0): ?>

                        <tr>
                            <td colspan="6">
                                No hay empleados registrados.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </section>

    </main>

</body>
</html>