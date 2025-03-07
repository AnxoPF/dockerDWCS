<?php include_once('head.php'); ?>
<body>

    <?php include_once('header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <main class="col">
                
                <div class="container justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Nuevo producto</h2>
                </div>

                <div class="container justify-content-between">
                    <?php
                    $nombre = $_POST['nombre'];
                    $descripcion = $_POST['descripcion'];
                    $precio = $_POST['precio'];
                    $unidades = $_POST['unidades'];
                    $target_dir = "fotos/";
                    $target_file = $target_dir . basename($_FILES["foto"]["name"]);
                    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                    $imagen = file_get_contents($_FILES['foto']['tmp_name']);

                    require_once('utils.php');
                    $error = false;
                    //verificar nombre
                    if (!validarCampoTexto($nombre))
                    {
                        $error = true;
                        echo '<div class="alert alert-danger" role="alert">El campo nombre es obligatorio y debe contener al menos 3 caracteres.</div>';
                    }    
                    if (!esNumeroValido($unidades))
                    {
                        $error = true;
                        echo '<div class="alert alert-danger" role="alert">El campo unidades es obligatorio y debe contener solo números.</div>';
                    }
                    //verificar fichero
                    if (!file_exists($target_file)) {
                        if ($_FILES["foto"]["size"] > (5 * 1024 * 1024)) {
                            echo '<div class="alert alert-danger" role="alert">La foto no debe superar los 5 Mb.</div>';
                            $error = true;
                        } else {
                            if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
                                echo '<div class="alert alert-danger" role="alert">La foto debe ser en formato png, jpg, jpeg o gif.</div>';
                                $error = true;
                            } else {
                                if (move_uploaded_file($_FILES["foto"]["tmp_name"], $target_file)) {
                                    echo "El fichero ". htmlspecialchars( basename( $_FILES["foto"]["name"])). "ha sido subido.";
                                }
                            }
                        }
                    } else {
                        echo '<div class="alert alert-danger" role="alert">La foto ya existe.</div>';
                        $error = true;
                    }
                    if (!$error)
                    {
                        require_once('database.php');
                        $resultado = nuevoProducto($nombre, $descripcion, $precio, $unidades, $imagen);
                        if ($resultado[0])
                        {
                            echo '<div class="alert alert-success" role="alert">Producto guardado correctamente.</div>';
                        }
                        else
                        {
                            echo '<div class="alert alert-danger" role="alert">Ocurrió un error guardando el producto: ' . $resultado[1] . '</div>';
                        }
                    }
                    ?>
                </div>

                <?php include_once('back.php'); ?>

            </main>
        </div>
    </div>
    
    <?php include_once('footer.php'); ?>
    
</body>
</html>
