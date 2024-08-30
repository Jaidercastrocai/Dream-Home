<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
    <link rel="stylesheet" href="CrearCuenta.css">
    
</head>
<body>
  
<form method="post" >

<h2 style=" color: white">Hola</h2>
<p>Inicia tu registro</p>

<div class="input-wrapper">
<input type="text" name="nombre" placeholder="Nombre" >
</div>

<div class="input-wrapper">
<input type="text" name="apellidos" placeholder="Apellidos" > 
</div>

<div class="input-wrapper">
<input type="text" name="documento" placeholder="Documento" >
</div>

<div class="input-wrapper">
<input type="tel" name="telefono" placeholder="Telefono">
</div>

<div class="input-wrapper">
<input type="email" name="email" placeholder="Correo Electronico">
</div>

<div class="input-wrapper">
<input type="text" name="direccion" placeholder="Dirección">
</div>

<div class="input-wrapper">
<input type="text" name="usuario" placeholder="Usuario">
</div>

<div class="input-wrapper">
<input type="password" name="contraseña" placeholder="Contraseña" >
</div>


<input class="btn" type="submit" name="register" value="Enviar">
<a href="login.php">Iniciar sesión</a>
</form>

<?php
    include("registrar.php");
?>
     
    
    
     
    
     
</body>
</html>