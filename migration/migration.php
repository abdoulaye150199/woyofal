<?php


require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/env.php';

use DevNoKage\Database;

class Migration {
    private \PDO $connection;

    public function __construct() {
        $this->connection = Database::getInstance(
            DB_DRIVE,
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_USER,
            DB_PASSWORD
        )->getConnexion();
    }

    public function migrate(): void {
        $this->createUuidExtension();
        $this->createFunctions();
        $this->createTables();
        $this->createIndexes();
        $this->createTriggers();
    }

    private function createUuidExtension(): void {
        $sql = "CREATE EXTENSION IF NOT EXISTS \"uuid-ossp\" WITH SCHEMA public;";
        $this->connection->exec($sql);
    }

    private function createFunctions(): void {
        // Fonction update_updated_at_column
        $this->connection->exec("
            CREATE OR REPLACE FUNCTION update_updated_at_column()
            RETURNS TRIGGER AS $$
            BEGIN
                NEW.updated_at = CURRENT_TIMESTAMP;
                RETURN NEW;
            END;
            $$ language 'plpgsql';
        ");

        // Fonction exists_code_recharge
        $this->connection->exec("
            CREATE OR REPLACE FUNCTION exists_code_recharge(code text) 
            RETURNS boolean AS $$
            BEGIN
                RETURN EXISTS (SELECT 1 FROM achats WHERE code_recharge = code);
            END;
            $$ LANGUAGE plpgsql;
        ");

        // Autres fonctions...
        // Ajoutez ici les autres fonctions du dump SQL
    }

    private function createTables(): void {
        // Table clients
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS clients (
                id SERIAL PRIMARY KEY,
                nom VARCHAR(100) NOT NULL,
                prenom VARCHAR(100) NOT NULL,
                telephone VARCHAR(20) NOT NULL,
                adresse TEXT NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ");

        // Table compteurs
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS compteurs (
                id SERIAL PRIMARY KEY,
                numero VARCHAR(50) NOT NULL UNIQUE,
                client_id INTEGER NOT NULL,
                actif BOOLEAN DEFAULT true,
                date_creation TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                FOREIGN KEY (client_id) REFERENCES clients(id) ON DELETE CASCADE
            );
        ");

        // Table tranches
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS tranches (
                id SERIAL PRIMARY KEY,
                nom VARCHAR(100) NOT NULL,
                min_montant NUMERIC(12,2) NOT NULL,
                max_montant NUMERIC(12,2),
                prix_kw NUMERIC(10,4) NOT NULL,
                ordre INTEGER NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                CONSTRAINT check_montant_coherent CHECK (min_montant <= COALESCE(max_montant, min_montant)),
                CONSTRAINT check_ordre_positif CHECK (ordre > 0),
                CONSTRAINT check_prix_positif CHECK (prix_kw > 0)
            );
        ");

        // Table achats
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS achats (
                id SERIAL PRIMARY KEY,
                reference VARCHAR(100) NOT NULL UNIQUE,
                code_recharge VARCHAR(255) NOT NULL UNIQUE,
                numero_compteur VARCHAR(100) NOT NULL,
                montant NUMERIC(10,2) NOT NULL,
                nbre_kwt NUMERIC(10,2) NOT NULL,
                tranche VARCHAR(50),
                prix_kw NUMERIC(10,2),
                client_nom VARCHAR(255),
                date_achat TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                statut VARCHAR(50) DEFAULT 'success'
            );
        ");

        // Table logs_achats
        $this->connection->exec("
            CREATE TABLE IF NOT EXISTS logs_achats (
                id SERIAL PRIMARY KEY,
                date_heure TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
                localisation VARCHAR(255),
                adresse_ip VARCHAR(45),
                statut VARCHAR(50) NOT NULL,
                numero_compteur VARCHAR(100),
                code_recharge VARCHAR(255),
                nbre_kwt NUMERIC(10,2),
                message_erreur TEXT,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
            );
        ");
    }

    private function createIndexes(): void {
        // Indexes pour la table achats
        $this->connection->exec("
            CREATE INDEX IF NOT EXISTS idx_achats_reference ON achats(reference);
            CREATE INDEX IF NOT EXISTS idx_achats_code_recharge ON achats(code_recharge);
            CREATE INDEX IF NOT EXISTS idx_achats_numero_compteur ON achats(numero_compteur);
            CREATE INDEX IF NOT EXISTS idx_achats_date_achat ON achats(date_achat);
            CREATE INDEX IF NOT EXISTS idx_achats_statut ON achats(statut);
        ");

        // Indexes pour la table compteurs
        $this->connection->exec("
            CREATE INDEX IF NOT EXISTS idx_compteurs_numero ON compteurs(numero);
            CREATE INDEX IF NOT EXISTS idx_compteurs_client ON compteurs(client_id);
            CREATE INDEX IF NOT EXISTS idx_compteurs_actif ON compteurs(actif);
        ");

        // Indexes pour la table logs_achats
        $this->connection->exec("
            CREATE INDEX IF NOT EXISTS idx_logs_achats_date_heure ON logs_achats(date_heure);
            CREATE INDEX IF NOT EXISTS idx_logs_achats_numero_compteur ON logs_achats(numero_compteur);
            CREATE INDEX IF NOT EXISTS idx_logs_achats_statut ON logs_achats(statut);
        ");

        // Index pour la table tranches
        $this->connection->exec("
            CREATE INDEX IF NOT EXISTS idx_tranches_ordre ON tranches(ordre);
        ");
    }

    private function createTriggers(): void {
        // Trigger pour clients
        $this->connection->exec("
            CREATE TRIGGER update_clients_updated_at 
            BEFORE UPDATE ON clients 
            FOR EACH ROW 
            EXECUTE FUNCTION update_updated_at_column();
        ");

        // Trigger pour compteurs
        $this->connection->exec("
            CREATE TRIGGER update_compteurs_updated_at 
            BEFORE UPDATE ON compteurs 
            FOR EACH ROW 
            EXECUTE FUNCTION update_updated_at_column();
        ");

        // Trigger pour tranches
        $this->connection->exec("
            CREATE TRIGGER update_tranches_updated_at 
            BEFORE UPDATE ON tranches 
            FOR EACH ROW 
            EXECUTE FUNCTION update_updated_at_column();
        ");
    }
}

// Exécuter la migration
$migration = new Migration();
$migration->migrate();

echo "Migration completed successfully!\n";