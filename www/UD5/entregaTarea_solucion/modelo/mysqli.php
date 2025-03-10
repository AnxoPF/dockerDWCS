<?php

require_once __DIR__ . '/entity/Estado.php';
require_once __DIR__ . '/entity/Tarea.php';
require_once __DIR__ . '/entity/Usuario.php';

function conecta($host, $user, $pass, $db)
{
    $conexion = new mysqli($host, $user, $pass, $db);
    return $conexion;
}

function conectaTareas()
{
    $host = $_ENV['DATABASE_HOST'];
    $user = $_ENV['DATABASE_USER'];
    $pass = $_ENV['DATABASE_PASSWORD'];
    $name = $_ENV['DATABASE_NAME'];
    return conecta($host, $user, $pass, $name);
}

function cerrarConexion($conexion)
{
    if (isset($conexion) && $conexion->connect_errno === 0) {
        $conexion->close();
    }
}

function creaDB()
{
    try {
        $host = $_ENV['DATABASE_HOST'];
        $user = $_ENV['DATABASE_USER'];
        $pass = $_ENV['DATABASE_PASSWORD'];
        $conexion = conecta($host, $user, $pass, null);
        
        if ($conexion->connect_error)
        {
            return [false, $conexion->error];
        }
        else
        {
            // Verificar si la base de datos ya existe
            $sqlCheck = "SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = 'tareas'";
            $resultado = $conexion->query($sqlCheck);
            if ($resultado && $resultado->num_rows > 0) {
                return [false, 'La base de datos "tareas" ya existía.'];
            }

            $sql = 'CREATE DATABASE IF NOT EXISTS tareas';
            if ($conexion->query($sql))
            {
                return [true, 'Base de datos "tareas" creada correctamente'];
            }
            else
            {
                return [false, 'No se pudo crear la base de datos "tareas".'];
            }
        }
    }
    catch (mysqli_sql_exception $e)
    {
        return [false, $e->getMessage()];
    }
    finally
    {
        cerrarConexion($conexion);
    }
}

function createTablaUsuarios()
{
    try {
        $conexion = conectaTareas();
        
        if ($conexion->connect_error)
        {
            return [false, $conexion->error];
        }
        else
        {
            // Verificar si la tabla ya existe
            $sqlCheck = "SHOW TABLES LIKE 'usuarios'";
            $resultado = $conexion->query($sqlCheck);

            if ($resultado && $resultado->num_rows > 0)
            {
                return [false, 'La tabla "usuarios" ya existía.'];
            }

            $sql = 'CREATE TABLE `usuarios` (`id` INT NOT NULL AUTO_INCREMENT , `username` VARCHAR(50) NOT NULL , `rol` INT DEFAULT 0, `nombre` VARCHAR(50) NOT NULL , `apellidos` VARCHAR(100) NOT NULL , `contrasena` VARCHAR(255) NOT NULL , PRIMARY KEY (`id`)) ';
            if ($conexion->query($sql))
            {
                return [true, 'Tabla "usuarios" creada correctamente'];
            }
            else
            {
                return [false, 'No se pudo crear la tabla "usuarios".'];
            }
        }
    }
    catch (mysqli_sql_exception $e)
    {
        return [false, $e->getMessage()];
    }
    finally
    {
        cerrarConexion($conexion);
    }
}

function createTablaTareas()
{
    try {
        $conexion = conectaTareas();
        
        if ($conexion->connect_error)
        {
            return [false, $conexion->error];
        }
        else
        {
            // Verificar si la tabla ya existe
            $sqlCheck = "SHOW TABLES LIKE 'tareas'";
            $resultado = $conexion->query($sqlCheck);

            if ($resultado && $resultado->num_rows > 0)
            {
                return [false, 'La tabla "tareas" ya existía.'];
            }

            $sql = 'CREATE TABLE `tareas` (`id` INT NOT NULL AUTO_INCREMENT, `titulo` VARCHAR(50) NOT NULL, `descripcion` VARCHAR(250) NOT NULL, `estado` VARCHAR(50) NOT NULL, `id_usuario` INT NOT NULL, PRIMARY KEY (`id`), FOREIGN KEY (`id_usuario`) REFERENCES `usuarios`(`id`))';
            if ($conexion->query($sql))
            {
                return [true, 'Tabla "tareas" creada correctamente'];
            }
            else
            {
                return [false, 'No se pudo crear la tabla "tareas".'];
            }
        }
    }
    catch (mysqli_sql_exception $e)
    {
        return [false, $e->getMessage()];
    }
    finally
    {
        cerrarConexion($conexion);
    }
}

function createTablaFicheros()
{
    try {
        $conexion = conectaTareas();
        
        if ($conexion->connect_error)
        {
            return [false, $conexion->error];
        }
        else
        {
            // Verificar si la tabla ya existe
            $sqlCheck = "SHOW TABLES LIKE 'ficheros'";
            $resultado = $conexion->query($sqlCheck);

            if ($resultado && $resultado->num_rows > 0)
            {
                return [false, 'La tabla "ficheros" ya existía.'];
            }

            $sql = 'CREATE TABLE `ficheros` (`id` INT NOT NULL AUTO_INCREMENT, `nombre` VARCHAR(100) NOT NULL, `file` VARCHAR(250) NOT NULL, `descripcion` VARCHAR(250) NOT NULL, `id_tarea` INT NOT NULL, PRIMARY KEY (`id`), FOREIGN KEY (`id_tarea`) REFERENCES `tareas`(`id`))';
            if ($conexion->query($sql))
            {
                return [true, 'Tabla "ficheros" creada correctamente'];
            }
            else
            {
                return [false, 'No se pudo crear la tabla "ficheros".'];
            }
        }
    }
    catch (mysqli_sql_exception $e)
    {
        return [false, $e->getMessage()];
    }
    finally
    {
        cerrarConexion($conexion);
    }
}

// Función para recuperar todas las tareas de la BD, y almacenarlas en un array de objetos Tarea
function listaTareas()
{
    try {
        $conexion = conectaTareas();

        if ($conexion->connect_error)
        {
            return [false, $conexion->error];
        }
        else
        {
            // Consulta para recuperar todos los datos de todas las tareas
            $sql = "SELECT * FROM tareas";
            $resultados = $conexion->query($sql);
            $tareas = array();
            while ($row = $resultados->fetch_assoc())
            {
                // Crea un objeto usuario con la función buscaUsuarioMysqli, a partir del ID de usuario de la tarea, para luego usarlo en el constructor del objeto Tarea
                $usuario = buscaUsuarioMysqli($row['id_usuario']);
                // Crea el objeto tarea con el constructor, donde está incluido el objeto Usuario que hemos recuperado antes
                $tarea = new Tarea($row['titulo'], $row['descripcion'], $usuario, Estado::from($row['estado']));
                // Añade al objeto tarea el parámetro del ID de la propia tarea, ya que no está el ID en el constructor
                $tarea->setId($row['id']);
                // Añade la tarea al array de tareas
                array_push($tareas, $tarea);
            }
            return [true, $tareas]; // Devuelve true, y el array de tareas
        }
        
    }
    catch (mysqli_sql_exception $e) {
        return [false, $e->getMessage()];
    }
    finally
    {
        cerrarConexion($conexion);
    }
}

// Función para crear una nueva tarea en la BD, que recibe un objeto Tarea
function nuevaTarea($tarea)
{
    try {
        $conexion = conectaTareas();
        
        if ($conexion->connect_error)
        {
            return [false, $conexion->error];
        }
        else
        {
            // Consulta Insert, donde preparamos los futuros valores
            $stmt = $conexion->prepare("INSERT INTO tareas (titulo, descripcion, estado, id_usuario) VALUES (?,?,?,?)");
            // Recuperamos en variables los valores del objeto Tarea que recibimos
            $titulo = $tarea->getTitulo();
            $descripcion = $tarea->getDescripcion();
            $estado = $tarea->getEstado()->value;
            $usuario = $tarea->getUsuario()->getId();
            // Y los bindeamos para la consulta
            $stmt->bind_param("ssss", $titulo, $descripcion, $estado, $usuario);

            $stmt->execute();

            return [true, 'Tarea creada correctamente.']; // Devolverá true y un mensaje confirmando que todo ha ido bien
        }
    }
    catch (mysqli_sql_exception $e)
    {
        return [false, $e->getMessage()];
    }
    finally
    {
        cerrarConexion($conexion);
    }
}

// Función para actualizar la información de una tarea en la BD, que recibe un objeto Tarea
function actualizaTarea($tarea)
{
    try {
        $conexion = conectaTareas();
        
        if ($conexion->connect_error)
        {
            return [false, $conexion->error];
        }
        else
        {
            // Consulta para actualizar la tarea, donde preparamos los futuros valores
            $sql = "UPDATE tareas SET titulo = ?, descripcion = ?, estado = ?, id_usuario = ? WHERE id = ?";
            $stmt = $conexion->prepare($sql);
            // Recuperamos en variables los parámetros del objeto Tarea que hemos recibido, a través de los getters
            $titulo = $tarea->getTitulo();
            $descripcion = $tarea->getDescripcion();
            $estado = $tarea->getEstado()->value;
            $usuario = $tarea->getUsuario()->getId();
            $id = $tarea->getId();
            // Bindeamos los parámetros
            $stmt->bind_param("sssii", $titulo, $descripcion, $estado, $usuario, $id);

            $stmt->execute();

            return [true, 'Tarea actualizada correctamente.'];
        }
    }
    catch (mysqli_sql_exception $e)
    {
        return [false, $e->getMessage()];
    }
    finally
    {
        cerrarConexion($conexion);
    }
}

// Función para borrar una tarea de la BD, que recibe un ID
function borraTarea($id)
{
    try {
        $conexion = conectaTareas();

        if ($conexion->connect_error)
        {
            return [false, $conexion->error];
        }
        else
        {
            // Consulta para borrar la tarea cuyo id coincida con el recibido
            $sql = "DELETE FROM tareas WHERE id = " . $id;
            if ($conexion->query($sql))
            {
                return [true, 'Tarea borrada correctamente.'];
            }
            else
            {
                return [false, 'No se pudo borrar la tarea.'];
            }
            
        }
        
    }
    catch (mysqli_sql_exception $e) {
        return [false, $e->getMessage()];
    }
    finally
    {
        cerrarConexion($conexion);
    }
}

// Función para buscar una tarea en la BD cuyo ID coincida con el recibido, que devolverá un objeto Tarea
function buscaTarea($id)
{
    $conexion = conectaTareas();

    if ($conexion->connect_error)
    {
        return [false, $conexion->error];
    }
    else
    {
        $sql = "SELECT * FROM tareas WHERE id = " . $id;
        $resultados = $conexion->query($sql);
        if ($resultados->num_rows == 1) // Si hay solo un resultado de la consulta:
        {
            $row = $resultados->fetch_assoc();
            // Recuperamos un objeto Usuario a través de la funciṕn buscaUsuarioMysqli, del usuario cuyo id coincida con el del id de usuario de la tarea recogida en la consulta
            $usuario = buscaUsuarioMysqli($row['id_usuario']);
            // Creamos el objeto Tarea con los datos recibidos de la consulta, incluyendo al objeto Usuario que acabamos de crear
            $tarea = new Tarea($row['titulo'], $row['descripcion'], $usuario, Estado::from($row['estado']));
            // Setteamos el id con el setter, ya que no va en el constructor
            $tarea->setId($row['id']);
            return $tarea; // Devuelve el nuevo objeto tarea
        }
        else
        {
            return null;
        }
    }
}

// Función para comprobar si un usuario es dueño de una tarea, que recibe el id de un usuario y el de una tarea
function esPropietarioTarea($idUsuario, $idTarea)
{
    // Usa la función anterior, que devovlerá un objeto Tarea si existe una con ese ID
    $tarea = buscaTarea($idTarea);
    if ($tarea) // En caso de que exista, recupera el ID de usuario de esa tarea y lo compara con el recibido en la función. En caso de ser el mismo devolverá true
    {
        return $tarea->getUsuario()->getId() == $idUsuario;
    }
    else // En caso de no encontrar una tarea con ese ID devolverá false directamente
    {
        return false;
    }
}

// Función para buscar un usuario en la BD cuyo id coincida con el recibido, que devolverá un objeto Usuario
function buscaUsuarioMysqli($id)
{
    $conexion = conectaTareas();

    if ($conexion->connect_error)
    {
        return [false, $conexion->error];
    }
    else
    {
        // Consulta para el usuario cuyo id coincida con el recibido
        $sql = "SELECT id, username, nombre, apellidos, rol, contrasena  FROM usuarios WHERE id = " . $id;
        $resultados = $conexion->query($sql);
        if ($resultados->num_rows == 1) // En caso de recibir un solo resultado de la consulta:
        {
            $row = $resultados->fetch_assoc();
            // Crea un nuevo objeto usuario
            $usuario = new Usuario();
            // Y le añade los valores recibidos en la consulta, a los parámetros del objeto Usuario, a través de los setters
            $usuario->setId($row['id']);
            $usuario->setUsername($row['username']);
            $usuario->setNombre($row['nombre']);
            $usuario->setApellidos($row['apellidos']);
            $usuario->setRol(Rol::from($row['rol']));
            $usuario->setContrasena($row['contrasena']);
            return $usuario; // Devuelve el objeto usuario
        }
        else
        {
            return null;
        }
    }
}