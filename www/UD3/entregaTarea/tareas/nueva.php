<?php include_once ('../vista/head.php'); ?>

<body>
    <?php include_once('../vista/header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <?php include_once('../vista/menu.php'); ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Gestión de tarea</h2>
                </div>

                <div class="container">
                    <?php
                    require_once('../utils.php');
                    $titulo = $_POST['titulo'];
                    $descripcion = $_POST['descripcion'];
                    $estado = $_POST['estado'];
                    $id_usuario = $_POST['id_usuario'];
                    $error = false;

                    if (!esTextoValido($titulo)) {
                        $error = true;
                        echo '<div class="alert alert-danger" role="alert">El campo titulo es obligatorio y debe contener al menos 3 caracteres.</div>';
                    }
                    if (!esTextoValido($descripcion)) {
                        $error = true;
                        echo '<div class="alert alert-danger" role="alert">El campo descripción es obligatorio y debe contener al menos 3 caracteres.</div>';
                    }
                    if (!esTextoValido($estado)) {
                        $error = true;
                        echo '<div class="alert alert-danger" role="alert">El campo estado es obligatorio.</div>';
                    }
                    if (!esNumeroValido($id_usuario)) {
                        $error = true;
                        echo '<div class="alert alert-danger" role="alert">El campo usuario es obligatorio.</div>';
                    }
                    if (!$error) {
                        require_once('../bbdd/mysqli.php');
                        $resultado = nuevaTarea(filtrarContenido($titulo), filtrarContenido($descripcion), filtrarContenido($estado), filtrarContenido($id_usuario));
                        if ($resultado[0]) {
                            echo '<div class="alert alert-success" role="alert">Tarea guardada correctamente.</div>';
                        } else {
                            echo '<div class="alert alert-danger" role="alert">Error guardando la tarea: ' . $resultado[1] . '</div>';
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
