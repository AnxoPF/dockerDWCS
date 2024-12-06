<?php

$globalTareas = [
    [
        [
            'id' => 1,
            'descripcion' => 'Corregir tarea unidad 2 grupo A',
            'estado' => 'Pendiente'
        ],
        [
            'id' => 2,
            'descripcion' => 'Corregir tarea unidad 2 grupo A',
            'estado' => 'Pendiente'
        ],
        [
            'id' => 3,
            'descripcion' => 'Preparación unidad 3',
            'estado' => 'En proceso'
        ],
        [
            'id' => 4,
            'descripcion' => 'Publicar en github solución de la tarea unidad 2',
            'estado' => 'Completada'
        ]
    ]
];

// Función para devolver el listado de tareas
function obtenerTareas() {
    global $globalTareas;
    return $globalTareas;
}

// Función para filtrar contenido
function filtrarContenido($contenido) {
    $contenido = trim($contenido);
    $contenido = stripslashes($contenido);
    $contenido = htmlspecialchars($contenido);
    return $contenido;
}

// Función para comprobar si un campo contiene información de texto válida
function esTextoValido($texto) {
    return (!empty(filtrarContenido($texto) && validarLargoCampo($texto, 2)));
}

function validarLargoCampo($texto, $longitud){
    return (strlen(trim($texto)) > $longitud);
}

function esNumeroValido($campo)
{
    return (!empty(filtraCampo($campo) && is_numeric($campo)));
}

function validaContrasena($campo)
{
    return (!empty($campo) && validarLargoCampo($campo, 7));
}

// Función para guardar una tarea de forma simulada
function guardarTarea($id, $descripcion, $estado) {
    global $globalTareas;

    if (esTextoValido($id) && esTextoValido($descripcion) && esTextoValido($estado)) {
        $nuevaTarea = [
            'id' => filtrarContenido($id),
            'descripcion' => filtrarContenido($descripcion) ,
            'estado' => filtrarContenido($estado)
        ];

        array_push($globalTareas, $nuevaTarea);

        return true; // Simular que se guardó correctamente
    } else {
        return false;
    }

}
