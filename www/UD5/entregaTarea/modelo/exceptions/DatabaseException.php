<?php

class DatabaseException extends Exception
{
    private string $method;
    private string $sql;

    public function __construct(string $mensaje, string $method, string $sql, int $codigo = 0, Exception $anterior = null)
    {
        parent::__construct($mensaje, $codigo, $anterior);
        $this->method = $method;
        $this->sql = $sql;
    }

    public function getMethod(): string
    {
        return $this->method;
    }

    public function getSql(): string
    {
        return $this->sql;
    }

    public function __toString(): string
    {
        return "DatabaseException: [{$this->codigo}]: {$this->mensaje} en método {$this->method}, SQL: {$this->sql}";
    }
}
