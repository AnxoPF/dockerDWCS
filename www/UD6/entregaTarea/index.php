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
    }

    $sql = 'SELECT * FROM usuarios WHERE token = ?';
    $sentencia = Flight::db()->prepare($sql);
    $sentencia->bindParam(1, $token);
    $sentencia->execute();
    $user = $sentencia->fetch();

    if (!$user) {
        Flight::json(['error' => 'Token inválido.'], 401);
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

Flight::route('DELETE /hoteles', function() {
    $id = Flight::request()->data->id;

    $sql = 'DELETE FROM hoteles WHERE id=:id';
    $sentencia = Flight::db()->prepare($sql);
    $sentencia->bindParam(':id', $id);

    $sentencia->execute();

    Flight::jsonp(["Hotel $id borrado correctamente"]);
});

Flight::route('PUT /hoteles', function() {
    $id = Flight::request()->data->id;
    $direccion = Flight::request()->data->direccion;
    $telefono = Flight::request()->data->telefono;
    $email = Flight::request()->data->email;

    $sql = "UPDATE hoteles SET direccion=?, telefono=?, email=? WHERE id=?";
    $sentencia = Flight::db()->prepare($sql);
    $sentencia->bindParam(1, $direccion);
    $sentencia->bindParam(2, $telefono);
    $sentencia->bindParam(3, $email);
    $sentencia->bindParam(4, $id);

    $sentencia->execute();

    Flight::jsonp(["Hotel $id actualizado correctamente"]);
});

Flight::route('GET /reservas(/@id)', function($id = null) {
    if ($id) {
        $sql = "SELECT r.*, c.nombre AS cliente, h.hotel AS hotel 
                FROM reservas r 
                JOIN clientes c ON r.id_cliente = c.id 
                JOIN hoteles h ON r.id_hotel = h.id 
                WHERE r.id = :id";
        $sentencia = Flight::db()->prepare($sql);
        $sentencia->bindParam(':id', $id);
        $sentencia->execute();
        $datos = $sentencia->fetch();
    } else {
        $sql = "SELECT r.*, c.nombre AS cliente, h.hotel AS hotel 
                FROM reservas r 
                JOIN clientes c ON r.id_cliente = c.id 
                JOIN hoteles h ON r.id_hotel = h.id";
        $sentencia = Flight::db()->prepare($sql);
        $sentencia->execute();
        $datos = $sentencia->fetchAll();
    }
    Flight::json($datos);
});

Flight::route('POST /reservas', function() {
    $id_cliente = Flight::request()->data->id_cliente;
    $id_hotel = Flight::request()->data->id_hotel;
    $fecha_reserva = Flight::request()->data->fecha_reserva;
    $fecha_entrada = Flight::request()->data->fecha_entrada;
    $fecha_salida = Flight::request()->data->fecha_salida;

    $checkHotel = Flight::db()->prepare('SELECT COUNT(*) FROM hoteles WHERE id = ?');
    $checkHotel->bindParam(1, $id_hotel);
    $checkHotel->execute();
    $hotelExists = $checkHotel->fetchColumn();

    if (!$hotelExists) {
        Flight::json(['error' => 'El hotel especificado no existe.'], 400);
        // Bad Request (400) -> de esta forma en lugar de devovler una respuesta en formato JSON correcta (200), devolvemos un error también formato JSON
        return;
    }

    $checkClient = Flight::db()->prepare('SELECT COUNT(*) FROM clientes WHERE id = ?');
    $checkClient->bindParam(1, $id_cliente);
    $checkClient->execute();
    $clientExists = $checkClient->fetchColumn();

    if (!$clientExists) {
        Flight::json(['error' => 'El cliente especificado no existe.'], 400);
        // Bad Request (400) -> de esta forma en lugar de devovler una respuesta en formato JSON correcta (200), devolvemos un error también formato JSON
        return;
    }

    $sql = 'INSERT INTO reservas(id_cliente, id_hotel, fecha_reserva, fecha_entrada, fecha_salida) VALUES (?, ?, ?, ?, ?)';
    $sentencia = Flight::db()->prepare($sql);
    $sentencia->bindParam(1, $id_cliente);
    $sentencia->bindParam(2, $id_hotel);
    $sentencia->bindParam(3, $fecha_reserva);
    $sentencia->bindParam(4, $fecha_entrada);
    $sentencia->bindParam(5, $fecha_salida);

    $sentencia->execute();

    Flight::jsonp(['Reserva guardada correctamente.']);
});

Flight::route('DELETE /reservas', function() {
    $id = Flight::request()->data->id;

    $sql = 'DELETE FROM reservas WHERE id=:id';
    $sentencia = Flight::db()->prepare($sql);
    $sentencia->bindParam(':id', $id);

    $sentencia->execute();

    Flight::jsonp(["Reserva $id borrada correctamente"]);
});

Flight::route('PUT /reservas', function() {
    $id = Flight::request()->data->id;
    $fecha_entrada = Flight::request()->data->fecha_entrada;
    $fecha_salida = Flight::request()->data->fecha_salida;

    $sql = "UPDATE reservas SET fecha_entrada=?, fecha_salida=? WHERE id=?";
    $sentencia = Flight::db()->prepare($sql);
    $sentencia->bindParam(1, $fecha_entrada);
    $sentencia->bindParam(2, $fecha_salida);
    $sentencia->bindParam(3, $id);

    $sentencia->execute();

    Flight::jsonp(["Reserva $id actualizada correctamente"]);
});

Flight::route('/', function () {
    echo 'API HOTELES';
});

Flight::start();
