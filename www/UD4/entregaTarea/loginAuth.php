<?php
session_start();
require_once('bbdd/pdo.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $contrasena = $_POST['contrasena'];

    if (empty($username) || empty($contrasena))
    {
        header('Location: login.php?error=true&message=Los campos del formulario son obligatorios.');
    }

    $pdo = conectarPDO();
    $stmt = $pdo->prepare('SELECT * FROM usuarios WHERE username = ?');
    $stmt->execute([$username]);
    $usuario = $stmt->fetch();

    if ($usuario && password_verify($contrasena, $usuario['contrasena'])) {
        $_SESSION['usuario'] = [
            'id' => $usuario['id'],
            'username' => $usuario['username'],
            'rol' => $usuario['rol']
        ];
        header('Location: index.php');
        exit;
    } else {
        echo '<div class="alert alert-danger" role="alert">Usuario o contraseña incorrectos.</div>';
    }
}