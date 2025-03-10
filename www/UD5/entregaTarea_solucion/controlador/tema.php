<?php

// Función para comprobar si la variable tema tiene uno de los 3 valores válidos, devolverá false si no es el caso.
function temaValido($tema)
{
    return ($tema == 'dark' || $tema = 'light' || $tema == 'auto');
}

// Guarda en una variable la ruta donde estamos, para redirigirnos después
$origen = $_SERVER['HTTP_REFERER'];

//Comprobar si se reciben los datos
if($_SERVER["REQUEST_METHOD"]=="POST"){
    $tema = $_POST["tema"];
    
    // Si la variable tema está vacío o no tiene un valor valido, mostramos un mensaje de aviso
    if (empty($tema) || !temaValido($tema))
    {
        header('Location: ' . $origen . '?error=true&message=Debes indicar un tema válido.');
    }

    // Crea la cookie tema, con el valor que se le ha pasado a través del formulario, y después de comprobar que es valido.
    setcookie('tema', $tema, time() + (86400 * 30), "/");
    // Redirección al sitio de donde veníamos
    header('Location: ' . $origen);
}
else // Si no recibe información a través del formulario, nos redirige con un aviso
{
    header('Location: ' . $origen . '?error=true&message=Debes indicar un tema válido.');
}