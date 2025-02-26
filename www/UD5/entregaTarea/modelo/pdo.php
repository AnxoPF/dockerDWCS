<?php

require_once(__DIR__ . '/entity/Fichero.php');
require_once(__DIR__ . '/entity/Usuario.php');


function conectaPDO()
{
    $servername = $_ENV['DATABASE_HOST'];
    $username = $_ENV['DATABASE_USER'];
    $password = $_ENV['DATABASE_PASSWORD'];
    $dbname = $_ENV['DATABASE_NAME'];

    $conPDO = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conPDO;
}

function listaUsuarios()
{
    try {
        $con = conectaPDO();
        $stmt = $con->prepare('SELECT id, nombre, apellidos, username, contrasena, rol FROM usuarios');
        $stmt->execute();

        $usuarios = [];
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuarios[] = new Usuario($row['id'], $row['nombre'], $row['apellidos'], $row['username'], $row['contrasena'], $row['rol']);
        }
        return [true, $usuarios];
    }
    catch (PDOException $e) {
        return [false, $e->getMessage()];
    }
    finally {
        $con = null;
    }
    
}

function listaTareasPDO($id_usuario, $estado)
{
    try {
        $con = conectaPDO();
        $sql = 'SELECT * FROM tareas WHERE id_usuario = ' . $id_usuario;
        if (isset($estado))
        {
            $sql = $sql . " AND estado = '" . $estado . "'";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute();

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $tareas = array();
        while ($row = $stmt->fetch())
        {
            $usuario = buscaUsuario($row['id_usuario']);
            $row['id_usuario'] = $usuario->getUsername();
            array_push($tareas, $row);
        }
        return [true, $tareas];
    }
    catch (PDOException $e) {
        return [false, $e->getMessage()];
    }
    finally {
        $con = null;
    }
    
}

function nuevoUsuario($usuario)
{
    try{
        $con = conectaPDO();
        $stmt = $con->prepare("INSERT INTO usuarios (nombre, apellidos, username, rol, contrasena) VALUES (?, ?, ?, ?, ?)");
        $hasheado = password_hash($usuario->getContrasena(), PASSWORD_DEFAULT);
        
        $stmt->execute([$usuario->getNombre(), $usuario->getApellidos(), $usuario->getUsername(), $usuario->getRol(), $hasheado]);
        
        $stmt->closeCursor();

        return [true, null];
    }
    catch (PDOException $e)
    {
        return [false, $e->getMessage()];
    }
    finally
    {
        $con = null;
    }
}

function actualizaUsuario($usuario)
{
    try{
        $con = conectaPDO();
        $sql = "UPDATE usuarios SET nombre = ?, apellidos = ?, username = ?, rol = ?";

        $params = [$usuario->getNombre(), $usuario->getApellidos(), $usuario->getUsername(), $usuario->getRol()];
        
        if ($usuario->getContrasena() !== "x")
        {
            $sql .= ', contrasena = ?';
            $params[] = password_hash($usuario->getContrasena(), PASSWORD_DEFAULT);
        }

        $sql .= ' WHERE id = ?';
        $params[] = $usuario->getId();

        $con->prepare($sql)->execute($params);
        
        return [true, null];
    }
    catch (PDOExcetion $e)
    {
        return [false, $e->getMessage()];
    }
    finally
    {
        $con = null;
    }
}

function borraUsuario($id)
{
    try {
        $con = conectaPDO();

        $con->beginTransaction();

        $stmt = $con->prepare('DELETE FROM tareas WHERE id_usuario = ' . $id);
        $stmt->execute();
        $stmt = $con->prepare('DELETE FROM usuarios WHERE id = ' . $id);
        $stmt->execute();
        
        return [$con->commit(), ''];
    }
    catch (PDOExcetion $e)
    {
        return [false, $e->getMessage()];
    }
    finally
    {
        $con = null;
    }
}

function buscaUsuario($id)
{

    try
    {
        $con = conectaPDO();
        $stmt = $con->prepare('SELECT id, username, nombre, apellidos, rol, contrasena FROM usuarios WHERE id = ' . $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $resultado = $stmt->fetch();

        if ($resultado)
        {
            return new Usuario(
            $resultado['id'],
            $resultado['username'],
            $resultado['nombre'],
            $resultado['apellidos'],
            $resultado['contrasena'],
            $resultado['rol']);
        }
        else
        {
            return null;
        }
    }
    catch (PDOExcetion $e)
    {
        return null;
    }
    finally
    {
        $con = null;
    }
    
}

function buscaUsername($username)
{
    try
    {
        $con = conectaPDO();
        $stmt = $con->prepare('SELECT id, rol, contrasena FROM usuarios WHERE username = "' . $username . '"');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        if ($stmt->rowCount() == 1)
        {
            return $stmt->fetch();
        }
        else
        {
            return null;
        }
    }
    catch (PDOExcetion $e)
    {
        return null;
    }
    finally
    {
        $con = null;
    }
    
}

function listaFicheros($id_tarea)
{
    try
    {
        $con = conectaPDO();
        $sql = 'SELECT * FROM ficheros WHERE id_tarea = ' . $id_tarea;
        $stmt = $con->prepare($sql);
        $stmt->execute();

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $ficheros = array();
        while ($row = $stmt->fetch())
        {
            $fichero = new Fichero($row['id'], $row['nombre'], $row['file'], $row['descripcion'], $row['id_tarea']);
            array_push($ficheros, $fichero);
        }
        return $ficheros;
    }
    catch (PDOException $e)
    {
        return array();
    }
    finally
    {
        $con = null;
    }
}

function buscaFichero($id)
{
    try
    {
        $con = conectaPDO();
        $sql = 'SELECT * FROM ficheros WHERE id = ' . $id;
        $stmt = $con->prepare($sql);
        $stmt->execute();

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $fichero = null;
        if ($row = $stmt->fetch())
        {
            $fichero = $row;
        }
        return $fichero;
    }
    catch (PDOException $e)
    {
        return null;
    }
    finally
    {
        $con = null;
    }
}

function borraFichero($id)
{
    try
    {
        $con = conectaPDO();
        $sql = 'DELETE FROM ficheros WHERE id = ' . $id;
        $stmt = $con->prepare($sql);
        $stmt->execute();

        return true;
    }
    catch (PDOException $e)
    {
        return false;
    }
    finally
    {
        $con = null;
    }
}

function nuevoFichero($file, $nombre, $descripcion, $idTarea)
{
    try
    {
        $con = conectaPDO();
        $stmt = $con->prepare("INSERT INTO ficheros (nombre, file, descripcion, id_tarea) VALUES (:nombre, :file, :descripcion, :idTarea)");
        $stmt->bindParam(':file', $file);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':idTarea', $idTarea);
        $stmt->execute();
        
        $stmt->closeCursor();

        return [true, null];
    }
    catch (PDOExcetion $e)
    {
        return [false, $e->getMessage()];
    }
    finally
    {
        $con = null;
    }
}