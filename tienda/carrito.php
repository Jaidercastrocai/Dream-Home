<?php
session_start();

$mensaje="";

if(isset($_POST['btnAccion'])){
    switch($_POST['btnAccion']){  
    case 'Agregar':

        if(is_numeric(openssl_decrypt($_POST['id_Producto'], COD, KEY))){
            $ID = openssl_decrypt($_POST['id_Producto'], COD, KEY);
            $mensaje .= "OK ID correcto: " . $ID . "<br/>";
        } else {
            $mensaje .= "Ups ID incorrecto<br/>";
            break;
        }

        if(is_string(openssl_decrypt($_POST['nombre_P'], COD, KEY))){
            $NOMBRE = openssl_decrypt($_POST['nombre_P'], COD, KEY);
            $mensaje .= "OK NOMBRE: " . $NOMBRE . "<br/>";
        } else {
            $mensaje .= "Ups, algo pasa con el nombre<br/>";
            break;
        }

        if(is_numeric(openssl_decrypt($_POST['cantidad_P'], COD, KEY))){
            $CANTIDAD = openssl_decrypt($_POST['cantidad_P'], COD, KEY);
            $mensaje .= "OK CANTIDAD: " . $CANTIDAD . "<br/>";
        } else {
            $mensaje .= "Ups, algo pasa con la cantidad<br/>";
            break;
        }

        if(is_numeric(openssl_decrypt($_POST['precio_P'], COD, KEY))){
            $PRECIO = openssl_decrypt($_POST['precio_P'], COD, KEY);
            $mensaje .= "OK PRECIO: " . $PRECIO . "<br/>";
        } else {
            $mensaje .= "Ups, algo pasa con el precio<br/>";
            break;
        }

        $producto = array(
            'ID' => $ID,
            'NOMBRE' => $NOMBRE,
            'CANTIDAD' => $CANTIDAD,
            'PRECIO' => $PRECIO
        );

        if(isset($_SESSION['CARRITO'])){
            $idProductos = array_column($_SESSION['CARRITO'], "ID");
            if(in_array($ID, $idProductos)){
                echo "<script>alert('El producto ya ha sido seleccionado...')</script>";
                $mensaje= "";
            } else {
                $NumeroProductos = count($_SESSION['CARRITO']);
                $_SESSION['CARRITO'][$NumeroProductos] = $producto;
                $mensaje = "";
            }
        } else {
            $_SESSION['CARRITO'][0] = $producto;
            $mensaje = "Producto agregado al carrito";
        }

        break;

    case "Eliminar":
        if(is_numeric(openssl_decrypt($_POST['id_Producto'], COD, KEY))){
            $ID = openssl_decrypt($_POST['id_Producto'], COD, KEY);
            
            foreach($_SESSION['CARRITO'] as $indice => $producto){
                if($producto['ID'] == $ID){
                    unset($_SESSION['CARRITO'][$indice]);
                    echo "<script>alert('Elemento borrado...');</script>";
                }
            }
        } else {
            $mensaje .= "Ups ID incorrecto<br/>";
        }
        break;
    }
}
?>