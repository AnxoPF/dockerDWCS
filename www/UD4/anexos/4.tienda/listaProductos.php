<?php include_once('head.php'); ?>
<body>

    <?php include_once('header.php'); ?>

    <div class="container-fluid">
        <div class="row">
            <main class="col">
                
                <div class="container justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Lista de Productos</h2>
                </div>

                <div class="container justify-content-between">
                    <div class="table">
                        <table class="table table-striped table-hover">
                            <thead class="thead">
                                <tr>                            
                                    <th>Id</th>
                                    <th>Nombre</th>
                                    <th>descripcion</th>
                                    <th>Precio</th>
                                    <th>Unidades</th>
                                    <th>Foto</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                    require_once('database.php');
                                    $resultado = listaProductos();
                                    if ($resultado && $resultado[0])
                                    {
                                        $productos = $resultado[1];
                                        if ($productos)
                                        {
                                            foreach ($productos as $producto)
                                            {
                                                echo '<tr>';
                                                echo '<td>' . $producto['id'] . '</td>';
                                                echo '<td>' . $producto['nombre'] . '</td>';
                                                echo '<td>' . $producto['descripcion'] . '</td>';
                                                echo '<td>' . $producto['precio'] . '</td>';
                                                echo '<td>' . $producto['unidades'] . '</td>';
                                                echo "<td><img src='data:image/jpeg;base64," . base64_encode($producto['foto']) . "' width='100'></td>";
                                                echo '</tr>';
                                            }
                                        }
                                        else{
                                            echo '<tr><td colspan="100">No hay productos registrados</td></tr>';
                                        }
                                    }
                                    else
                                    {
                                        echo '<tr><td colspan="100">Error recuperando productos: ' . $resultado['1'] . '</td></tr>';
                                    }
                                    
                                ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <?php include_once('back.php'); ?>

            </main>
        </div>
    </div>
    
    <?php include_once('footer.php'); ?>
    
</body>
</html>
