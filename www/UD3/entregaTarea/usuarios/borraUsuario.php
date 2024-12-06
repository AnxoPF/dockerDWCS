<?php include_once ('../vista/head.php'); ?>

<body>

    <?php include_once ('../vista/header.php'); ?>

    <div class="container-fluid">
        <div class="row">

            <?php include_once ('../vista/menu.php'); ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Borrar usuario</h2>
                </div>

                <div class="container">
                    <?php
                        require_once('../bbdd/pdo.php');
                        if (!empty($_GET)) {
                            $id = $_GET['id'];
                            if (!empty($id)) {
                                $resultado = borrarUsuario($id);
                                if ($resultado[0]) {
                                    echo '<div class="alert alert-success" role="alert">Usuario borrado correctamente.</div>';
                                } else {
                                    echo '<div class="alert alert-danger" role="alert">No se pudo borrar el usuario.</div>';
                                }
                            } else {
                                echo '<div class="alert alert-danger" role="alert">No se pudo recuperar la información del usuario.</div>';
                            }
                        } else {
                            echo '<div class="alert alert-danger" role="alert">Debes acceder a través del listado de usuarios.</div>';
                        }
                    ?>
                </div>
            </main>
        </div>
    </div>

    <?php include_once('../vista/footer.php'); ?>

</body>
</html>