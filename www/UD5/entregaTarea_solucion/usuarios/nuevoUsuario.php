<?php
require_once('../login/sesiones.php');
if (!checkAdmin()) redirectIndex(); // Si no somos admin nos redirige a index
    
require_once('../utils.php');
// Recoge la información del formulario en variables
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$username = $_POST['username'];
$contrasena = $_POST['contrasena'];
$rol = $_POST['rol'];
$response = 'error';
$messages = array();

require_once('../modelo/entity/Usuario.php');
require_once('../modelo/entity/Rol.php');

// Usa la función de validación de la clase Usuario con los parametros del post
$errores = Usuario::validate($_POST);

// Si la función de validación no devuelve ningún error, procede
if (empty($errores))
{
    // crea un nuevo usuario con los campos del formulario ya valdiados, pasandoles la validación de utils además.
    $usuario = new Usuario(filtraCampo($nombre), filtraCampo($apellidos), filtraCampo($username), $contrasena, Rol::from((int)$rol));

    require_once('../modelo/pdo.php');
    // Crea un nuevo usuario en la BBDD a partir del objeto usuario que acabamos de crear, que tiene los valores del formulario
    $resultado = nuevoUsuario($usuario);
    if ($resultado[0]) 
    {
        $messages = ['Usuario guardado correctamente.'];
        $response = 'success';
    }
    else
    {
        $messages = ['Ocurrió un error guardando el usuario: ' . $resultado[1]];
    }
}
else
{
    $messages = $errores;
}

$_SESSION['status'] = $response;
$_SESSION['messages'] = $messages;
header("Location: nuevoUsuarioForm.php");

