<?php
// load.php
require __DIR__.'/vendor/autoload.php';

use Symfony\Component\Dotenv\Dotenv;
use Doctrine\DBAL\DriverManager;
use Symfony\Component\PasswordHasher\Hasher\NativePasswordHasher;

// Charger .env
if (file_exists(__DIR__.'/.env')) {
    $dotenv = new Dotenv();
    $dotenv->load(__DIR__.'/.env');
}

// Vérifie DATABASE_URL
if (!isset($_ENV['DATABASE_URL'])) {
    die("Erreur : DATABASE_URL introuvable dans .env\n");
}

// Parse DATABASE_URL
$dsn = $_ENV['DATABASE_URL'];
$parsed = parse_url($dsn);

$driver = $parsed['scheme'] === 'mysql' ? 'pdo_mysql' : 'pdo_pgsql';
$host = $parsed['host'] ?? '127.0.0.1';
$port = $parsed['port'] ?? null;
$user = $parsed['user'] ?? '';
$pass = $parsed['pass'] ?? '';
$dbname = ltrim($parsed['path'], '/');

try {
    // Connexion DB
    $conn = DriverManager::getConnection([
        'driver' => $driver,
        'host' => $host,
        'port' => $port,
        'user' => $user,
        'password' => $pass,
        'dbname' => $dbname,
        'charset' => 'utf8mb4',
    ]);

    echo "Connexion réussie !\n";
} catch (\Exception $e) {
    die("Erreur de connexion : " . $e->getMessage() . "\n");
}

// Données admin
$email = 'admin@example.com';
$roles = json_encode(['ROLE_ADMIN']);
$nom = 'Pascal Ilunga';

// Hash du mot de passe
$hasher = new NativePasswordHasher();
$password = $hasher->hash('password123');
// Insertion
try {
    $conn->executeStatement(
        'INSERT INTO user (email, roles, password, nom) VALUES (?, ?, ?, ?)',
        [$email, $roles, $password, $nom]
    );
    echo "Admin inséré avec succès !\n";
} catch (\Exception $e) {
    die("Erreur lors de l'insertion : " . $e->getMessage() . "\n");
}