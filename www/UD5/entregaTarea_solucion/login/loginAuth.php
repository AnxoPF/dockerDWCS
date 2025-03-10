<?php
require_once('../modelo/entity/Usuario.php');
require_once('../modelo/entity/Rol.php');
session_start();

require_once('../modelo/pdo.php');

function comprobarUsuario($nombre, $pass, $conPDO)
{
    // Buscamos en la base de datos un usuario que coincida con el nombre introducido
    $usuarioBD = buscaUsername($nombre);

    // Comprobamos que existe el usuario en la base de datos
    if ($usuarioBD)
    {
        // Guardamos en una variable la contraseña del usuario de la base de datos que coincidia con el nombre introducido
        $passBD=$usuarioBD->getContrasena();
        // Comprobamos si la contraseña introducida coincide con la anterior
        if (password_verify($pass, $passBD))
        {
            // Si coincide la contraseña, creamos un objeto Usuario al que le damos:
            $usuario = new Usuario();
            // El mismo ID que tenía el Usuario en la base de datos
            $usuario->setId($usuarioBD->getId());
            // El username que rescatamos directamente del parámetro que nos han pasado a la función, que usamos antes para buscar al usuario en la BBDD también
            $usuario->setUsername($nombre);
            // Lo mismo con el rol que con el ID
            $usuario->setRol($usuarioBD->getRol());
            // Nos devuelve un objeto Usuario con los parámetros que le acabamos de dar
            return $usuario;
        }
        else // Si la contraseña introducida no coincide con la de su usuario en la BBDD, devuelve null
        {
            return null;
        }
    }
    else // Si no existe el usuario en la BBDD, devuelve null
    {
        return null;
    }
}

//Comprobar si se reciben los datos
if($_SERVER["REQUEST_METHOD"]=="POST"){
    // Guardamos en las variables los 2 datos del formulario
    $usuario = $_POST["username"];
    $pass = $_POST["pass"];

    // >>>> IMPORTANTE: estas líneas están solo para permitir el primer acceso, cuando no está creada la base de datos. Solo se deben descomentar para la primera conexión.
    // Si coincide con estos valores en concreto, creamos un usuario, esto está pensado para cuando no hay usuarios creados en la base de datos aún
    if ($usuario == 'admintest' && $pass == 'test123')
    {
        $user = new Usuario();
        $user->setUsername('admintest');
        $user->setRol(Rol::ADMIN);
        $user->setId(0);
        $_SESSION['usuario'] = $user;
        //Redirigimos a index.php
        header('Location: ../index.php');
        exit();
    }

    // Si uno de los 2 valores resulta estar vacio, redirigimos de vuelta al login con un mensaje personalizado
    if (empty($usuario) || empty($pass))
    {
        header('Location: ./login.php?error=true&message=Los campos del formulario son obligatorios.');
    }

    // Creamos la variable de conexión a la BBDD
    $conPDO = conectaPDO();
    // Comprobación de que no da error la conexión
    if (is_string($conPDO))
    {
        header('Location: ./login.php?error=true&message=' . $conPDO);
    }

    // Usamos la función comprobarUsuario creada anteriormente
    $user = comprobarUsuario($usuario, $pass, $conPDO);
    // Si la función nos devuelve un "user" null, redirigimos de vuelta a login con "error=true"
    if(!$user)
    {
        header('Location: ./login.php?error=true');
    }
    elseif (is_string($user)) // Si la función nos devuelve un string, redirigimos a login con un mensaje de error personalizado
    {
        header('Location: ./login.php?error=true&message=' . $user);
    }
    else // Si no se da ninguno de los 2 errores anteriores
    {
        // Establecemos la variable de sesión "usuario", y le damos como valor el objeto Usuario con los parámetros pertinentes que recuperó la función anterior
        // Es decir el ID, el username, y el rol
        $_SESSION['usuario'] = $user;
        //Redirigimos a index.php
        header('Location: ../index.php');
    }
}