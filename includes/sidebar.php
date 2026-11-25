<?php
$paginaActual = $paginaActual ?? "";
$rol = $rol ?? "";

$rutaBase = (strpos($_SERVER["PHP_SELF"], "/usuarios/") !== false ||
             strpos($_SERVER["PHP_SELF"], "/clientes/") !== false ||
             strpos($_SERVER["PHP_SELF"], "/servicios/") !== false ||
             strpos($_SERVER["PHP_SELF"], "/empleados/") !== false ||
             strpos($_SERVER["PHP_SELF"], "/solicitudes/") !== false ||
             strpos($_SERVER["PHP_SELF"], "/reportes/") !== false)
             ? "../"
             : "";
?>

<aside class="sidebar">

    <div class="logo">
        Tech<span>Services</span>
    </div>

    <a href="<?php echo $rutaBase; ?>dashboard.php"
       class="<?php echo $paginaActual === 'dashboard' ? 'active' : ''; ?>">
        Panel principal
    </a>


    <?php if ($rol === "administrador"): ?>

        <!-- OPCIONES DEL ADMINISTRADOR -->

        <a href="<?php echo $rutaBase; ?>usuarios/index.php"
           class="<?php echo $paginaActual === 'usuarios' ? 'active' : ''; ?>">
            Usuarios
        </a>

        <a href="<?php echo $rutaBase; ?>empleados/index.php"
           class="<?php echo $paginaActual === 'empleados' ? 'active' : ''; ?>">
            Empleados
        </a>

        <a href="<?php echo $rutaBase; ?>clientes/index.php"
           class="<?php echo $paginaActual === 'clientes' ? 'active' : ''; ?>">
            Clientes
        </a>

        <a href="<?php echo $rutaBase; ?>servicios/index.php"
           class="<?php echo $paginaActual === 'servicios' ? 'active' : ''; ?>">
            Servicios
        </a>


        <a href="<?php echo $rutaBase; ?>solicitudes/listar.php" class="<?php echo $paginaActual === 'solicitudes' ? 'active' : ''; ?>">Solicitudes</a>
        <a href="<?php echo $rutaBase; ?>reportes/index.php" class="<?php echo $paginaActual === 'reportes' ? 'active' : ''; ?>">Reportes</a>

    <?php elseif ($rol === "empleado"): ?>

        <!-- OPCIONES DEL EMPLEADO -->

        <a href="<?php echo $rutaBase; ?>solicitudes/listar.php"
           class="<?php echo $paginaActual === 'mis_solicitudes_empleado' ? 'active' : ''; ?>">
            Mis solicitudes
        </a>


    <?php elseif ($rol === "cliente"): ?>

        <!-- OPCIONES DEL CLIENTE -->

        <a href="<?php echo $rutaBase; ?>solicitudes/crear.php"
           class="<?php echo $paginaActual === 'crear_solicitudes' ? 'active' : ''; ?>">
            Crear solicitud
        </a>

        <a href="<?php echo $rutaBase; ?>solicitudes/listar.php"
           class="<?php echo $paginaActual === 'mis_solicitudes' ? 'active' : ''; ?>">
            Mis solicitudes
        </a>

    <?php endif; ?>


    <!-- CERRAR SESIÓN -->
    <a href="<?php echo $rutaBase; ?>auth/logout.php">
        Cerrar sesión
    </a>

</aside>