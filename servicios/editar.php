<?php
require_once "../config/conexion.php";

// OBTENER EL ID DEL SERVICIO
if (!isset($_GET["id"]) || !is_numeric($_GET["id"])) {
    header("Location: index.php");
    exit();
}

$id = (int) $_GET["id"];

// BUSCAR EL SERVICIO
$consulta = $conexion->prepare(
    "SELECT * FROM servicios WHERE id = ?"
);

$consulta->execute([$id]);

$servicio = $consulta->fetch(PDO::FETCH_ASSOC);

if (!$servicio) {
    die("El servicio no existe.");
}

// ACTUALIZAR SERVICIO
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $nombre = trim($_POST["nombre"]);
    $descripcion = trim($_POST["descripcion"]);
    $precio = $_POST["precio"];

    $sql = "UPDATE servicios SET
            nombre = ?,
            descripcion = ?,
            precio = ?
            WHERE id = ?";

    $actualizar = $conexion->prepare($sql);

    $actualizar->execute([
        $nombre,
        $descripcion,
        $precio,
        $id
    ]);

    header("Location: index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar servicio | TechServices</title>

    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>

<body>

    <aside class="sidebar">

        <div class="logo">
            Tech<span>Services</span>
        </div>

        <a href="../dashboard.php">Panel principal</a>

        <a href="../clientes/index.php">Clientes</a>

        <a href="index.php" class="active">Servicios</a>

    </aside>

    <main class="main-content">

        <header class="header">
            <div>
                <h1>Editar servicio</h1>
                <p>Modifica la información del servicio registrado.</p>
            </div>
        </header>

        <section class="section">

            <form method="POST">

                <label>Nombre del servicio:</label>
                <input
                    type="text"
                    name="nombre"
                    value="<?php echo htmlspecialchars($servicio["nombre"], ENT_QUOTES, "UTF-8"); ?>"
                    required
                >

                <label>Descripción:</label>
                <textarea
                    name="descripcion"
                    rows="3"
                    required
                ><?php echo htmlspecialchars($servicio["descripcion"], ENT_QUOTES, "UTF-8"); ?></textarea>

                <label>Precio:</label>
                <input
                    type="number"
                    name="precio"
                    step="0.01"
                    min="0"
                    value="<?php echo htmlspecialchars((string)$servicio["precio"], ENT_QUOTES, "UTF-8"); ?>"
                    required
                >

                <button type="submit" class="btn">
                    Guardar cambios
                </button>

                <a href="index.php" class="btn btn-danger">
                    Cancelar
                </a>

            </form>

        </section>

    </main>

</body>
</html>