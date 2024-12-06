<?php

function conectaPDO() {
    $servername = 'db';
    $username = 'root';
    $password = 'test';
    $dbname = 'tareas';

    $conPDO = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conPDO;
}

function listaUsuarios(){
    try {
        $con = conectaPDO();
        $stmt = $con->prepare('SELECT id, username, nombre, apellidos FROM usuarios');
        $stmt->execute();

        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $resultados = $stmt->fetchAll();
        return [true, $resultados];
    } catch (PDOException $e) {
        return [false, $e->getMessage()];
    } finally {
        $con = null;
    }
}

function nuevoUsuario($nombre, $apellidos, $username, $contrasena) {
    try {
        $con = conectaPDO();
        $stmt = $con->prepare("INSERT INTO usuarios (nombre, apellidos, username, contrasena) 
                                VALUES (:nombre, :apellidos, :username, :contrasena)");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':contrasena', $contrasena);
        $stmt->execute();

        $stmt->closeCursor();

        return [true, null];
    } catch (PDOException $e) {
        return [false, $e->getMessage()];
    } finally {
        $con = null;
    }
}

function buscarUsuario($id) {
    try {
        $con = conectaPDO();
        $stmt = $con->prepare('SELECT * FROM usuarios WHERE id = ' . $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() == 1) {
            return $stmt->fetch();
        } else {
            return null;
        }
    } catch (PDOException $e) {
        return null;
    } finally {
        $conPDO = null;
    }
}

function actualizarUsuario($id, $nombre, $apellidos, $username, $contrasena) {
    try {
        $con = conectaPDO();
        $sql = "UPDATE usuarios SET nombre = :nombre, apellidos = :apellidos, username = :username";

        if (isset($contrasena)) {
            $sql = $sql . ', contrasena = :contrasena';
        }

        $sql = $sql . ' WHERE id = :id';

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':username', $username);
        if (isset($contrasena)) $stmt->bindParam(':contrasena', $contrasena);
        $stmt->bindParam(':id', $id);
        
        $stmt->execute();

        $stmt->closeCursor();

        return [true, null];
    } catch (PDOException $e) {
        return [false, $e->getMessage()];
    } finally {
        $con = null;
    }
}

function borrarUsuario($id) {
    try {
        $con = conectaPDO();

        $con->beginTransaction();

        $stmt = $con->prepare('DELETE FROM tareas WHERE id_usuario = ' . $id);
        $stmt->execute();
        $stmt = $con->prepare('DELETE FROM usuarios WHERE id = ' . $id);
        $stmt->execute();

        return [$con->commit(), ''];
    } catch (PDOException $e) {
        return [false, $e->getMessage()];
    } finally {
        $con = null;
    }
}