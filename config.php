<?php
// PDO configuration. Default uses SQLite for easy testing.
// To switch to MySQL, comment the SQLite DSN and uncomment the MySQL block below.

// ---- SQLite (default) ----
// $dsn = 'sqlite:' . __DIR__ . '/database.sqlite';
// $user = null;
// $pass = null;

// config.php

$host = "localhost";       // ou o IP do servidor MySQL
$db   = "orcamentos";   // nome do banco criado no schema.sql
$user = "root";            // usuário do MySQL (ajuste se não for root)
$pass = "";                // senha do MySQL (coloque a sua se tiver)

try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão com o banco: " . $e->getMessage());
}


// Helpers globais

if (!function_exists('base_path')) {
    function base_path(string $path = ''): string
    {
        return rtrim(__DIR__, '/') . ($path ? '/' . ltrim($path, '/') : '');
    }
}

if (!function_exists('storage_path')) {
    function storage_path(string $path = ''): string
    {
        return base_path('storage' . ($path ? '/' . ltrim($path, '/') : ''));
    }
}

if (!function_exists('url')) {
    function url(string $path = ''): string
    {
        // Detecta protocolo (http ou https)
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";

        // Host atual
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';

        // Diretório raiz do projeto (pasta public normalmente)
        $root = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'])), '/');

        // Monta a URL final
        return $scheme . '://' . $host . ($root ? $root : '') . '/' . ltrim($path, '/');
    }
}
