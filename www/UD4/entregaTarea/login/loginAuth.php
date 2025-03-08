<?php
session_start();
require_once('../modelo/pdo.php');

function comprobarUsuario($nombre, $pass, $conPDO) {
    $usuarioBD = buscaUsername($nombre, $pass, $conPDO);

    if ($usuarioBD) {
        $passBD = $usuarioBD['contrasena'];

        if(password_verify($pass, $passBD)) {
            $usuario['username']=$nombre;
            $usuario['rol']=$usuarioBD['rol'];
            $usuario['id']=$usuarioBD['id'];
            return $usuario;
        } else {
            return null;
        }
    } else {
        return null;
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $contrasena = $_POST['contrasena'];

    // >>>> IMPORTANTE: estas líneas están solo para permitir el primer acceso, cuando no está creada la base de datos. Solo se deben descomentar para la primera conexión.
    if ($usuario == 'admintest' && $pass == 'test123')
    {
        $user['username']='admintest';
        $user['rol']=1;
        $user['id']=0;
        $_SESSION['usuario'] = $user;
        //Redirigimos a index.php
        header('Location: ../index.php');
        exit();
    }

    if (empty($username) || empty($contrasena))
    {
        header('Location: login.php?error=true&message=Los campos del formulario son obligatorios.');
    }

    $pdo = conectarPDO();
    $usuario = comprobarUsuario($username, $pass, $conPDO);

    if (!$usuario) {
        header('Location: login.php?error=true');
    } elseif (is_string($user)) {
        header('Location: ./login.php?error=true&message=' . $user);    
    } else {
        $_SESSION['usuario'] = $usuario;
        header('Location: ../index.php');
    }
}