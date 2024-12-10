<?php include_once ('../vista/head.php'); ?>

<body>

    <?php include_once ('../vista/header.php'); ?>

    <div class="container-fluid">
        <div class="row">

            <?php include_once ('../vista/menu.php'); ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Actualizar usuario</h2>
                </div>

                <div class="container justify-content-between">
                    <form action="editaUsuario.php" method="POST" class="mb 5 w-50">
                        <?php
                            require_once('../bbdd/pdo.php');
                            if (!empty($_GET)) {
                                $id = $_GET['id'];
                                $usuario = buscarUsuario($id);
                                if (!empty($id) && $usuario) {
                                    $nombre = $usuario['nombre'];
                                    $apellidos = $usuario['apellidos'];
                                    $username = $usuario['username'];
                        ?>
                            <input type="hidden" name="id" value="<?php echo $id ?>">
                            <?php include_once('formUsuario.php'); ?>
                            <div class="mb-3">
                                <label for="contrasena" class="form-label">Contraseña</label>
                                <input type="password" class="form-control" id="contrasena" name="contrasena" >
                            </div>
                            <button type="submit" class="btn btn-primary">Actualizar</button>
                        <?php
                                } else {
                                echo '<div class="alert alert-danger" role="alert">No se pudo recuperar la información del usuario</div>';
                            }
                        } else {
                            echo '<div class="alert alert-danger" role="alert">No se pudo recuperar la información del usuario</div>';
                        }
                        ?>
                    </form>
                </div>
            </main>
        </div>
    </div>

    <?php include_once('../vista/footer.php'); ?>

</body>
</html>