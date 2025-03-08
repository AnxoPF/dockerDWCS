<?php
session_start();

$raiz = $_ENV['RAIZ_UD4'];

if (!checkSession()) {	
    redirectLogin();
}

function checkSession()
{
    return isset($_SESSION['usuario']);
}

function redirectLogin()
{
    global $raiz;
    header("Location: $raiz/login/login.php?redirect=true");
    exit();
}

function checkAdmin()
{
    global $raiz;
    return (checkSession() && $_SESSION['usuario']['rol'] == 1);
}

function redirectIndex()
{
    global $raiz;
    header("Location: $raiz/index.php?redirect=true");
    exit();
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>UD4. Tarea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">
</head>
<body>

<header class="bg-primary text-white text-center py-3">
    <h1>Gestión de tareas</h1>
    <p>Solución tarea unidad 4 de DWCS</p>
</header>
