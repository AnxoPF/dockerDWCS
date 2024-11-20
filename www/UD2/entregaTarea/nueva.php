<!-- nueva.php -->
<?php
include 'utils.php'; // Asegúrate de que este archivo existe y tiene el método que necesitas

$errores = [];

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $titulo = $_POST['titulo'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $prioridad = $_POST['prioridad'] ?? '';

    // Validaciones simples
    if (empty($titulo)) {
        $errores[] = "El título es obligatorio.";
    }
    if (empty($descripcion)) {
        $errores[] = "La descripción es obligatoria.";
    }
    if (empty($prioridad)) {
        $errores[] = "La prioridad es obligatoria.";
    }

    // Si no hay errores, simular guardar la tarea
    if (empty($errores)) {
        // Llama a la función de utils.php que simula guardar
        guardarTarea($titulo, $descripcion, $prioridad); // Asegúrate de que la función existe y hace lo que necesitas

        // Mensaje de éxito
        $mensaje = "La tarea ha sido creada exitosamente.";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultado de Nueva Tarea</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <!-- Header -->
    <?php include 'header.php'; ?>

    <div class="container-fluid">
        <div class="row">
            <!-- Menu -->
            <?php include 'menu.php'; ?>

            <!-- Main Content -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h2>Resultado de Nueva Tarea</h2>
                </div>
                <div class="container">
                    <?php if (!empty($errores)): ?>
                        <div class="alert alert-danger">
                            <ul>
                                <?php foreach ($errores as $error): ?>
                                    <li><?php echo $error; ?></li>
                                <?php endforeach; ?>
                            </ul>
                        </div>
                    <?php elseif (isset($mensaje)): ?>
                        <div class="alert alert-success">
                            <?php echo $mensaje; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </main>
        </div>
    </div>

    <!-- Footer -->
    <?php include 'footer.php'; ?>
</body>
</html>
