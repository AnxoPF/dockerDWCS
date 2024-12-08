<?php

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
    return (!empty(filtrarContenido($campo) && is_numeric($campo)));
}

function validaContrasena($campo)
{
    return (!empty($campo) && validarLargoCampo($campo, 7));
}

