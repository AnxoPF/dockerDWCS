<?php

require_once('FicherosDBInt.php');
require_once(__DIR__ . '/../pdo.php');

class FicherosDBImp implements FicherosDBInt {
    public function listaFicheros($id_tarea): array
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
            throw new DatabaseException("Error al obtener la lista de ficheros", __METHOD__, $sql, 0, $e);
        }
        finally
        {
            $con = null;
        }
    }

    public function buscaFichero($id): ?Fichero
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
            throw new DatabaseException("Error al buscar el fichero", __METHOD__, $sql, 0, $e);
        }
        finally
        {
            $con = null;
        }
    }

    public function borraFichero($id): bool
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
            throw new DatabaseException("Error al borrar el fichero", __METHOD__, $sql, 0, $e);
        }
        finally
        {
            $con = null;
        }
    }

    public function nuevoFichero($fichero): bool
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
    
            return true;
        }
        catch (PDOException $e)
        {
            throw new DatabaseException("Error al insertar el fichero", __METHOD__, $sql, 0, $e);
        }
        finally
        {
            $con = null;
        }
    }
}