<?php

interface FicherosDBInt
{
    public function listaFicheros(int $id_tarea): array;
    public function buscaFichero(int $id): ?Fichero;
    public function borraFichero(int $id): bool;
    public function nuevoFichero(Fichero $fichero): bool;
}