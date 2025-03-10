<?php
require_once('../login/sesiones.php');
if (!checkAdmin()) redirectIndex();

require_once('../utils.php');
// Recoge en variables los datos del formulario
$id = $_POST['id'];
$nombre = $_POST['nombre'];
$apellidos = $_POST['apellidos'];
$username = $_POST['username'];
$contrasena = $_POST['contrasena'];
$rol = $_POST['rol'];

$response = 'error';
$messages = array();

require_once('../modelo/entity/Usuario.php');
require_once('../modelo/entity/Rol.php');

$errores = array();
if (!empty($contrasena)) // Si el campo contraseña NO está vacío, usa el método de validación de la clase Usuario
{
    $errores = Usuario::validate($_POST);
}
else // Si está vacío el campo contraseña, usa el método de validación de la clase Usuario que ignora los errores relacionados con el campo contraseña
{
    $errores = Usuario::validateWithoutPassword($_POST);
}

if (empty($errores)) // Si no hay errores
{
    // Crea un objeto usuario con los valores del formulario que han sido validados, y les pasa los filtros de utils.php
    $usuario = new Usuario(filtraCampo($nombre), filtraCampo($apellidos), filtraCampo($username), $contrasena, Rol::from((int)$rol));
    $usuario->setId($id); // Adjudica al objeto usuario el ID que recupera del propio formulario, a través del setter

    require_once('../modelo/pdo.php');
    $resultado = actualizaUsuario($usuario); // Utiliza la función actualizaUsuario, pasandole el objeto usuario que acabamos de crear
    
    if ($resultado[0])
    {
        $messages = ['Usuario actualizado correctamente.'];
        $response = 'success';
    }
    else
    {
        $messages = ['Ocurrió un error actualizando el usuario: ' . $resultado[1]];
    }
}
else
{
    $messages = $errores;
}

$_SESSION['status'] = $response;
$_SESSION['messages'] = $messages;
header("Location: editaUsuarioForm.php?id=$id");
