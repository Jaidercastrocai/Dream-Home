<?php
session_start();
include 'cabecera.php';

include 'global/config.php';
include 'global/conextienda.php';
include 'carrito.php';
include 'templetes/cabecera.php';

// Lógica de búsqueda
$buscar = '';
$categoria = '';
if (isset($_POST['buscar'])) {
    $buscar = $_POST['buscar'];
}
if (isset($_POST['categoria'])) {
    $categoria = $_POST['categoria'];
}

$query = "SELECT * FROM tienda WHERE 1";
if ($buscar != '') {
    $query .= " AND nombre_P LIKE '%$buscar%'";
}
if ($categoria != '') {
    $query .= " AND categoria = '$categoria'";
}

$sentencia = $pdo->prepare($query);
$sentencia->execute();
$listaProductos = $sentencia->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Venta de Muebles</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
</head>
<body>

    <div class="container">
        <br>
        <!-- Formulario de búsqueda -->
        <form method="post" action="">
            <div class="form-row">
                <div class="col">
                    <input type="text" name="buscar" class="form-control" placeholder="Buscar producto" value="<?php echo $buscar; ?>">
                </div>
                <div class="col">
                    <select name="categoria" class="form-control">
                        <option value="">Todas las categorías</option>
                        <option value="Muebles de sala" <?php if($categoria == 'Muebles de sala') echo 'selected'; ?>>Muebles de sala</option>
                        <option value="Muebles de comedor" <?php if($categoria == 'Muebles de comedor') echo 'selected'; ?>>Muebles de comedor</option>
                        <option value="Muebles de oficina" <?php if($categoria == 'Muebles de oficina') echo 'selected'; ?>>Muebles de oficina</option>
                        <!-- Añadir más categorías según sea necesario -->
                    </select>
                </div>
                <div class="col">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </div>
        </form>
        <br>
        
        <?php if($mensaje!="") {?>
        <div class="alert alert-success">
            <?php echo $mensaje; ?>
            <a href="mostrarCarrito.php" class="badge badge-success">Ver carrito</a>
        </div>
        <?php }?>

        <div class="row">
            <?php foreach($listaProductos as $producto){?>
                <div class="col-3">
                    <div class="card">
                        <img title="<?php echo $producto['nombre_P'];?>"
                            alt="<?php echo $producto['nombre_P'];?>"
                            class="card-img-top"
                            src="<?php echo $producto['imagen_P'];?>"
                            data-toggle="popover"
                            data-trigger="hover"
                            data-content="<?php echo $producto['descripcion_P'];?>"
                            height="317px">
                        <div class="card-body">
                            <span><?php echo $producto['nombre_P'];?></span>
                            <h5 class="card-title">$<?php echo $producto['precio_P'];?></h5>
                            <p class="card-text">Descripción</p>
                            <form action="" method="post" onsubmit="return verificarSesion()">
                                <input type="hidden" name="id_Producto" id="id_Producto" value="<?php echo openssl_encrypt($producto['id_Producto'],COD,KEY);?>">
                                <input type="hidden" name="nombre_P" id="nombre_P" value="<?php echo openssl_encrypt($producto['nombre_P'],COD,KEY);?>">
                                <input type="hidden" name="precio_P" id="precio_P" value="<?php echo openssl_encrypt($producto['precio_P'],COD,KEY);?>">
                                <input type="hidden" name="cantidad_P" id="cantidad_P" value="<?php echo openssl_encrypt(1,COD,KEY);?>">
                                <button class="btn btn-primary" name="btnAccion" value="Agregar" type="submit">
                                    Agregar al carrito
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            <?php } ?>
        </div>
    </div>
    
    <!-- Banner en la parte inferior -->
    <div class="container mt-5">
        <div class="row">
            <div class="col-12">
                <img src="/mnt/data/image.png" alt="Banner" class="img-fluid">
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        function verificarSesion() {
            <?php if (!isset($_SESSION['usuario'])) { ?>
                if (confirm('Debes iniciar sesión. ¿Deseas crear una cuenta o iniciar sesión ahora?')) {
                    window.location.href = 'CrearCuenta.php';
                }
                return false; // Evita que el formulario se envíe
            <?php } ?>
            return true; // Permite que el formulario se envíe
        }

        $(function () {
            $('[data-toggle="popover"]').popover()
        });
    </script>
</body>
</html>

<?php
include 'templetes/pie.php';
?>
