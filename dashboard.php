<?php
// Conectar con la base de datos
require_once "config/conexion.php";

// Contar clientes registrados
$consultaClientes = $conexion->query(
    "SELECT COUNT(*) FROM clientes"
);

$totalClientes = $consultaClientes->fetchColumn();

// Contar servicios registrados
$consultaServicios = $conexion->query(
    "SELECT COUNT(*) FROM servicios"
);

$totalServicios = $consultaServicios->fetchColumn();

$consultaUsuarios = $conexion->query(
    "SELECT COUNT(*) FROM usuarios"

);
 $totalUsuarios = $consultaUsuarios->fetchColumn();

?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Panel Principal | Servicios Tecnológicos</title>

    <link rel="stylesheet" href="assets/css/dashboard.css">
</head>

<body>

    <!-- MENU LATERAL -->
    <?php
        $paginaActual = "dashboard";
        include "includes/sidebar.php";
    ?>

    <!-- CONTENIDO -->
    <main class="main-content">

        <header class="header">
            <div>
                <h1>Panel principal</h1>
                <p>Bienvenido al sistema de gestión tecnológica.</p>
            </div>

            <div>
                <strong>Administrador</strong>
            </div>
        </header>

        <!-- TARJETAS -->
        <section class="cards">

            <div class="card">
                <h3>Total de Usuario</h3>

                <div class="number">
                    <?php echo $totalUsuarios; ?>
                </div>

                <p>Usuarios registrados</p>
            </div>

            <div class="card">
                <h3>Total de clientes</h3>

                <div class="number">
                    <?php echo $totalClientes; ?>
                </div>

                <p>Clientes registrados</p>
            </div>

            <div class="card">
                <h3>Servicios disponibles</h3>

                <div class="number">
                    <?php echo $totalServicios; ?>
                </div>

                <p>Servicios registrados</p>
            </div>

            <div class="card">
                <h3>Gestión del sistema</h3>
                <div class="number">100%</div>
                <p>Control de clientes y servicios</p>
            </div>

        </section>

        <!-- BIENVENIDA -->
        <section class="section">

            <h2>Bienvenido a TechServices</h2>

            <p>
                Desde este panel podrás administrar los clientes
                y los servicios tecnológicos ofrecidos por la empresa.
            </p>

            <br>

            <a href="clientes/index.php" class="btn">
                Gestionar clientes
            </a>

            <a href="servicios/index.php" class="btn">
                Gestionar servicios
            </a>

            <a href="usuarios/index.php" class="btn">
                Gestionar usuarios
            </a>

        </section>

    </main>

</body>

</html>