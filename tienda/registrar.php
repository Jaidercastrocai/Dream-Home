<?php
include("conexregister.php");

if (isset($_POST['register'])){
    if (
        !empty($_POST['nombre']) &&
        !empty($_POST['apellidos']) &&
        !empty($_POST['documento']) &&
        !empty($_POST['telefono']) &&
        !empty($_POST['email']) &&
        !empty($_POST['direccion']) &&
        !empty($_POST['usuario']) &&
        !empty($_POST['contraseña'])
    ) {
        $nombre = trim($_POST['nombre']);
        $apellidos = trim($_POST['apellidos']);
        $documento = trim($_POST['documento']);
        $telefono = trim($_POST['telefono']);
        $email = trim($_POST['email']);
        $direccion = trim($_POST['direccion']);
        $usuario = trim($_POST['usuario']);
        $contraseña = trim($_POST['contraseña']);
        $fecha = date("d/m/Y");

        // Verificar que el correo no se repita
        $verificar_correo = mysqli_query($conex, "SELECT * FROM usuarios WHERE email='$email'");
        if (mysqli_num_rows($verificar_correo) > 0) {
            echo '
            <script>
                alert("Este correo ya está registrado, intenta con otro diferente");
                window.location = "CrearCuenta.php";
            </script>
            ';
            exit();
        }

        // Verificar que el usuario no se repita
        $verificar_usuario = mysqli_query($conex, "SELECT * FROM usuarios WHERE usuario='$usuario'");
        if (mysqli_num_rows($verificar_usuario) > 0) {
            echo '
            <script>
                alert("Este Usuario ya está registrado, crea otro diferente");
                window.location = "CrearCuenta.php";
            </script>
            ';
            exit();
        }

        $consulta = "INSERT INTO usuarios(nombre, apellidos, documento, telefono, email, direccion, usuario, contraseña, rol, fecha)
        VALUES ('$nombre', '$apellidos', '$documento', '$telefono', '$email', '$direccion', '$usuario', '$contraseña', 'usuario', '$fecha')";

        $resultado = mysqli_query($conex, $consulta);
        if ($resultado) {
            echo '
            <script>
                alert("Tu registro se ha completado exitosamente");
                window.location = "login.php";
            </script>
            ';
            exit();
        } else {
            echo '
            <script>
                alert("Error al registrarse");
            </script>
            ';
        }
    } else {
        echo '
        <script>
            alert("Llena todos los campos");
        </script>
        ';
    }
}
?>