<?php

// Recuerda descargar el framework Flight

require_once 'flight/Flight.php';

$host = $_ENV['DATABASE_HOST'];
$name = $_ENV['DATABASE_TAREA'];
$user = $_ENV['DATABASE_USER'];
$pass = $_ENV['DATABASE_PASSWORD'];

Flight::register('db', 'PDO', array("mysql:host=$host;dbname=$name", $user, $pass));

Flight::route('POST /register', function() {

    $nombre = Flight::request()->data->nombre;
    $email = Flight::request()->data->email;
    $password = password_hash(Flight::request()->data->password, PASSWORD_DEFAULT);
    
    $sql = 'INSERT INTO usuarios (nombre, email, password, token) VALUES (?, ?, ?, NULL)';

    $sentencia = Flight::db()->prepare($sql);

    $sentencia->bindParam(1, $nombre);
    $sentencia->bindParam(2, $email);
    $sentencia->bindParam(3, $password);

    try {
        $sentencia->execute();
        Flight::jsonp(['Usuario registrado correctamente.']);
    } catch (PDOException $e) {
        Flight::json(['error' => 'Error al registrar el usuario: ' . $e->getMessage()], 400);
    }
});

Flight::route('POST /login', function(){
    $email = Flight::request()->data->email;
    $password = Flight::request()->data->password;

    $sql = 'SELECT * FROM usuarios WHERE email = ?';

    $sentencia = Flight::db()->prepare($sql);
    $sentencia->bindParam(1, $email);
    $sentencia->execute();
    $user = $sentencia->fetch();

    if (!$user || !password_verify($password, $user['password'])) {
        Flight::json(['error' => 'Credenciales incorrectas.'], 400);
        return;
    }

    $token = bin2hex(random_bytes(32));
    $sql = 'UPDATE usuarios SET token = :token WHERE id = :id';
    $update = Flight::db()->prepare($sql);
    $update->bindParam(':token', $token);
    $update->bindParam(':id', $user['id']);
    $update->execute();

    Flight::response()->header('X-Token', $token);

    Flight::json(['token' => $token]);
});

Flight::route('GET /contactos(/@id)', function($id = null){
    $token = Flight::request()->getHeader('X-Token');

    if (!$token) {
        Flight::json(['error' => 'Token no proporcionado.'], 401);
        return;
    }
    
    $sql = 'SELECT * FROM usuarios WHERE token = ?';
    $sentencia = Flight::db()->prepare($sql);
    $sentencia->bindParam(1, $token);
    $sentencia->execute();
    $user = $sentencia->fetch();

    if (!$user) {
        Flight::json(['error' => 'Token inválido.'], 401);
        return;
    }

    if ($id) {
        $sql = 'SELECT * FROM contactos WHERE id = ? AND usuario_id = ?';
        $sentencia = Flight::db()->prepare($sql);
        $sentencia->bindParam(1, $id);
        $sentencia->bindParam(2, $user['id']);
        $sentencia->execute();
        $contacto = $sentencia->fetch();

        if (!$contacto) {
            Flight::json(['error' => 'Contacto no encontrado.'], 404);
            return;
        }

        Flight::json($contacto);
    } else {
        $sql = 'SELECT * FROM contactos WHERE usuario_id = ?';
        $sentencia = Flight::db()->prepare($sql);
        $sentencia->bindParam(1, $user['id']);
        $sentencia->execute();
        $contactos = $sentencia->fetchAll();

        Flight::json($contactos);
    }

});

Flight::route('POST /contactos', function() {
    $token = Flight::request()->getHeader('X-Token');
    
    if (!$token) {
        Flight::json(['error' => 'Token no proporcionado.'], 401);
        return;
    }

    $sql = 'SELECT * FROM usuarios WHERE token = ?';
    $sentencia = Flight::db()->prepare($sql);
    $sentencia->bindParam(1, $token);
    $sentencia->execute();
    $user = $sentencia->fetch();

    if (!$user) {
        Flight::json(['error' => 'Token inválido.'], 401);
        return;
    }

    $nombre = Flight::request()->data->nombre;
    $telefono = Flight::request()->data->telefono;
    $email = Flight::request()->data->email;

    $sql = 'INSERT INTO contactos (nombre, telefono, email, usuario_id) VALUES (?, ?, ?, ?)';
    $sentencia = Flight::db()->prepare($sql);
    $sentencia->bindParam(1, $nombre);
    $sentencia->bindParam(2, $telefono);
    $sentencia->bindParam(3, $email);
    $sentencia->bindParam(4, $user['id']);

    try {
        $sentencia->execute();
        Flight::json(['message' => 'Contacto añadido correctamente.']);
    } catch (PDOException $e) {
        Flight::json(['error' => 'Error al añadir el contacto: ' . $e->getMessage()], 400);
    }
});

Flight::route('PUT /contactos', function() {
    $token = Flight::request()->getHeader('X-Token');
    
    if (!$token) {
        Flight::json(['error' => 'Token no proporcionado.'], 401);
        return;
    }

    $sql = 'SELECT * FROM usuarios WHERE token = ?';
    $sentencia = Flight::db()->prepare($sql);
    $sentencia->bindParam(1, $token);
    $sentencia->execute();
    $user = $sentencia->fetch();

    if (!$user) {
        Flight::json(['error' => 'Token inválido.'], 401);
        return;
    }

    $contacto_id = Flight::request()->data->id;
    $nombre = Flight::request()->data->nombre;
    $telefono = Flight::request()->data->telefono;
    $email = Flight::request()->data->email;

    $sql = 'SELECT * FROM contactos WHERE id = ? AND usuario_id = ?';
    $sentencia = Flight::db()->prepare($sql);
    $sentencia->bindParam(1, $contacto_id);
    $sentencia->bindParam(2, $user['id']);
    $sentencia->execute();
    $contacto = $sentencia->fetch();

    if (!$contacto) {
        Flight::json(['error' => 'Contacto no encontrado o no autorizado.'], 403);
        return;
    }

    $sql = 'UPDATE contactos SET nombre = ?, telefono = ?, email = ? WHERE id = ?';
    $sentencia = Flight::db()->prepare($sql);
    $sentencia->bindParam(1, $nombre);
    $sentencia->bindParam(2, $telefono);
    $sentencia->bindParam(3, $email);
    $sentencia->bindParam(4, $contacto_id);

    try {
        $sentencia->execute();
        Flight::json(['message' => 'Contacto actualizado correctamente.']);
    } catch (PDOException $e) {
        Flight::json(['error' => 'Error al actualizar el contacto: ' . $e->getMessage()], 400);
    }
});

Flight::route('DELETE /contactos', function() {
    $token = Flight::request()->getHeader('X-Token');
    
    if (!$token) {
        Flight::json(['error' => 'Token no proporcionado.'], 401);
        return;
    }

    $sql = 'SELECT * FROM usuarios WHERE token = ?';
    $sentencia = Flight::db()->prepare($sql);
    $sentencia->bindParam(1, $token);
    $sentencia->execute();
    $user = $sentencia->fetch();

    if (!$user) {
        Flight::json(['error' => 'Token inválido.'], 401);
        return;
    }

    $id = Flight::request()->data->id;

    $sql = 'SELECT * FROM contactos WHERE id = ? AND usuario_id = ?';
    $sentencia = Flight::db()->prepare($sql);
    $sentencia->bindParam(1, $id);
    $sentencia->bindParam(2, $user['id']);

    $sentencia->execute();
    $contacto = $sentencia->fetch();

    if (!$contacto) {
        Flight::json(['error' => 'Contacto no encontrado o no autorizado.'], 403);
        return;
    }

    $sql = 'DELETE FROM contactos WHERE id = ?';
    $sentencia = Flight::db()->prepare($sql);
    $sentencia->bindParam(1, $id);

    try {
        $sentencia->execute();
        Flight::json(['message' => 'Contacto eliminado correctamente.']);
    } catch (PDOException $e) {
        Flight::json(['error' => 'Error al eliminar el contacto: ' . $e->getMessage()], 400);
    }
});

Flight::route('/', function () {
    echo 'Tarea UD6';
});

Flight::start();
