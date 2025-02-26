<?php

class Fichero {
    private int $id;
    private string $nombre;
    private string $file;
    private string $descripcion;
    private int $id_tarea;

    public function __construct(int $id, string $nombre, string $file, string $descripcion, int $id_tarea) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->file = $file;
        $this->descripcion = $descripcion;
        $this->id_tarea = $id_tarea;
    }

    public function getId(): int {
        return $this->id;
    }

    public function setId(int $id): void {
        $this->id = $id;
    }

    public function getNombre(): string {
        return $this->nombre;
    }

    public function setNombre(string $nombre): void {
        $this->nombre = $nombre;
    }

    public function getFile(): string {
        return $this->file;
    }

    public function setFile(string $file): void {
        $this->file = $file;
    }

    public function getDescripcion(): string {
        return $this->descripcion;
    }

    public function setDescripcion(string $descripcion): void {
        $this->descripcion = $descripcion;
    }

    public function getIdTarea(): int {
        return $this->id_tarea;
    }

    public function setIdTarea(int $id_tarea): void {
        $this->id_tarea = $id_tarea;
    }
}
