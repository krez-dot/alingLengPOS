<?php

// Reads Railway's MySQL plugin variables (MYSQLHOST, MYSQLPORT, ...) when present,
// falling back to local XAMPP defaults for development.
return [
    'host' => getenv('MYSQLHOST') ?: 'localhost',
    'port' => getenv('MYSQLPORT') ?: '3306',
    'database' => getenv('MYSQLDATABASE') ?: 'sarisari_pos',
    'username' => getenv('MYSQLUSER') ?: 'root',
    'password' => getenv('MYSQLPASSWORD') ?: '',
    'charset' => 'utf8mb4',
];
