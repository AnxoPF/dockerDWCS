<?php
session_start();

$raiz = "/UD5/entregaTarea";

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