<?php

namespace App\Shared\Database;

use PDO;

class Database
{
    public static function getConnection(): PDO {
        $host = 'localhost';
        $db = 'flyto';
        $user = 'root';
        $pass = '';
        $charset = 'utf8mb4';

        $dsn = "mysql:host=$host;dbname=$db;charset=$charset";

        $options = [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        ];

        //return new PDO($dsn, $user, $pass, $options);
        // Fuerza los parámetros limpios de XAMPP directamente en el constructor
return new PDO(
    "mysql:host=localhost;dbname=flyto;charset=utf8mb4", 
    "root", 
    "1234", 
    $options);
    }
}
