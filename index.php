
<?php
session_start();

if (isset($_SESSION["id_usuario"])) {
    header("Location: dashboard.php");
} else {
    header("Location: auth/login.php");
}

exit();
?>
