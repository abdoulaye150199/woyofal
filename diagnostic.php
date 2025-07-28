<?php

require_once __DIR__ . '/vendor/autoload.php';
require_once __DIR__ . '/app/config/env.php';

use DevNoKage\Database;

try {
    echo "⚙️ Configuration de la base de données:\n";
    echo "Host: " . DB_HOST . "\n";
    echo "Port: " . DB_PORT . "\n";
    echo "Database: " . DB_NAME . "\n";
    echo "Driver: " . DB_DRIVE . "\n\n";

    $db = Database::getInstance(
        DB_DRIVE,
        DB_HOST,
        DB_PORT,
        DB_NAME,
        DB_USER,
        DB_PASSWORD
    )->getConnexion();

    echo "✅ Connexion à la base de données réussie\n\n";

    // Vérifier les tables
    $tables = $db->query("SELECT table_name FROM information_schema.tables WHERE table_schema = 'public'")->fetchAll(PDO::FETCH_COLUMN);
    echo "📋 Tables existantes:\n";
    print_r($tables);

    if (empty($tables)) {
        echo "\n⚠️ Aucune table trouvée. Exécutez les migrations:\n";
        echo "php migration/migration.php\n";
    } else {
        // Vérifier les compteurs
        $compteurs = $db->query("SELECT * FROM compteurs LIMIT 5")->fetchAll(PDO::FETCH_ASSOC);
        echo "\n⚡ Exemple de compteurs:\n";
        print_r($compteurs);
    }

} catch (Exception $e) {
    echo "❌ Erreur: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}