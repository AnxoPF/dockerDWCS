<?php
// utils.php

// Array global que almacenará las tareas
$globalTareas = [];

// Función para devolver el listado de tareas
function obtenerTareas() {
    global $globalTareas;
    return $globalTareas;
}

// Función para filtrar contenido
function filtrarContenido($contenido) {
    // Eliminar caracteres especiales y espacios duplicados
    $contenido = trim($contenido); // Eliminar espacios al inicio y al final
    $contenido = preg_replace('/\s+/', ' ', $contenido); // Reemplazar múltiples espacios por uno solo
    $contenido = htmlspecialchars($contenido); // Convertir caracteres especiales a entidades HTML
    return $contenido;
}

// Función para comprobar si un campo contiene información de texto válida
function esTextoValido($texto) {
    $textoFiltrado = filtrarContenido($texto);
    return !empty($textoFiltrado); // Debe ser no vacío tras filtrar
}

// Función para guardar una tarea de forma simulada
function guardarTarea($titulo, $descripcion, $estado) {
    global $globalTareas;

    // Filtrar y validar campos
    $tituloFiltrado = filtrarContenido($titulo);
    $descripcionFiltrada = filtrarContenido($descripcion);
    $estadoFiltrado = filtrarContenido($estado);

    if (esTextoValido($tituloFiltrado) && esTextoValido($descripcionFiltrada) && esTextoValido($estadoFiltrado)) {
        // Crear un nuevo array para la tarea
        $nuevaTarea = [
            'id' => count($globalTareas) + 1, // Asignar un ID único
            'descripcion' => $descripcionFiltrada,
            'estado' => $estadoFiltrado
        ];

        // Agregar la nueva tarea al array global
        $globalTareas[] = $nuevaTarea;

        return true; // Simular que se guardó correctamente
    }

    return false; // No se pudo guardar la tarea
}
