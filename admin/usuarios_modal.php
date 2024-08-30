<?php
// Supongamos que ya has realizado la conexión a la base de datos y otras configuraciones necesarias.

$search = isset($_POST['search']) ? $_POST['search'] : '';

// Consulta para obtener los usuarios registrados
$query = "SELECT * FROM usuarios WHERE nombre LIKE '%$search%' OR apellidos LIKE '%$search%' OR email LIKE '%$search%'";
$result = $conexion->query($query);
?>

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
                            <!-- Formulario para editar usuario -->
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