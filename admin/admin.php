<?php
// Conexión a la base de datos
$servername = "localhost"; 
$username = "root";
$password = "";
$dbname = "dreamhomedb";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Lógica para agregar un nuevo producto
if (isset($_POST['addProduct'])) {
    $nombre_P = $_POST['nombre_P'];
    $descripcion_P = $_POST['descripcion_P'];
    $categoria = $_POST['categoria'];
    $precio_P = $_POST['precio_P'];

    // Manejar la carga de la imagen
    $imagen_P = $_FILES['imagen_P']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($imagen_P);
    $uploadOk = 1;
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    // Verificar si el archivo es una imagen real
    $check = getimagesize($_FILES["imagen_P"]["tmp_name"]);
    if ($check === false) {
        echo "El archivo no es una imagen.";
        $uploadOk = 0;
    }

    // Verificar el tamaño del archivo
    if ($_FILES["imagen_P"]["size"] > 5000000) { // Limitar tamaño a 500KB
        echo "El archivo es demasiado grande.";
        $uploadOk = 0;
    }

    // Permitir ciertos formatos de archivo
    if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
        echo "Solo se permiten archivos JPG, JPEG, PNG y GIF.";
        $uploadOk = 0;
    }

    if ($uploadOk == 1) {
        if (move_uploaded_file($_FILES["imagen_P"]["tmp_name"], $target_file)) {
            // Preparar y ejecutar la consulta
            $stmt = $conn->prepare("INSERT INTO tienda (nombre_P, descripcion_P, categoria, precio_P, imagen_P) VALUES (?, ?, ?, ?, ?)");
            $stmt->bind_param("sssss", $nombre_P, $descripcion_P, $categoria, $precio_P, $imagen_P);
            if ($stmt->execute()) {
                echo "Nuevo producto agregado exitosamente";
            } else {
                echo "Error: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Lo siento, hubo un error al subir tu archivo.";
        }
    }
}

// Buscar usuarios
$search = '';
if (isset($_POST['search'])) {
    $search = $conn->real_escape_string($_POST['search']);
}

// Eliminar usuario
if (isset($_POST['delete'])) {
    $id = intval($_POST['id']);
    // Preparar y ejecutar la consulta de eliminación
    $stmt_log = $conn->prepare("DELETE FROM log_usuarios WHERE usuario_id = ?");
    $stmt_log->bind_param("i", $id);
    $stmt_log->execute();
    $stmt_log->close();

    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->close();
}

// Actualizar usuario
if (isset($_POST['update'])) {
    $id = intval($_POST['id']);
    $nombre = $_POST['nombre'];
    $apellidos = $_POST['apellidos'];
    $direccion = $_POST['direccion'];
    $documento = $_POST['documento'];
    $telefono = $_POST['telefono'];
    $usuario = $_POST['usuario'];
    $contraseña = $_POST['contraseña']; // Considera manejar contraseñas de forma más segura
    $email = $_POST['email'];
    $fecha = $_POST['fecha'];

    // Preparar y ejecutar la consulta de actualización
    $stmt = $conn->prepare("UPDATE usuarios SET nombre = ?, apellidos = ?, direccion = ?, documento = ?, telefono = ?, usuario = ?, contraseña = ?, email = ?, fecha = ? WHERE id = ?");
    $stmt->bind_param("sssssssssi", $nombre, $apellidos, $direccion, $documento, $telefono, $usuario, $contraseña, $email, $fecha, $id);
    $stmt->execute();
    $stmt->close();
}

// Obtener usuarios
$sql = "SELECT * FROM usuarios WHERE nombre LIKE ? OR apellidos LIKE ? OR email LIKE ?";
$search_param = "%$search%";
$stmt = $conn->prepare($sql);
$stmt->bind_param("sss", $search_param, $search_param, $search_param);
$stmt->execute();
$result = $stmt->get_result();

// Obtener estadísticas de ventas
$sql_ventas = "SELECT mueble, SUM(cantidad) AS total FROM muebles_vendidos GROUP BY mueble";
$result_ventas = $conn->query($sql_ventas);
$ventas_labels = [];
$ventas_data = [];
while ($row = $result_ventas->fetch_assoc()) {
    $ventas_labels[] = htmlspecialchars($row['mueble']);
    $ventas_data[] = intval($row['total']);
}

// Obtener estadísticas de usuarios por mes y año
$sql_usuarios_mes = "
    SELECT 
        DATE_FORMAT(fecha, '%Y-%m') AS mes, 
        COUNT(*) AS total 
    FROM 
        log_usuarios 
    GROUP BY 
        DATE_FORMAT(fecha, '%Y-%m')
    ORDER BY 
        mes;
";
$result_usuarios_mes = $conn->query($sql_usuarios_mes);
$usuarios_mes_labels = [];
$usuarios_mes_data = [];
while ($row = $result_usuarios_mes->fetch_assoc()) {
    $usuarios_mes_labels[] = htmlspecialchars($row['mes']);
    $usuarios_mes_data[] = intval($row['total']);
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrador - Venta de Muebles</title>
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        body {
            display: flex;
            font-family: Arial, sans-serif;
        }
        .sidebar {
            width: 250px;
            background-color: #343a40;
            padding: 15px;
            color: white;
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
        }
        .sidebar h2 {
            text-align: center;
            margin-bottom: 30px;
        }
        .sidebar button {
            width: 100%;
            margin-bottom: 10px;
            text-align: left;
        }
        .content {
            margin-left: 270px;
            padding: 20px;
            width: calc(100% - 270px);
        }
    </style>
</head>
<body>
    <!-- Panel lateral izquierdo -->
    <div class="sidebar">
        <h2>Administración</h2>
        <!-- Botón para agregar producto -->
        <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addProductModal">Agregar Producto</button>
        <!-- Botón para mostrar muebles vendidos -->
        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#ventasModal">Mostrar Muebles Vendidos</button>
        <!-- Botón para mostrar usuarios registrados -->
        <button type="button" class="btn btn-secondary" data-toggle="modal" data-target="#usuariosModal">Mostrar Usuarios Registrados</button>
    </div>

    <div class="content">
        <h1>Panel de Administración</h1>

        <!-- Modal para agregar producto -->
        <div class="modal fade" id="addProductModal" tabindex="-1" role="dialog" aria-labelledby="addProductModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="addProductModalLabel">Agregar Producto</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <form method="post" enctype="multipart/form-data">
                        <div class="modal-body">
                            <div class="form-group">
                                <label for="nombre_P">Nombre del Producto</label>
                                <input type="text" class="form-control" id="nombre_P" name="nombre_P" required>
                            </div>
                            <div class="form-group">
                                <label for="descripcion_P">Descripción</label>
                                <textarea class="form-control" id="descripcion_P" name="descripcion_P" required></textarea>
                            </div>
                            <div class="form-group">
                                <label for="categoria">Categoría</label>
                                <select class="form-control" id="categoria" name="categoria" required>
                                    <option value="Muebles de sala">Muebles de sala</option>
                                    <option value="Muebles de comedor">Muebles de comedor</option>
                                    <option value="Muebles de dormitorio">Muebles de dormitorio</option>
                                    <!-- Agrega más opciones según tus categorías -->
                                </select>
                            </div>
                            <div class="form-group">
                                <label for="precio_P">Precio</label>
                                <input type="number" class="form-control" id="precio_P" name="precio_P" required>
                            </div>
                            <div class="form-group">
                                <label for="imagen_P">Imagen del Producto</label>
                                <input type="file" class="form-control-file" id="imagen_P" name="imagen_P" required>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                            <button type="submit" class="btn btn-primary" name="addProduct">Guardar</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal para mostrar muebles vendidos -->
        <div class="modal fade" id="ventasModal" tabindex="-1" role="dialog" aria-labelledby="ventasModalLabel" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="ventasModalLabel">Muebles Vendidos</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <canvas id="ventasChart"></canvas>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal para mostrar usuarios registrados -->
        <div class="modal fade" id="usuariosModal" tabindex="-1" role="dialog" aria-labelledby="usuariosModalLabel" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="usuariosModalLabel">Usuarios Registrados</h5>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                    </div>
                    <div class="modal-body">
                        <form method="post" class="mb-3">
                            <input type="text" name="search" placeholder="Buscar por nombre, apellidos o email" class="form-control" value="<?php echo htmlspecialchars($search); ?>">
                        </form>
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>Nombre</th>
                                    <th>Apellidos</th>
                                    <th>Email</th>
                                    <th>Acciones</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()) { ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['nombre']); ?></td>
                                    <td><?php echo htmlspecialchars($row['apellidos']); ?></td>
                                    <td><?php echo htmlspecialchars($row['email']); ?></td>
                                    <td>
                                        <form method="post" style="display:inline;">
                                            <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                            <button type="submit" name="delete" class="btn btn-danger btn-sm">Eliminar</button>
                                        </form>
                                        <button type="button" class="btn btn-primary btn-sm" data-toggle="modal" data-target="#updateModal<?php echo $row['id']; ?>">Editar</button>
                                    </td>
                                </tr>

                                <!-- Modal para editar usuario -->
                                <div class="modal fade" id="updateModal<?php echo $row['id']; ?>" tabindex="-1" role="dialog" aria-labelledby="updateModalLabel<?php echo $row['id']; ?>" aria-hidden="true">
                                    <div class="modal-dialog" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="updateModalLabel<?php echo $row['id']; ?>">Editar Usuario</h5>
                                                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                                                    <span aria-hidden="true">&times;</span>
                                                </button>
                                            </div>
                                            <form method="post">
                                                <div class="modal-body">
                                                    <input type="hidden" name="id" value="<?php echo $row['id']; ?>">
                                                    <div class="form-group">
                                                        <label for="nombre">Nombre</label>
                                                        <input type="text" class="form-control" id="nombre" name="nombre" value="<?php echo htmlspecialchars($row['nombre']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="apellidos">Apellidos</label>
                                                        <input type="text" class="form-control" id="apellidos" name="apellidos" value="<?php echo htmlspecialchars($row['apellidos']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="direccion">Dirección</label>
                                                        <input type="text" class="form-control" id="direccion" name="direccion" value="<?php echo htmlspecialchars($row['direccion']); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="documento">Documento</label>
                                                        <input type="text" class="form-control" id="documento" name="documento" value="<?php echo htmlspecialchars($row['documento']); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="telefono">Teléfono</label>
                                                        <input type="text" class="form-control" id="telefono" name="telefono" value="<?php echo htmlspecialchars($row['telefono']); ?>">
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="usuario">Usuario</label>
                                                        <input type="text" class="form-control" id="usuario" name="usuario" value="<?php echo htmlspecialchars($row['usuario']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="contraseña">Contraseña</label>
                                                        <input type="password" class="form-control" id="contraseña" name="contraseña" value="<?php echo htmlspecialchars($row['contraseña']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="email">Email</label>
                                                        <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($row['email']); ?>" required>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="fecha">Fecha</label>
                                                        <input type="date" class="form-control" id="fecha" name="fecha" value="<?php echo htmlspecialchars($row['fecha']); ?>">
                                                    </div>
                                                </div>
                                                <div class="modal-footer">
                                                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                                                    <button type="submit" class="btn btn-primary" name="update">Guardar Cambios</button>
                                                </div>
                                            </form>
                                        </div>
                                    </div>
                                </div>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-dismiss="modal">Cerrar</button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
    <script>
        // Script para mostrar gráficos en los modales
        var ctxVentas = document.getElementById('ventasChart').getContext('2d');
        var ventasChart = new Chart(ctxVentas, {
            type: 'bar',
            data: {
                labels: <?php echo json_encode($ventas_labels); ?>,
                datasets: [{
                    label: 'Ventas por Mueble',
                    data: <?php echo json_encode($ventas_data); ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.2)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });
    </script>
</body>
</html>