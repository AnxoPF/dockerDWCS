<?php

require_once __DIR__ . '/entity/Usuario.php';
require_once __DIR__ . '/entity/Tarea.php';
require_once __DIR__ . '/entity/Fichero.php';

// Función para conectarse a la BD
function conectaPDO()
{
    // Guarda en variables los datos del .env, a través de la variable de entorno $_ENV
    $servername = $_ENV['DATABASE_HOST'];
    $username = $_ENV['DATABASE_USER'];
    $password = $_ENV['DATABASE_PASSWORD'];
    $dbname = $_ENV['DATABASE_NAME'];

    // Usa esas mismas variables para establecer la conexión
    $conPDO = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $conPDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    return $conPDO;
}

// Función para recoger un array con los datos de todos los usuarios de la BD
function listaUsuarios()
{
    try {
        $con = conectaPDO(); // Prepara la conexión
        $stmt = $con->prepare('SELECT id, username, nombre, apellidos, rol, contrasena FROM usuarios'); // Prepara la consulta SELECT
        $stmt->execute(); // La ejecuta

        $result = $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $resultados = [];
        // Para cada fila del resultado:
        while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
            $usuario = new Usuario(); // Crea un nuevo objeto Usuario
            // Y le proporciona los valores recuperados en el SELECT al objeto usuario, a través de los setters
            $usuario->setId($row['id']);
            $usuario->setUsername($row['username']);
            $usuario->setNombre($row['nombre']);
            $usuario->setApellidos($row['apellidos']);
            $usuario->setRol(Rol::from($row['rol']));
            $usuario->setContrasena($row['contrasena']);
            $resultados[] = $usuario; // Guarda el usuario en un array de usuarios
        }
        return [true, $resultados];
    }
    catch (PDOException $e) {
        return [false, $e->getMessage()];
    }
    finally {
        $con = null;
    }
    
}

// Función para recoger un array con los datos de las tareas de la BD, pasandole un id de usuario y un estado
function listaTareasPDO($id_usuario, $estado)
{
    try {
        $con = conectaPDO();
        // Consulta las tareas donde el id de usuario coincida con el id que le hemos pasado a la función
        $sql = 'SELECT * FROM tareas WHERE id_usuario = ' . $id_usuario;
        if (isset($estado)) // En caso de recibir también un estado, amplía la consulta para que tenga que coincidir también dicho estado
        {
            $sql = $sql . " AND estado = '" . $estado->value . "'";
        }
        $stmt = $con->prepare($sql);
        $stmt->execute();

        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $tareas = array();
        // Para cada fila de los resultados de la consulta
        while ($row = $stmt->fetch()) {
            $usuario = buscaUsuario($row['id_usuario']); // Recoge el objeto usuario que coincida con el id
            // Crea un nuevo objeto tarea con los parametros que haya devuelto la consulta, incluyendo el usuario que acabamos de crear
            $tarea = new Tarea($row['titulo'], $row['descripcion'], $usuario, Estado::from($row['estado'])); 
            // Añade al objeto tarea su Id, el que hemos recogido de la propia consulta, a través de un setter ya que el id no figura en el constructor
            $tarea->setId($row['id']);
            
            $tareas[] = $tarea; // Guarda la tarea en un array de tareras
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

// Función para crear un nuevo usuario, añadirlo a la BD, que recoge un objeto usuario
function nuevoUsuario($usuario)
{
    try {
        $con = conectaPDO();
        // La consulta de inserción, donde preparamos los futuros valores
        $stmt = $con->prepare("INSERT INTO usuarios (nombre, apellidos, username, rol, contrasena) VALUES (:nombre, :apellidos, :username, :rol, :contrasena)");
        
        // Recogemos en variables los parámetros del objeto Usuario que nos han pasado en la función
        $nombre = $usuario->getNombre();
        $apellidos = $usuario->getApellidos();
        $username = $usuario->getUsername();
        $rol = $usuario->getRol()->value;
        // Encriptamos la contraseña, que en el objeto Usuario es texto plano.
        $hasheado = password_hash($usuario->getContrasena(), PASSWORD_DEFAULT);

        // Bindeamos todos los parametros para la consulta
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':rol', $rol);
        $stmt->bindParam(':contrasena', $hasheado);
        $stmt->execute();
        
        $stmt->closeCursor();

        return [true, null];
    } catch (PDOException $e) {
        return [false, $e->getMessage()];
    } finally {
        $con = null;
    }
}

// Función para actualizar la información de un usuario en la BD, que recibe un objeto Usuario
function actualizaUsuario($usuario)
{
    try
    {
        $con = conectaPDO();
        // Preparamos la consulta Update, con los futuros valores
        $sql = "UPDATE usuarios SET nombre = :nombre, apellidos = :apellidos, username = :username, rol = :rol";

        // Si el usuario que nos han pasado tiene el parámetro contraseña, la añadiremos a la consulta para actualizarla también
        if (!empty($usuario->getContrasena()))
        {
            $sql .= ', contrasena = :contrasena';
        }

        // Por último añadimos a la consulta la parte donde requerimos que haya coincidencia con un ID determinado, para saber qué usuario estamos actualizando.
        $sql .= ' WHERE id = :id';

        $stmt = $con->prepare($sql);

        // Recuperamos en variables los valores del objeto Usuario que nos han pasado
        $nombre = $usuario->getNombre();
        $apellidos = $usuario->getApellidos();
        $username = $usuario->getUsername();
        $rol = $usuario->getRol()->value;
        $id = $usuario->getId();

        // Y los bindeamos
        $stmt->bindParam(':nombre', $nombre);
        $stmt->bindParam(':apellidos', $apellidos);
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':rol', $rol);
        // En caso de haber contraseña, la hasheamos y bindeamos la nueva contraseña encriptada
        if (!empty($usuario->getContrasena()))
        {
            $hasheado = password_hash($usuario->getContrasena(), PASSWORD_DEFAULT);
            $stmt->bindParam(':contrasena', $hasheado);
        }
        $stmt->bindParam(':id', $id);

        $stmt->execute();
        
        $stmt->closeCursor();

        return [true, null]; // Devolverá true si no ha habido ningún error
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

// Función para borrar un usuario de la BD a partir de un ID
function borraUsuario($id)
{
    try {
        $con = conectaPDO();

        $con->beginTransaction();

        // Consulta para borrar primero todas las tareas cuyo id sea el del usuario que nos han pasado
        $stmt = $con->prepare('DELETE FROM tareas WHERE id_usuario = ' . $id);
        $stmt->execute();
        // Consulta para borrar al usuario cuyo id nos han pasado
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

// Función para buscar un Usuario de la BD que coincida con el ID que nos han pasado, y devovlerá un objeto Usuario
function buscaUsuario($id)
{
    try
    {
        $con = conectaPDO();
        // Consulta para recuperar todos los datos del usuario cuyo id coincida con el recibido
        $stmt = $con->prepare('SELECT id, username, nombre, apellidos, rol, contrasena FROM usuarios WHERE id = :id');
        $stmt->bindParam(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);
        $usuario = null;
        // Si hay resultado, crea un nuevo objeto Usuario y le otorga los valores recuperados de la BD al nuevo objeto Usuario, a través de los setters
        if ($row = $stmt->fetch()) {
            $usuario = new Usuario();
            $usuario->setId($row['id']);
            $usuario->setUsername($row['username']);
            $usuario->setNombre($row['nombre']);
            $usuario->setApellidos($row['apellidos']);
            $usuario->setRol(Rol::from($row['rol']));
            $usuario->setContrasena($row['contrasena']);
        }
        return $usuario; // Devuelve el objeto Usuario (que será null si no hay uno con el Id correspondiente)

        if ($stmt->rowCount() == 1)
        {
            return $stmt->fetch();
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

// Función para buscar un usuario por su username. Devolverá un objeto usuario
function buscaUsername($username)
{
    try
    {
        $con = conectaPDO();
        // Consulta para buscar al usuario cuyo username coincida con el recibido
        $stmt = $con->prepare('SELECT id, rol, contrasena FROM usuarios WHERE username = "' . $username . '"');
        $stmt->execute();
        $stmt->setFetchMode(PDO::FETCH_ASSOC);

        // Si hay 1 resultado, crea un nuevo objeto Usuario y le da los valores Id, Rol y Contraseña a través de los setters.
        if ($stmt->rowCount() == 1)
        {
            $usuario = null;
            if ($row = $stmt->fetch()) {
                $usuario = new Usuario();
                $usuario->setId($row['id']);
                $usuario->setRol(Rol::from($row['rol']));
                $usuario->setContrasena($row['contrasena']);
            }
            return $usuario;
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