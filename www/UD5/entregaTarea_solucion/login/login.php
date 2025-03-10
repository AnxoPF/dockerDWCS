<?php
    session_start();
    // Iniciamos la sesión y comprobamos si existe la variable de sesión 'usuario'
    if (isset($_SESSION['usuario']))
    {
        // Si es el caso, redirigimos a index con el añadido "redirect"
        header("Location: ../index.php?redirect=true");
        exit();
    }
?>

<?php include_once('../vista/header.php'); ?>
    
    <main class="d-flex justify-content-center align-items-center flex-grow-1 p-4 m-4" style="background-image: url('../portada.webp'); background-size: cover; background-position: center;">
        <div class="position-absolute top-0 start-0 w-100 h-100 bg-light" style="opacity: 0.6;"></div>
        <div class="card shadow p-4 w-100" style="max-width:400px">
            
            <h2 class="text-center mb-4">Iniciar sesión</h2>

            <?php
            //Comprobar si se reciben datos a través de GET
            $redirect = isset($_GET['redirect']) ? true : false;
            $error = isset($_GET['error']) ? true : false;
            $message = isset($_GET['message']) ? $_GET['message'] : null;
            if ($redirect) // Si hemos recibido el dato redirect, mostramos el mensaje de alerta de que se debe iniciar sesión
            {
                echo '<div class="alert alert-danger" role="alert">Debes iniciar sesión para acceder.</div>';
            }
            elseif ($error)
            {
                if ($message) // Si hemos recibido el dato error, y además el dato message, mostramos un mensaje de alerta con el mensaje del valor que hemos recibido
                {
                    echo '<div class="alert alert-danger" role="alert">Error: ' . $message . '</div>';
                }
                else // Si no venía un dato message, mostramos nosotros el mensaje de Usuario y contraseña incorrectos en la alerta
                {
                    echo '<div class="alert alert-danger" role="alert">Usuario y contraseña incorrectos.</div>';
                }
            }
            ?>
            <!-- El formulario para introducir el usuario y la contraseña, que se procesará en "loginAuth.php" -->
            <form action="loginAuth.php" method="POST" class="needs-validation text-center">
                <div class="mb-3">
                    <input name="username" id="username" type="text" class="form-control" placeholder="usuario" required>
                </div>
                <div class="mb-3">
                    <input name="pass" id="pass" type="password" class="form-control" placeholder="contraseña" required>
                </div>
                <button type="submit" class="btn btn-primary">Iniciar sesión</button>
            </form>
            
        </div>
    
    </main>

    <?php include_once('../vista/footer.php'); ?>

</body>
</html>