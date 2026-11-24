<?php

// Selección provisional de roles
$rol = "administrador";

// Opciones: administrador, empleado y cliente

require_once "config/conexion.php";

$consultaClientes = $conexion->query(
    "SELECT COUNT(*) FROM clientes"
);

$totalClientes = $consultaClientes->fetchColumn();


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

    <?php
        $paginaActual = "dashboard";
        include "includes/sidebar.php";
    ?>

    <main class="main-content">

        <header class="header">
            <div>
                <h1>Panel principal</h1>
                <p>Bienvenido al sistema de gestión tecnológica.</p>
            </div>

            <div>
                <strong>
                    <?php echo ucfirst($rol); ?>
                </strong>
            </div>
        </header>


        <?php if ($rol === "administrador"): ?>

            <!-- DASHBOARD DEL ADMINISTRADOR -->

            <section class="cards">

                <div class="card">
                    <h3>Total de usuarios</h3>

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

            <!-- BIENVENIDA DEL ADMINISTRADOR -->
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

                <a href="empleados/index.php" class="btn">
                    Gestionar empleados
                </a>

            </section>


        <?php elseif ($rol === "empleado"): ?>

            <!-- DASHBOARD DEL EMPLEADO -->

            <section class="section">

                <h2>Bienvenido a TechServices</h2>

                <p>
                    Desde este panel podrás consultar y gestionar
                    las solicitudes de servicio que te sean asignadas.
                </p>

                <br>

                <a href="solicitudes/listar.php" class="btn">
                    Mis solicitudes
                </a>

            </section>


        <?php elseif ($rol === "cliente"): ?>

            <!-- DASHBOARD DEL CLIENTE -->

            <section class="section">

                <h2>Bienvenido a TechServices</h2>

                <p>
                    Desde este panel podrás solicitar servicios tecnológicos
                    y consultar el estado de tus solicitudes.
                </p>

                <br>

                <a href="solicitudes/crear.php" class="btn">
                    Crear solicitud
                </a>

                <a href="solicitudes/listar.php" class="btn">
                    Consultar mis solicitudes
                </a>

            </section>


        <?php else: ?>

            <!-- Sin rol -->

            <section class="section">
                <h2>error</h2>
                <p>No se pudo identificar el tipo de usuario.</p>
            </section>

        <?php endif; ?>

    </main>

</body>

</html>