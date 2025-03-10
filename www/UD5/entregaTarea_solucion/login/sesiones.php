<?php
require_once(__DIR__ . '/../modelo/entity/Rol.php');
require_once(__DIR__ . '/../modelo/entity/Usuario.php');
session_start();
// Aquí estamos creando una variable cuyo valor es la ruta absoluta de la carpeta donde estamos, pero eliminando la ruta del directorio raíz del servidor web
$url_sesiones = str_replace($_SERVER['DOCUMENT_ROOT'], '', __DIR__); 

// Usamos la función que describimos abajo, si devuelve false usamos la otra función
if (!checkSession()) {	
    // La cual nos redirigirá a login, con el valor "redirect"
    redirectLogin();
}

// Función que comprueba si existe el parámetro "usuario" en la sesión y devolverá true/false
function checkSession()
{
    return isset($_SESSION['usuario']);
}

// Función que nos redirigirá a login con el valor "redirect"
function redirectLogin()
{
    global $url_sesiones;
    header("Location: $url_sesiones/login.php?redirect=true");
    exit();
}

// Función que comprueba si hay una sesión de usuario, y recupera su valor Rol. Si es Admin, devolverá true.
function checkAdmin()
{
    return (checkSession() && $_SESSION['usuario']->getRol() == Rol::ADMIN);
}

// Función para redirigir a Index.php, con el valor redirect
function redirectIndex()
{
    global $url_sesiones;
    header("Location: $url_sesiones/../index.php?redirect=true");
    exit();
}