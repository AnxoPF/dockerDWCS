<?php include_once ('../vista/head.php'); ?>

<body>

    <?php include_once ('../vista/header.php'); ?>

    <div class="container-fluid">
        <div class="row">

            <?php include_once ('../vista/menu.php'); ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Usuarios</h2>
                </div>

                <div class="container justify-content-between">
                    <?php
                        require_once('../bbdd/pdo.php');
                        $resultado = listaUsuarios();
                        if ($resultado[0]){
                    ?>
                        <div class="table">
                            <table class="table table-sm table-striped table-hover">
                                <thead class="thead">
                                    <tr>
                                        <th>ID</th>
                                        <th>Nombre</th> 
                                        <th>Apellidos</th> 
                                        <th>Usuario</th>
                                    </tr>     
                                </thead>
                                <tbody>
                                    <?php
                                    $lista = $resultado[1];
                                    if (count($lista) > 0) {
                                        foreach ($lista as $usuario) {
                                            echo '<tr>';
                                            echo '<td>' . $usuario['id'] . '</td>';
                                            echo '<td>' . $usuario['nombre'] . '</td>';
                                            echo '<td>' . $usuario['apellidos'] . '</td>';
                                            echo '<td>' . $usuario['username'] . '</td>';
                                            echo '<td>';
                                            echo '<a class="btn btn-sm btn-outline-success" href="editaUsuarioForm.php?id=' . $usuario['id'] . '" role ="button">Editar</a>';
                                            echo '<a class="btn btn-sm btn-outline-danger ms-2" href="borraUsuario.php?id=' . $usuario['id'] . '" role ="button">Borrar</a>';
                                            echo '<a class="btn btn-sm btn-outline-warning ms-2" href="../tareas/nuevaForm.php?id=' . $usuario['id'] . '" role ="button">Nueva Tarea</a>';                                    
                                        }
                                    } else {
                                        echo '<tr><td colspan="100">No hay usuarios</td></tr>';
                                    }
                                    ?>
                                </tbody>
                            </table>                           
                        </div>
                    <?php
                        } else {
                            echo '<div class="alert alert-warning" role="alert"' . $resultado[1] . '</div>';
                        }
                    ?>
                </div>
            </main>
        </div>
    </div>

    <?php include_once('../vista/footer.php'); ?>

</body>
</html>