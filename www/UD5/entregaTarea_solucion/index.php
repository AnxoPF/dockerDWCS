<?php
    require_once('login/sesiones.php'); // sesiones.php nos redirigirá automáticamente a login.php si no existe el parametro usuario en la sesión.
?>

<?php include_once('vista/header.php'); // En el header se incluye ?> 

    <div class="container-fluid">
        <div class="row">
            
            <?php include_once('vista/menu.php'); ?>

            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4" >

                <div class="container d-flex justify-content-center align-items-center m-4" >
                    <img src="portada.webp" class="img-fluid" style="max-height: 500px; object-fit: cover;" alt="Portada"/>
                </div>

            </main>
        </div>
    </div>

    <?php include_once('vista/footer.php'); ?>
    
</body>
</html>
