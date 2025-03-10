<?php
require_once('../login/sesiones.php');
if (!checkAdmin()) redirectIndex(); // Comprueba que si no somos admin, nos redirigirá al Index

$message = 'Error borrando el usuario.';
$error = true;

require_once('../modelo/pdo.php');

if (!empty($_GET)) // Si el Get NO está vacío
{
    $id = $_GET['id']; // Recupera el id que recibe a través del Get, en una variable
    if (!empty($id)) // Si dicha variable no está vacía
    {
        $resultado = borraUsuario($id); // Usa la función borraUsuario pasandole el id que acabamos de almacenar en una variable desde el GET
        if ($resultado[0])
        {
            $message = 'Usuario borrado correctamente.';
            $error = false;
        }
        else
        {
            $message = 'No se pudo borrar el usuario.';
        }
    }
    else
    {
        $message = 'No se pudo recuperar la información del usuario.';
    }
}
else
{
    $message = 'Debes acceder a través del listado de usuarios.';
}

$status = $error ? 'error' : 'success';
header("Location: usuarios.php?status=$status&message=$message");
