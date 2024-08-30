<?php
session_start();
include("conexregister.php");

$usuario = $_POST['usuario'];
$contraseña = $_POST['contraseña'];

// Validar si el usuario es administrador o usuario
$validar_login = mysqli_query($conex, "SELECT * FROM usuarios WHERE usuario='$usuario' AND contraseña='$contraseña'");

if (mysqli_num_rows($validar_login) > 0) {
    $usuario_data = mysqli_fetch_assoc($validar_login);
    $_SESSION['usuario'] = $usuario;
    $_SESSION['rol'] = $usuario_data['rol'];

    if ($usuario_data['rol'] === 'administrador') {
        header("location: /dreamhome/admin/admin.php");
    } else {
        header("location: /dreamhome/tienda/index.php");
    }
    exit;
} else {
    echo '
    <script>
        alert("Usuario no existe, por favor verifique los datos introducidos");
        window.location = "login.php";
    </script>
    ';
    exit;
}
?>