<?php

function conecta($host, $user, $pass, $db){
    $conexion = new mysqli($host, $user, $pass, $db);
    return $conexion;
}

function conectaTareas(){
    return conecta('db', 'root', 'test', 'tareas');
}

function cerrarConexion($conexion){
    if (isset($conexion) && $conexion->connect_errno === 0) {
        $conexion->close();
    }
}

function creaDB()
{
    try {
        $conexion = conecta('db', 'root', 'test', null);
        
        if ($conexion->connect_error) {
            return [false, $conexion->error];
        } else {
            $sqlCheck = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'tareas'";
            $resultado = $conexion->query($sqlCheck);
            if ($resultado && $resultado->num_rows > 0) {
                return [false, 'La base de datos "tareas" ya existía.'];
            }

            $sql = 'CREATE DATABASE IF NOT EXISTS tareas';
            if ($conexion->query($sql)) {
                return [true, 'Base de datos "tareas" creada correctamente'];
            } else {
                return [false, 'No se pudo crear la base de datos "tareas".'];
            }
        }
    } catch (mysqli_sql_exception $e) {
        return [false, $e->getMessage()];
    } finally {
        cerrarConexion($conexion);
    }
}

function crearTablaUsuarios(){
    try {
        $conexion = conectaTareas();

        if ($conexion->connect_error){
            return [false, $conexion->error];
        } else {
            $sqlCheck = "SHOW TABLES LIKE 'usuarios'";
            $resultado = $conexion->query($sqlCheck);

            if ($resultado && $resultado->num_rows > 0) {
                return [false, 'La tabla "usuarios" ya existía.'];
            }

            $sql = 'CREATE TABLE IF NOT EXISTS `usuarios` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `username` VARCHAR(50) NOT NULL,
                `nombre` VARCHAR(50) NOT NULL,
                `apellidos` VARCHAR(100) NOT NULL,
                `contrasena` VARCHAR(100) NOT NULL, 
                PRIMARY KEY (`id`)
                )';
            if ($conexion->query($sql)){
                return [true, 'Tabla "usuarios" creada correctamente.'];
            } else {
                return [false, 'No se pudo crear la tabla "usuarios'];
            }
        }
    } catch (mysqli_sql_exception $e) {
        return [false, $e->getMessage()];
    } finally {
        cerrarConexion($conexion);
    }
}

function crearTablaTareas(){
    try {
        $conexion = conectaTareas();

        if ($conexion->connect_error){
            return [false, $conexion->error];
        } else {
            $sqlCheck = "SHOW TABLES LIKE 'tareas'";
            $resultado = $conexion->query($sqlCheck);

            if ($resultado && $resultado->num_rows > 0) {
                return [false, 'La tabla "tareas" ya existía.'];
            }

            $sql = 'CREATE TABLE IF NOT EXISTS `tareas` (
                `id` INT NOT NULL AUTO_INCREMENT,
                `titulo` VARCHAR(50) NOT NULL,
                `descripcion` VARCHAR(250) NOT NULL,
                `estado` VARCHAR(50) NOT NULL,
                `id_usuario` INT NOT NULL, 
                PRIMARY KEY (`id`), 
                FOREIGN KEY (`id_usuario`) REFERENCES `usuarios`(`id`)
                )';
            if ($conexion->query($sql)){
                return [true, 'Tabla "tareas" creada correctamente.'];
            } else {
                return [false, 'No se pudo crear la tabla "tareas'];
            }
        }
    } catch (mysqli_sql_exception $e) {
        return [false, $e->getMessage()];
    } finally {
        cerrarConexion($conexion);
    }
}