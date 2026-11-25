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

            $nombre = trim($_POST["nombre"] ?? "");
            $correo = trim($_POST["correo"] ?? "");
            $contrasena = $_POST["contrasena"] ?? "";
            $rol = $_POST["rol"] ?? "";

            if (!in_array($rol, ["administrador", "empleado", "cliente"], true)) {
                throw new Exception("El rol seleccionado no es válido.");
            }

            if (strlen($contrasena) < 8) {
                throw new Exception("La contraseña debe tener al menos 8 caracteres.");
            }

            $hash = password_hash($contrasena, PASSWORD_DEFAULT);

            $sql = "INSERT INTO usuarios (nombre, correo, contraseña, rol)
                    VALUES (?, ?, ?, ?)";

            $consulta = $conexion->prepare($sql);

            $consulta->execute([
                $nombre,
                $correo,
                $hash,
                $rol
            ]);

            $mensaje = "Usuario registrado correctamente.";
        }

        if ($accion === "actualizar") {

            $id = $_POST["id"] ?? "";
            $nombre = trim($_POST["nombre"] ?? "");
            $correo = trim($_POST["correo"] ?? "");
            $rol = $_POST["rol"] ?? "";
            $contrasena = $_POST["contrasena"] ?? "";

            if (!in_array($rol, ["administrador", "empleado", "cliente"], true)) {
                throw new Exception("El rol seleccionado no es válido.");
            }

            if ($contrasena !== "") {

                if (strlen($contrasena) < 8) {
                    throw new Exception("La contraseña debe tener al menos 8 caracteres.");
                }

                $hash = password_hash($contrasena, PASSWORD_DEFAULT);

                $sql = "UPDATE usuarios SET
                        nombre = ?,
                        correo = ?,
                        contraseña = ?,
                        rol = ?
                        WHERE id = ?";

                $consulta = $conexion->prepare($sql);

                $consulta->execute([
                    $nombre,
                    $correo,
                    $hash,
                    $rol,
                    $id
                ]);

            } else {

                $sql = "UPDATE usuarios SET
                        nombre = ?,
                        correo = ?,
                        rol = ?
                        WHERE id = ?";

                $consulta = $conexion->prepare($sql);

                $consulta->execute([
                    $nombre,
                    $correo,
                    $rol,
                    $id
                ]);
            }

            $mensaje = "Usuario actualizado correctamente.";
        }

        if ($accion === "eliminar") {

            $id = $_POST["id"] ?? "";

            if (isset($_SESSION["id_usuario"]) && $id == $_SESSION["id_usuario"]) {
                throw new Exception("No puedes eliminar tu propia cuenta.");
            }

            $consulta = $conexion->prepare(
                "DELETE FROM usuarios WHERE id = ?"
            );

            $consulta->execute([$id]);

            $mensaje = "Usuario eliminado correctamente.";
        }

    } catch (PDOException $e) {

        if ($e->getCode() === "23000") {
            $mensaje = "No se pudo registrar el usuario. Verifica si el correo ya está registrado o si tiene registros relacionados.";
        } else {
            $mensaje = "No se pudo completar la operación.";
        }

    } catch (Exception $e) {

        $mensaje = $e->getMessage();

    }
}

$usuarioEditar = null;

if (isset($_GET["editar"])) {

    $consulta = $conexion->prepare(
        "SELECT id, nombre, correo, rol
         FROM usuarios
         WHERE id = ?"
    );

    $consulta->execute([$_GET["editar"]]);

    $usuarioEditar = $consulta->fetch(PDO::FETCH_ASSOC);
}

$busqueda = "";

$consulta = $conexion->query(
    "SELECT id, nombre, correo, rol
     FROM usuarios
     ORDER BY id DESC"
);

$usuarios = $consulta->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de Usuarios | TechServices</title>

    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>

    <?php
        $paginaActual = "usuarios";
        $rol="administrador";
        include "../includes/sidebar.php";
    ?>

    <main class="main-content">

        <header class="header">
            <div>
                <h1>Gestión de Usuarios</h1>
                <p>Administración de las cuentas y roles de acceso.</p>
            </div>
        </header>

        <?php if ($mensaje !== ""): ?>

            <div class="section">
                <strong><?php echo limpiar($mensaje); ?></strong>
            </div>

        <?php endif; ?>

        <section class="section">

            <h2>
                <?php echo $usuarioEditar ? "Editar usuario" : "Registrar nuevo usuario"; ?>
            </h2>

            <form method="POST" action="index.php" id="usuarioForm" novalidate>

                <input
                    type="hidden"
                    name="accion"
                    value="<?php echo $usuarioEditar ? "actualizar" : "guardar"; ?>"
                >

                <?php if ($usuarioEditar): ?>

                    <input
                        type="hidden"
                        name="id"
                        value="<?php echo limpiar($usuarioEditar["id"]); ?>"
                    >

                <?php endif; ?>

                   <label>Nombre:</label>

                    <input
                        type="text"
                        id="nombre"
                        name="nombre"
                        required
                        value="<?php echo limpiar($usuarioEditar["nombre"] ?? ""); ?>"
                        placeholder="Ingrese el nombre"
                    >

                    <label>Correo electrónico:</label>

                    <input
                        type="email"
                        id="correo"
                        name="correo"
                        required
                        value="<?php echo limpiar($usuarioEditar["correo"] ?? ""); ?>"
                        placeholder="ejemplo@correo.com"
                    >

                    <label>Contraseña:</label>

                    <input
                        type="password"
                        id="contrasena"
                        name="contrasena"
                        <?php echo $usuarioEditar ? "" : "required"; ?>
                        minlength="8"
                        placeholder="<?php echo $usuarioEditar ? "Dejar vacío para conservar la actual" : "Mínimo 8 caracteres"; ?>"
                    >

                   <label>Rol:</label>

                <select name="rol" id="rol" required>

                    <option value="">Seleccione un rol</option>

                    <option
                        value="administrador"
                        <?php echo ($usuarioEditar["rol"] ?? "") === "administrador" ? "selected" : ""; ?>
                    >
                        Administrador
                    </option>

                    <option
                        value="empleado"
                        <?php echo ($usuarioEditar["rol"] ?? "") === "empleado" ? "selected" : ""; ?>
                    >
                        Empleado
                    </option>

                    <option
                        value="cliente"
                        <?php echo ($usuarioEditar["rol"] ?? "") === "cliente" ? "selected" : ""; ?>
                    >
                        Cliente
                   </option>

                </select>

                <button type="submit" class="btn">
                    <?php echo $usuarioEditar ? "Guardar cambios" : "Registrar usuario"; ?>
                </button>

                <?php if ($usuarioEditar): ?>

                    <a href="index.php" class="btn btn-danger">
                        Cancelar
                    </a>

                <?php endif; ?>

            </form>

        </section>

        <section class="section">

            <h2>Usuarios registrados</h2>

            <div>
                <label for="buscar">Buscar por nombre o correo:</label>

                <input
                type="text"
                id="buscar"
                placeholder="Escriba para buscar..."
                >

                <label for="filtroRol">Filtrar por rol:</label>

                <select id="filtroRol">
                    <option value="">Todos los roles</option>
                    <option value="administrador">Administrador</option>
                    <option value="empleado">Empleado</option>
                    <option value="cliente">Cliente</option>
                </select>
            </div>

            <p>Total de resultados: <span id="totalResultados"><?php echo count($usuarios); ?></span></p>

            <br>

            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Correo</th>
                        <th>Rol</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody id="tablaUsuarios">

                    <?php foreach ($usuarios as $usuario): ?>

                        <tr data-rol="<?php echo limpiar($usuario["rol"]); ?>">

                            <td><?php echo limpiar($usuario["id"]); ?></td>
                            <td><?php echo limpiar($usuario["nombre"]); ?></td>
                            <td><?php echo limpiar($usuario["correo"]); ?></td>
                            <td><?php echo limpiar(ucfirst($usuario["rol"])); ?></td>

                            <td>
                                <a
                                    href="index.php?editar=<?php echo limpiar($usuario["id"]); ?>"
                                    class="btn"
                                >
                                    Editar
                                </a>

                                <form
                                    method="POST"
                                    action="index.php"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar este usuario?');"
                                    style="display:inline;"
                                >
                                    <input type="hidden" name="accion" value="eliminar">

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?php echo limpiar($usuario["id"]); ?>"
                                    >

                                    <button type="submit" class="btn btn-danger">
                                        Eliminar
                                    </button>
                                </form>
                          </td>

                         </tr>

                    <?php endforeach; ?>

                    <?php if (count($usuarios) === 0): ?>
                    <tr>
                        <td colspan="5">No se encontraron usuarios registrados.</td>
                    </tr>
                    <?php endif; ?>

                </tbody>

            </table>

        </section>

    </main>
    <script src="../assets/js/usuarios.js"></script>
</body>
</html>