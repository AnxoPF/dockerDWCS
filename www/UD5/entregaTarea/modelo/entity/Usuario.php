<?php

class Usuario
{
    private String $id;
    private String $username;
    private String $nombre;
    private String $apellidos;
    private String $contrasena;
    private int $rol;

    public function __construct(int $id, String $nombre, String $apellidos, String $username, String $contrasena, $rol)
    {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->apellidos = $apellidos;
        $this->username = $username;
        $this->contrasena = $contrasena;
        $this->rol = $rol;
    }

    public function getId(): String
    {
        return $this->id;
    }

    public function setId(String $id): void
    {
        $this->id = $id;
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

    public function getUsername(): String
    {
        return $this->username;
    }

    public function setUsername(String $username): void
    {
        $this->username = $username;
    }

    public function getContrasena(): String
    {
        return $this->contrasena;
    }

    public function setContrasena(String $contrasena): void
    {
        $this->contrasena = $contrasena;
    }

    public function getRol(): int
    {
        return $this->rol;
    }

    public function setRol(int $rol): void
    {
        $this->rol = $rol;
    }

    public function __destruct()
    {
        // Destructor logic here if needed
    }
    
}