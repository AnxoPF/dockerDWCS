<?php

require_once(__DIR__ . '/entity/Fichero.php');
require_once(__DIR__ . '/entity/Usuario.php');
require_once(__DIR__ . '/entity/Tarea.php');


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
            $tarea = new Tarea($row['id'], $row['titulo'], $row['descripcion'], $row['estado'], $usuario);
            array_push($tareas, $tarea);
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
        $sql = "UPDATE usuarios SET username = ?, nombre = ?, apellidos = ?, rol = ?";

        $params = [$usuario->getUsername(), $usuario->getNombre(), $usuario->getApellidos(), $usuario->getRol()];
        
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

function borraUsuario($usuario)
{
    try {
        $con = conectaPDO();

        $con->beginTransaction();

        $stmt = $con->prepare('DELETE FROM tareas WHERE id_usuario = ' . $usuario->getId());
        $stmt->execute();
        $stmt = $con->prepare('DELETE FROM usuarios WHERE id = ' . $usuario->getId());
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
            $resultado['nombre'],
            $resultado['apellidos'],
            $resultado['username'],
            $resultado['contrasena'],
            $resultado['rol']);
        }
        else
        {
            return null;
        }
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
            $tarea = buscaTareaPDO($row['id_tarea']);
            $fichero = new Fichero($row['id'], $row['nombre'], $row['file'], $row['descripcion'], $tarea);
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
            $tarea = buscaTareaPDO($row['id_tarea']);

            $fichero = new Fichero(
                $row['id'],
                $row['nombre'],
                $row['file'],
                $row['descripcion'],
                $tarea
            );
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

function nuevoFichero($fichero)
{
    try
    {
        $con = conectaPDO();
        $stmt = $con->prepare("INSERT INTO ficheros (nombre, file, descripcion, id_tarea) VALUES (:nombre, :file, :descripcion, :idTarea)");
        
        $file = $fichero->getFile();
        $nombre = $fichero->getNombre();
        $descripcion = $fichero->getDescripcion();
        $id_tarea = $fichero->getIdTarea();
        
        
        $stmt->bindParam(':file', $file);
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':descripcion', $descripcion);
        $stmt->bindParam(':idTarea', $id_tarea);
        $stmt->execute();
        
        $fichero->setId($con->lastInsertId());
        
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

function buscaTareaPDO($id)
{

    try
    {
        $con = conectaPDO();
        $stmt = $con->prepare('SELECT * FROM tareas WHERE id = ' . $id);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        $resultado = $stmt->fetch();
        $usuario = buscaUsuario($resultado['id_usuario']);

        if ($resultado)
        {
            return new Tarea(
                $resultado['id'],
                $resultado['titulo'],
                $resultado['descripcion'],
                $resultado['estado'],
                $usuario
            );
        }
        else
        {
            return null;
        }
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