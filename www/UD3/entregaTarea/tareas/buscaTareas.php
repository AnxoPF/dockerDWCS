<?php include_once ('../vista/head.php'); ?>

<body>
    <?php include_once('../vista/header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <?php include_once('../vista/menu.php'); ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Buscar tareas</h2>
                </div>
                
                <div class="container">
                    <form action="tareas.php" method="GET" class="mb-5">
                        <div class="mb-3">
                            <label for="id_usuario" class="form-label">Usuario</label>
                            <select class="form-select" id="id_usuario" name="id_usuario" required>
                                <option value="" selected disabled>Seleccione el usuario</option>
                                <?php
                                    require_once('../bbdd/pdo.php');
                                    $usuarios = listaUsuarios()[1];
                                    foreach ($usuarios as $usuario) { ?>
                                        <option value="<?php echo ($usuario['id']); ?>">
                                            <?php echo $usuario['username']; ?>
                                        </option>
                                <?php } ?>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select class="form-select" id="estado" name="estado" >
                                <option value="" selected disabled>Seleccione el estado</option>
                                <option value="en_proceso">En Proceso</option>
                                <option value="pendiente">Pendiente</option>
                                <option value="completada">Completada</option>
                            </select>
                        </div>
                        <button type="submit" class="btn btn-primary">Buscar</button>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <?php include_once('../vista/footer.php'); ?>
</body>
</html>
