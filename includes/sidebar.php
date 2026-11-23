<?php
$paginaActual = $paginaActual ?? "";

// Determinar si estamos en una carpeta interna
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

    <a href="<?php echo $rutaBase; ?>usuarios/index.php"
       class="<?php echo $paginaActual === 'usuarios' ? 'active' : ''; ?>">
        Usuarios
    </a>

    <a href="<?php echo $rutaBase; ?>clientes/index.php"
       class="<?php echo $paginaActual === 'clientes' ? 'active' : ''; ?>">
        Clientes
    </a>

    <a href="<?php echo $rutaBase; ?>servicios/index.php"
       class="<?php echo $paginaActual === 'servicios' ? 'active' : ''; ?>">
        Servicios
    </a>

</aside>