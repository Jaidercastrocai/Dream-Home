<?php
session_start();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Inicio de sesión</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <form action="iniciarSesion.php" method="POST">
      <h1>INICIAR SESION</h1>
      <hr>
      <i class="fa-solid fa-user"></i>
      <label>Usuario</label>
      <input type="text" name="usuario" placeholder="Usuario" required>
      <i class="fa-solid fa-unlock"></i>
      <label>Contraseña</label>
      <input type="password" name="contraseña" placeholder="Contraseña" required>
      <hr>
      <button type="submit" name="enviar">Iniciar sesión</button>
      <a href="CrearCuenta.php">Crear Cuenta</a>
      <a href="#">¿Olvidaste tu contraseña?</a>
    </form>
    <style>
      body {
        background-image: url(img/spacejoy-RqO6kwm4tZY-unsplash.jpg);
        background-size: cover;
        background-position: center;
        width: 300px;
        justify-content: center;
        align-items: center;
      }
      form {
        margin: auto;
        margin-left: 360px;
      }
    </style>
</body>
</html>
