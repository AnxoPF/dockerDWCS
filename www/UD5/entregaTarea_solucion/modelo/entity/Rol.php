<?php

enum Rol: int {
    case USER = 0;
    case ADMIN = 1;

    public function descripcion(): string {
        return match($this) {
            // En caso de ser admin, la función devolvera una cosa, y si es admin otra.
            Rol::USER => 'Usuario registrado',
            Rol::ADMIN => 'Administrador',
        };
    }
}
?>