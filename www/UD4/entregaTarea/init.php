<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UD3. Tarea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>

    <?php include_once('vista/header.php'); ?>

    <div class="container-fluid">
        <div class="row">
        
            <?php include_once('vista/menu.php'); ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="container justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Inicialización de la base de datos</h2>
                </div>

                <div class="container justify-content-between">
                    <?php
                        require_once('modelo/mysqli.php');
                        $resultado = creaDB();
                        if ($resultado[0]) {
                            echo '<div class="alert alert-success" role="alert">' . $resultado[1] . '</div>';
                        } else {
                            echo '<div class="alert alert-warning" role="alert">' . $resultado[1] . '</div>';
                        }

                        require_once('modelo/pdo.php');
                        try {
                            $conn = conectarPDO();
                            crearTablaUsuarios($conn);
                            crearTablaTareas($conn);
                            crearTablaFicheros($conn);
                            echo '<div class="alert alert-success" role="alert">Tablas creadas correctamente.</div>';
                        } catch (PDOException $e) {
                            echo '<div class="alert alert-danger" role="alert">Error creando las tablas: ' . $e->getMessage() . '</div>';
                        }
                    ?>
                </div>
            </main>
        </div>
    </div>

    <?php include_once('vista/footer.php'); ?>
    
</body>
</html>
