<?php

require_once('../login/sesiones.php');
require_once('../modelo/mysqli.php');
require_once('../modelo/pdo.php');

$location = '../tareas.php';
$response = 'error';
$messages = array();

$error = false;

if ($_SERVER['REQUEST_METHOD'] === 'POST')
{

    $nombreArchivo = $_POST['nombre'] ?? '';
    $descripcion = $_POST['descripcion'] ?? '';
    $archivo = $_FILES['file'] ?? null;
    $id_tarea = $_POST['id_tarea'] ?? '';
    $location = 'subidaFichForm.php?id=' . $id_tarea;

    $tarea = buscaTarea($id_tarea);

    if (!$tarea) {
        $messages[] = "La tarea seleccionada no es válida.";
    } else {
        $erroresValidacion = Fichero::validarCampos($nombreArchivo, $archivo['name'], $descripcion, $tarea);

        if (!empty($erroresValidacion)) {
            $messages = array_merge($messages, array_values($erroresValidacion));
        } else {
            $codigoAleatorio = bin2hex(random_bytes(8));
            $extension = pathinfo($archivo['name'], PATHINFO_EXTENSION);
            $nombreFinal = $codigoAleatorio . '.' . $extension;
            $rutaDestino = '../files/' . $nombreFinal;

            if (!is_writable('../files'))
            {
                $messages[] = "No hay permisos de escritura en la carpeta destino.";
            } else if (move_uploaded_file($archivo['tmp_name'], $rutaDestino)) {
                $fichero = new Fichero(0, $nombreArchivo, $nombreFinal, $descripcion, $tarea);
                $resultado = nuevoFichero($fichero);

                if ($resultado[0])
                {
                $response = 'success';
                $messages[] = 'Archivo subido correctamente.';
                $location = '../tareas/tarea.php?id=' . $id_tarea;
                }
                else
                {
                    $messages[] = 'Ocurrió un error guardando el fichero: ' . $resultado[1] . '.';
                }
            }
            else
            {
                $messages[] = 'Error al guardar el archivo.';
            }
        }
    }
} else {
    $messages[] = 'Método de solicitud no válido.';
}

$_SESSION['status'] = $response;
$_SESSION['messages'] = $messages;
header("Location: " . $location);