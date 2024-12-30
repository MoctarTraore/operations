<?php

// $db_connection = 'mysql'; 
// $db_host = '10.0.16.12';
// $db_port = 3306;
// $db_name = 'gestion_operations';
// $db_username = 'innovdigit';
// $db_password = 'admin';

// Configuration de la connexion à la base de données
$host = '127.0.0.1';
$db = 'gestion_operations';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    $dsn = "mysql:host=$host;dbname=$db;charset=$charset";
    $pdo = new PDO($dsn, $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erreur de connexion à la base de données : " . $e->getMessage());
}

// try {
    
//     $dsn = "$db_connection:host=$db_host;port=$db_port;dbname=$db_name;charset=utf8";

    
//     $pdo = new PDO($dsn, $db_username, $db_password);

    
//     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
//     $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

    
// } catch (PDOException $e) {
    
//     die("Erreur de connexion à la base de données : " . $e->getMessage());
// }
