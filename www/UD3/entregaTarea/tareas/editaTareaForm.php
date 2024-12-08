<?php include_once ('../vista/head.php'); ?>

<body>
    <?php include_once('../vista/header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <?php include_once('../vista/menu.php'); ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Actualizar tarea</h2>
                </div>
                
                <div class="container">
                    <form action="editaTarea.php" method="POST" class="mb-5">
                    <?php
                    require_once('../bbdd/mysqli.php');
                    if (!empty($_GET)) {
                        $id = $_GET['id'];
                        $tarea = buscaTarea($id);
                        if (!empty($id) && $tarea) {
                            $titulo = $tarea['titulo'];
                            $descripcion = $tarea['descripcion'];
                            $estado = $tarea['estado'];
                            $id_usuario = $tarea['id_usuario'];
                    ?>
                        <input type="hidden" name="id" value="<?php echo $id ?>">
                        <?php include_once('formTarea.php'); ?>
                        <button type="submit" class="btn btn-primary">Actualizar</button>   
                        
                    <?php
                        } else {
                            echo '<div class="alert alert-danger" role="alert">No se pudo recuperar la información de la tarea.</div>';
                        }
                    } else {
                        echo '<div class="alert alert-danger" role="alert">Debes acceder a través del listado de tareas.</div>';
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
