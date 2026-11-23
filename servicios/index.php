<?php
require_once "../config/conexion.php";

$mensaje = "";

// REGISTRAR O ELIMINAR SERVICIOS
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $accion = $_POST["accion"] ?? "";

    try {

        if ($accion === "guardar") {

            $nombre = trim($_POST["nombre"]);
            $descripcion = trim($_POST["descripcion"]);
            $precio = $_POST["precio"];

            $sql = "INSERT INTO servicios (nombre, descripcion, precio)
                    VALUES (?, ?, ?)";

            $consulta = $conexion->prepare($sql);

            $consulta->execute([
                $nombre,
                $descripcion,
                $precio
            ]);

            $mensaje = "Servicio registrado correctamente.";
        }

        if ($accion === "eliminar") {

            $id = $_POST["id"];

            $consulta = $conexion->prepare(
                "DELETE FROM servicios WHERE id = ?"
            );

            $consulta->execute([$id]);

            $mensaje = "Servicio eliminado correctamente.";
        }

    } catch (PDOException $e) {

        $mensaje = "No se pudo completar la operación. Verifica si el servicio está asociado a alguna solicitud.";
    }
}

// BUSCAR SERVICIOS
$busqueda = trim($_GET["buscar"] ?? "");

if ($busqueda !== "") {

    $consulta = $conexion->prepare(
        "SELECT * FROM servicios
         WHERE nombre LIKE ?
         OR descripcion LIKE ?
         ORDER BY id DESC"
    );

    $termino = "%$busqueda%";

    $consulta->execute([$termino, $termino]);

} else {

    $consulta = $conexion->query(
        "SELECT * FROM servicios ORDER BY id DESC"
    );
}

$servicios = $consulta->fetchAll(PDO::FETCH_ASSOC);

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Gestión de Servicios | TechServices</title>

    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>

    <!-- MENU LATERAL -->
    <aside class="sidebar">

        <div class="logo">
            Tech<span>Services</span>
        </div>

        <a href="../index.php">Panel principal</a>

        <a href="../clientes/index.php">Clientes</a>

        <a href="index.php" class="active">Servicios</a>

    </aside>

    <!-- CONTENIDO PRINCIPAL -->
    <main class="main-content">

        <header class="header">
            <div>
                <h1>Gestión de Servicios</h1>
                <p>Administración de los servicios ofrecidos por la empresa.</p>
            </div>
        </header>

        <?php if ($mensaje !== ""): ?>
            <section class="section">
                <strong><?php echo htmlspecialchars($mensaje, ENT_QUOTES, "UTF-8"); ?></strong>
            </section>
        <?php endif; ?>

        <!-- REGISTRAR SERVICIOS -->
        <section class="section">

            <h2>Registrar nuevo servicio</h2>

            <form method="POST" action="index.php">

                <input type="hidden" name="accion" value="guardar">

                <label>Nombre del servicio:</label>
                <input
                    type="text"
                    name="nombre"
                    required
                    placeholder="Ingrese el nombre del servicio"
                >

                <label>Descripción:</label>
                <textarea
                    name="descripcion"
                    rows="3"
                    required
                    placeholder="Describa el servicio"
                ></textarea>

                <label>Precio:</label>
                <input
                    type="number"
                    name="precio"
                    step="0.01"
                    min="0"
                    required
                    placeholder="Ingrese el precio"
                >

                <button type="submit" class="btn">
                    Registrar servicio
                </button>

            </form>

        </section>

        <!-- LISTADO DE SERVICIOS -->
        <section class="section">

            <h2>Servicios registrados</h2>

            <form method="GET" action="index.php">

                <label>Buscar por nombre o descripción:</label>

                <input
                    type="text"
                    name="buscar"
                    value="<?php echo htmlspecialchars($busqueda, ENT_QUOTES, "UTF-8"); ?>"
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

            <p>Total de servicios: <?php echo count($servicios); ?></p>

            <br>

            <table>

                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Acciones</th>
                    </tr>
                </thead>

                <tbody>

                    <?php foreach ($servicios as $servicio): ?>

                        <tr>

                            <td>
                                <?php echo htmlspecialchars((string)$servicio["id"], ENT_QUOTES, "UTF-8"); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($servicio["nombre"], ENT_QUOTES, "UTF-8"); ?>
                            </td>

                            <td>
                                <?php echo htmlspecialchars($servicio["descripcion"], ENT_QUOTES, "UTF-8"); ?>
                            </td>

                            <td>
                                RD$ <?php echo number_format((float)$servicio["precio"], 2); ?>
                            </td>

                            <td>

                                <form
                                    method="POST"
                                    action="index.php"
                                    onsubmit="return confirm('¿Seguro que deseas eliminar este servicio?');"
                                >

                                    <input
                                        type="hidden"
                                        name="accion"
                                        value="eliminar"
                                    >

                                    <input
                                        type="hidden"
                                        name="id"
                                        value="<?php echo htmlspecialchars((string)$servicio["id"], ENT_QUOTES, "UTF-8"); ?>"
                                    >

                                    <button type="submit" class="btn btn-danger">
                                        Eliminar
                                    </button>

                                </form>

                            </td>

                        </tr>

                    <?php endforeach; ?>

                    <?php if (count($servicios) === 0): ?>

                        <tr>
                            <td colspan="5">
                                No se encontraron servicios registrados.
                            </td>
                        </tr>

                    <?php endif; ?>

                </tbody>

            </table>

        </section>

    </main>

</body>
</html>