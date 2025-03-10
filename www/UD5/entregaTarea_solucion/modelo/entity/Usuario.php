<?php

require_once __DIR__ . '/Rol.php';

class Usuario 
{
    private int $id;
    private String $username;
    private String $nombre;
    private String $apellidos;
    private String $contrasena;
    private Rol $rol;

    // Constructor de la clase Usuario. No se incluye el id en el constructor, y el rol por defecto es el de usuario
    public function __construct(String $username = '', String $nombre = '', String $apellidos = '', String $contrasena = '', Rol $rol = Rol::USER) 
    {
        $this->id = 0;
        $this->username = $username;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->contrasena = $contrasena;
        $this->rol = $rol;
    }

    // Getters y setters 
    public function getId(): int 
    {
        return $this->id;
    }

    public function setId(int $id): void 
    {
        $this->id = $id;
    }

    public function getUsername(): String 
    {
        return $this->username;
    }

    public function setUsername(String $username): void 
    {
        $this->username = $username;
    }

    public function getNombre(): String 
    {
        return $this->nombre;
    }

    public function setNombre(String $nombre): void 
    {
        $this->nombre = $nombre;
    }

    public function getApellidos(): String 
    {
        return $this->apellidos;
    }

    public function setApellidos(String $apellidos): void 
    {
        $this->apellidos = $apellidos;
    }

    public function getContrasena(): String 
    {
        return $this->contrasena;
    }

    public function setContrasena(String $contrasena): void 
    {
        $this->contrasena = $contrasena;
    }

    public function getRol(): Rol 
    {
        return $this->rol;
    }

    public function setRol(Rol $rol): void 
    {
        $this->rol = $rol;
    }

    // Función para validar los parámetros de un usuario, que se entregarán a través de un array
    public static function validate(array $data): array 
    {
        // Almacenará los mensajes de error en este array en función del parámetro que de error
        $errors = [];

        // Si el parámetro username del array está vacío, guardará un error
        if (empty($data['username'])) 
        {
            $errors['username'] = 'El nombre de usuario no puede estar vacío';
        } 
        elseif (strlen($data['username']) < 4) // Si el tamaño de username es menor que 4, error
        {
            $errors['username'] = 'El nombre de usuario debe tener al menos 3 caracteres';
        }

        if (empty($data['nombre']))  // Si el parámetro nombre está vacío
        {
            $errors['nombre'] = 'El nombre no puede estar vacío';
        } 
        elseif (strlen($data['nombre']) < 4) // SI el parámetro nombre tiene un tamaño menor que 4
        {
            $errors['nombre'] = 'El nombre debe tener al menos 2 caracteres';
        }

        if (empty($data['apellidos']))  // Si el parámetro apellidos está vacío
        {
            $errors['apellidos'] = 'Los apellidos no pueden estar vacíos';
        } 
        elseif (strlen($data['apellidos']) < 4) // Si el tamaño del parámetro apellidos es menor que 4
        {
            $errors['apellidos'] = 'Los apellidos deben tener al menos 2 caracteres';
        }

        if (empty($data['contrasena'])) // Si el parámetro contrasena está vacío
        {
            $errors['contrasena'] = 'La contraseña no puede estar vacía';
        } 
        elseif (strlen($data['contrasena']) < 6) // Si el parámetro contrasena tiene un tamaño menor que 6
        {
            $errors['contrasena'] = 'La contraseña debe tener al menos 6 caracteres';
        }

        $valores = array_map(fn($rol) => $rol->value, Rol::cases());
        if (!isset($data['rol']) || !in_array($data['rol'], $valores))
        {
            $errors['rol'] = 'El rol no es válido';
        }

        return $errors; // Devuelve el array errors
    }
    
    // Igual que la función anterior, pero elimina la parte de contrasena del array de errores.
    public static function validateWithoutPassword(array $data): array 
    {
        $errors = self::validate($data);
        unset($errors['contrasena']);
        return $errors;
    }
}
?>