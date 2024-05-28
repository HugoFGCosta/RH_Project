<?php


if (!function_exists('translateRole')) {
    function translateRole($role)
    {
        $translations = [
            'Manager' => 'Gestor',
            'Administrator' => 'Administrador',
            'Worker' => 'Utilizador',
        ];

        return $translations[$role] ?? $role; // Return the translated role or the original if not found
    }
}
