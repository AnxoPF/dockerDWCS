<?php
session_start();
if (!isset($_SESSION['usuario'])) {
    header('Location: login.php');
    exit();
}

require_once('modelo/pdo.php');

$usuario = $_SESSION['usuario'];
list($success, $tareas) = listaTareasPDO($usuario['id'], null);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <?php include('vista/header.php'); ?>
    <div class="container mt-4">
        <h2>Bienvenido, <?= htmlspecialchars($usuario['nombre']) ?>!</h2>
        <a href="logout.php" class="btn btn-danger mb-3">Cerrar Sesión</a>

        <h3>Tus Tareas</h3>
        <?php if ($success && !empty($tareas)): ?>
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Título</th>
                        <th>Descripción</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($tareas as $tarea): ?>
                        <tr>
                            <td><?= htmlspecialchars($tarea['titulo']) ?></td>
                            <td><?= htmlspecialchars($tarea['descripcion']) ?></td>
                            <td><?= htmlspecialchars($tarea['estado']) ?></td>
                            <td>
                                <a href="editar_tarea.php?id=<?= $tarea['id'] ?>" class="btn btn-warning btn-sm">Editar</a>
                                <a href="borrar_tarea.php?id=<?= $tarea['id'] ?>" class="btn btn-danger btn-sm">Borrar</a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?>
            <div class="alert alert-info">No tienes tareas asignadas.</div>
        <?php endif; ?>
        <a href="nueva_tarea.php" class="btn btn-success mt-3">Agregar Nueva Tarea</a>
    </div>
</body>
</html>
