<?php

class Fichero {
    private int $id;
    private string $nombre;
    private string $file;
    private string $descripcion;
    private Tarea $tarea;

    public const FORMATOS = ['pdf', 'jpeg', 'png'];
    public const MAX_SIZE = 20 * 1024 * 1024;

    public function __construct(int $id, string $nombre, string $file, string $descripcion, Tarea $tarea) {
        $this->id = $id;
        $this->nombre = $nombre;
        $this->file = $file;
        $this->descripcion = $descripcion;
        $this->tarea = $tarea;
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

    public function getTarea(): Tarea {
        return $this->tarea;
    }

    public function setTarea(Tarea $tarea): void {
        $this->tarea = $tarea;
    }

    public function getIdTarea(): int {
        return $this->getTarea()->getId();
    }

    public static function validarCampos(string $nombre, string $file, string $descripcion, Tarea $tarea): array {
        $errores = [];

        if (empty($nombre)) {
            $errores['nombre'] = 'El campo nombre es obligatorio.';
        }

        if ($_FILES['file']['error'] !== UPLOAD_ERR_OK) {
            $errores['file'] = 'Error al subir el archivo.';
        }

        $extension = strtolower(pathinfo($file, PATHINFO_EXTENSION));
        if (!in_array($extension, self::FORMATOS)) {
            $errores['file'] = 'Formato de archivo no permitido. Solo se aceptan: ' . implode(', ', self::FORMATOS) . '.';
        }

        if (isset($_FILES['file']) && $_FILES['file']['size'] > self::MAX_SIZE) {
            $errores['file'] = 'El archivo supera el tamaño máximo de 20 MB.';
        }

        $ruta = "../files/" . basename($file);
        if (file_exists($ruta)) {
            $errores['file'] = "El archivo ya existe.";
        }

        if (empty($descripcion)) {
            $errores['descripcion'] = 'El campo descripción es obligatorio';
        }

        if (!$tarea || !$tarea->getId()) {
            $errores['tarea'] = 'Debe asignarse una tarea válida.';
        }

        return $errores;
    }
}
