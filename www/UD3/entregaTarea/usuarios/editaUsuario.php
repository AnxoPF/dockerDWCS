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
                <?php
                        require_once('../utils.php');
                        $id = $_POST['id'];
                        $nombre = $_POST['nombre'];
                        $apellidos = $_POST['apellidos'];
                        $username = $_POST['username'];
                        $contrasena = $_POST['contrasena'];
                        $error = false;

                        if (!esTextoValido($nombre)) {
                            $error = true;
                            echo '<div class="alert alert-danger" role="alert">El campo nombre es obligatorio y debe contener al menos 3 caracteres.</div>';
                        }
                        if (!esTextoValido($apellidos)) {
                            $error = true;
                            echo '<div class="alert alert-danger" role="alert">El campo apellidos es obligatorio y debe contener al menos 3 caracteres.</div>';
                        }
                        if (!esTextoValido($username)) {
                            $error = true;
                            echo '<div class="alert alert-danger" role="alert">El campo username es obligatorio y debe contener al menos 3 caracteres.</div>';
                        }
                        if (!empty($contrasena) && !validaContrasena($contrasena)) {
                            $error = true;
                            echo '<div class="alert alert-danger" role="alert">El campo contraseña es obligatorio y debe ser compleja.</div>';
                        }
                        if (!$error) {
                            require_once('../bbdd/pdo.php');
                            if (empty($contrasena)) $contrasena = null;
                            $resultado = actualizarUsuario($id, filtrarContenido($nombre), filtrarContenido($apellidos), filtrarContenido($username), $contrasena);
                            if ($resultado[0]) {
                                echo '<div class="alert alert-success" role="alert">Usuario guardado correctamente.</div>';
                            } else {
                                echo '<div class="alert alert-danger" role="alert">No se ha podido guardar el usuario: ' . $resultado[1] . '</div>';
                            }
                        }
                    ?>
                </div>
            </main>
        </div>
    </div>

    <?php include_once('../vista/footer.php'); ?>

</body>
</html>