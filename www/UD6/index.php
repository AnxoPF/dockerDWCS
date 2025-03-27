<?php

//Incluimos la librería de flight
require 'flight/Flight.php';

//Defimimos la ruta que vamos a utilizar y la función que vamos a invocar 
Flight::route('/', function () {
    echo 'hello world!';
});

//Iniciamos el servicio 
Flight::start();