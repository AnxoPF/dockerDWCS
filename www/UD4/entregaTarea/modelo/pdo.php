<?php

function conectarPDO() {
    $servername = $_ENV['DATABASE_HOST'];
    $username = $_ENV['DATABASE_USER'];
    $password = $_ENV['DATABASE_PASSWORD'];
    $dbname = $_ENV['DATABASE_NAME'];

    $conPDO = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conPDO;
}

function listaUsuarios() {
    try {
        $con = conectarPDO();
        $stmt = $con->prepare('SELECT id, username, nombre, apellidos, rol, contrasena FROM usuarios');
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

function listaTareasPDO($id_usuario, $estado = null) {
    try {
        $con = conectarPDO();
        $sql = 'SELECT * FROM tareas WHERE id_usuario = :id_usuario';
        if ($estado) {
            $sql .= ' AND estado = :estado';
        }
        $stmt = $con->prepare($sql);
        $stmt->bindParam(':id_usuario', $id_usuario, PDO::PARAM_INT);
        if ($estado) {
            $stmt->bindParam(':estado', $estado, PDO::PARAM_STR);
        }
        $stmt->execute();

        $tareas = $stmt->fetchAll();
        foreach ($tareas as &$tarea) {
            $usuario = buscaUsuario($tarea['id_usuario']);
            $tarea['id_usuario'] = $usuario['username'];
        }

        return [true, $tareas];
    } catch (PDOException $e) {
        return [false, $e->getMessage()];
    } finally {
        $con = null;
    }
}

function nuevoUsuario($nombre, $apellidos, $username, $contrasena, $rol=0) {
    try {
        $con = conectarPDO();
        $stmt = $con->prepare("INSERT INTO usuarios (nombre, apellidos, username, rol, contrasena) 
                               VALUES (:nombre, :apellidos, :username, :rol, :contrasena)");
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':rol', $rol);
        $hasheado = password_hash($contrasena, PASSWORD_DEFAULT)
        $stmt->bindParam(':contrasena', $hasheado);
        $stmt->execute();

        $stmt->closeCursps();

        return [true, null];
    } catch (PDOException $e) {
        return [false, $e->getMessage()];
    } finally {
        $con = null;
    }
}

function actualizaUsuario($id, $nombre, $apellidos, $username, $contrasena, $rol) {
    try {
        $con = conectarPDO();
        $sql = "UPDATE usuarios SET nombre = :nombre, apellidos = :apellidos, username = :username, rol = :rol";
        if ($contrasena) {
            $sql .= ", contrasena = :contrasena";
        }
        $sql .= " WHERE id = :id";

        $stmt = $con->prepare($sql);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':rol', $rol);
        $stmt->bindParam(':id', $id);
        if (isset($contrasena)) {
            $hasheado = password_hash($contrasena, PASSWORD_DEFAULT);
            $stmt->bindParam(':contrasena', $hasheado);
        }
        $stmt->execute();

        $stmt->closeCursor();

        return [true, null];
    } catch (PDOException $e) {
        return [false, $e->getMessage()];
    } finally {
        $con = null;
    }
}

function borraUsuario($id) {
    try {
        $con = conectarPDO();
        $con->beginTransaction();

        $stmt = $con->prepare('DELETE FROM tareas WHERE id_usuario = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $stmt = $con->prepare('DELETE FROM usuarios WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        $con->commit();
        return [true, 'Usuario y tareas eliminados correctamente.'];
    } catch (PDOException $e) {
        $con->rollBack();
        return [false, $e->getMessage()];
    } finally {
        $con = null;
    }
}

function buscaUsuario($id) {
    try {
        $con = conectarPDO();
        $stmt = $con->prepare('SELECT * FROM usuarios WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch() ?: null;
    } catch (PDOException $e) {
        return null;
    } finally {
        $con = null;
    }
}

function crearTablaUsuarios(PDO $pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS usuarios (
                id INT AUTO_INCREMENT PRIMARY KEY,
                username VARCHAR(50) UNIQUE NOT NULL,
                nombre VARCHAR(50) NOT NULL,
                apellidos VARCHAR(100) NOT NULL,
                contrasena VARCHAR(255) NOT NULL,
                rol TINYINT(1) DEFAULT 0
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql);
}

function crearTablaTareas(PDO $pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS tareas (
                id INT AUTO_INCREMENT PRIMARY KEY,
                titulo VARCHAR(100) NOT NULL,
                descripcion TEXT NOT NULL,
                estado VARCHAR(50) NOT NULL,
                id_usuario INT,
                FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql);
}

function crearTablaFicheros(PDO $pdo) {
    $sql = "CREATE TABLE IF NOT EXISTS ficheros (
                id INT AUTO_INCREMENT PRIMARY KEY,
                nombre VARCHAR(100) NOT NULL,
                file VARCHAR(250) NOT NULL,
                descripcion VARCHAR(250),
                id_tarea INT,
                FOREIGN KEY (id_tarea) REFERENCES tareas(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
    $pdo->exec($sql);
}
