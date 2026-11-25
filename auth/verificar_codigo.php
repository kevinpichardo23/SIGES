<?php
session_start();

$recuperacion = $_SESSION["recuperacion"] ?? null;

if (!$recuperacion) {
    header("Location: recuperar.php");
    exit;
}

$error = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {

    $codigo = trim($_POST["codigo"] ?? "");

    if (time() > $recuperacion["expira"]) {

        unset($_SESSION["recuperacion"]);
        $error = "El código ha expirado. Solicita uno nuevo.";

    } elseif ($codigo === $recuperacion["codigo"]) {

        $_SESSION["codigo_verificado"] = true;

        header("Location: nueva_contrasena.php");
        exit;

    } else {

        $error = "El código introducido es incorrecto.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verificar código - SIGES</title>
    <link rel="stylesheet" href="../assets/css/login.css">
</head>
<body>

    <div class="form-container">

        <h3 class="titulo">Verificar código</h3>

        <p>Introduce el código de seis dígitos generado por SIGES.</p>

        <p>
            Código de demostración:
            <strong><?= htmlspecialchars($recuperacion["codigo"]) ?></strong>
        </p>

        <?php if ($error): ?>
            <p role="alert" style="color: #b91c1c;">
                <?= htmlspecialchars($error) ?>
            </p>
        <?php endif; ?>

        <form method="POST">

            <div class="campo">
                <label class="label" for="codigo">
                    Código de verificación
                </label>

                <input
                    type="text"
                    name="codigo"
                    id="codigo"
                    class="input"
                    maxlength="6"
                    pattern="[0-9]{6}"
                    inputmode="numeric"
                    required
                >
            </div>

            <button type="submit" class="btn btn-primary">
                Verificar código
            </button>

        </form>

    </div>

</body>
</html>