<?php

require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/../app/config/env.php';

use DevNoKage\Database;

class DatabaseSeeder {
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

    public function seed(): void {
        $this->seedClients();
        $this->seedCompteurs();
        $this->seedTranches();
        $this->seedAchats();      // Ajout de la nouvelle méthode
        $this->seedLogsAchats();  // Ajout de la nouvelle méthode
    }

    private function seedClients(): void {
        $clients = [
            ['Diallo', 'Abdoulaye', '+221771234567', 'Dakar, Sénégal'],
            ['DIOP', 'Fatou', '+221772345678', 'Thiès, Sénégal'],
            ['FALL', 'Moussa', '+221773456789', 'Saint-Louis, Sénégal'],
            ['BA', 'Aminata', '+221774567890', 'Kaolack, Sénégal'],
            ['SARR', 'Ibrahima', '+221775678901', 'Ziguinchor, Sénégal'],
            ['SOW', 'Ousmane', '+221776789012', 'Dakar, Sénégal'],
            ['KANE', 'Khady', '+221777890123', 'Rufisque, Sénégal'],
            ['DIALLO', 'Mamadou', '+221778901234', 'Kolda, Sénégal']
        ];

        $stmt = $this->connection->prepare("
            INSERT INTO clients (nom, prenom, telephone, adresse) 
            VALUES (:nom, :prenom, :telephone, :adresse)
        ");

        foreach ($clients as $client) {
            $stmt->execute([
                ':nom' => $client[0],
                ':prenom' => $client[1],
                ':telephone' => $client[2],
                ':adresse' => $client[3]
            ]);
        }
    }

    private function seedCompteurs(): void {
        $stmt = $this->connection->prepare("
            INSERT INTO compteurs (numero, client_id, actif) 
            VALUES (:numero, :client_id, :actif)
        ");

        $clientsIds = $this->connection->query("SELECT id FROM clients")->fetchAll(\PDO::FETCH_COLUMN);
        
        foreach ($clientsIds as $clientId) {
            $stmt->execute([
                ':numero' => 'CPT' . str_pad(rand(100000, 999999), 6, '0', STR_PAD_LEFT),
                ':client_id' => $clientId,
                ':actif' => true
            ]);
        }
    }

    private function seedTranches(): void {
        $tranches = [
            ['Tranche 1', 0, 5000, 98, 1],
            ['Tranche 2', 5001, 15000, 105, 2],
            ['Tranche 3', 15001, 30000, 115, 3],
            ['Tranche 4', 30001, null, 125, 4]
        ];

        $stmt = $this->connection->prepare("
            INSERT INTO tranches (nom, min_montant, max_montant, prix_kw, ordre) 
            VALUES (:nom, :min_montant, :max_montant, :prix_kw, :ordre)
        ");

        foreach ($tranches as $tranche) {
            $stmt->execute([
                ':nom' => $tranche[0],
                ':min_montant' => $tranche[1],
                ':max_montant' => $tranche[2],
                ':prix_kw' => $tranche[3],
                ':ordre' => $tranche[4]
            ]);
        }
    }

    private function seedAchats(): void {
        $compteurs = $this->connection->query("SELECT numero FROM compteurs")->fetchAll(\PDO::FETCH_COLUMN);
        $clients = $this->connection->query("SELECT CONCAT(prenom, ' ', nom) as nom_complet FROM clients")->fetchAll(\PDO::FETCH_COLUMN);
        
        $stmt = $this->connection->prepare("
            INSERT INTO achats (
                reference, 
                code_recharge, 
                numero_compteur, 
                montant, 
                nbre_kwt, 
                tranche, 
                prix_kw,
                client_nom,
                date_achat,
                statut
            ) VALUES (
                :reference,
                :code_recharge,
                :numero_compteur,
                :montant,
                :nbre_kwt,
                :tranche,
                :prix_kw,
                :client_nom,
                :date_achat,
                :statut
            )
        ");

        foreach ($compteurs as $index => $compteur) {
            $date = new DateTime();
            $date->modify("-{$index} days");
            
            $montant = rand(1000, 50000);
            $nbre_kwt = $montant / 100;
            $prix_kw = 100;
            
            $stmt->execute([
                ':reference' => 'WOY-' . date('Ymd') . '-' . str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT),
                ':code_recharge' => implode('-', array_map(fn() => str_pad(rand(1000, 9999), 4, '0', STR_PAD_LEFT), range(1, 4))),
                ':numero_compteur' => $compteur,
                ':montant' => $montant,
                ':nbre_kwt' => $nbre_kwt,
                ':tranche' => 'Tranche ' . rand(1, 4),
                ':prix_kw' => $prix_kw,
                ':client_nom' => $clients[$index % count($clients)],
                ':date_achat' => $date->format('Y-m-d H:i:s'),
                ':statut' => 'success'
            ]);
        }
    }

    private function seedLogsAchats(): void {
        $achats = $this->connection->query("
            SELECT code_recharge, numero_compteur, nbre_kwt 
            FROM achats
        ")->fetchAll();

        $stmt = $this->connection->prepare("
            INSERT INTO logs_achats (
                date_heure,
                localisation,
                adresse_ip,
                statut,
                numero_compteur,
                code_recharge,
                nbre_kwt,
                message_erreur
            ) VALUES (
                :date_heure,
                :localisation,
                :adresse_ip,
                :statut,
                :numero_compteur,
                :code_recharge,
                :nbre_kwt,
                :message_erreur
            )
        ");

        foreach ($achats as $index => $achat) {
            $date = new DateTime();
            $date->modify("-{$index} hours");

            $stmt->execute([
                ':date_heure' => $date->format('Y-m-d H:i:s'),
                ':localisation' => 'Dakar, Sénégal',
                ':adresse_ip' => '127.0.0.1',
                ':statut' => 'Success',
                ':numero_compteur' => $achat['numero_compteur'],
                ':code_recharge' => $achat['code_recharge'],
                ':nbre_kwt' => $achat['nbre_kwt'],
                ':message_erreur' => null
            ]);
        }
    }
}

// Exécuter le seeding
$seeder = new DatabaseSeeder();
$seeder->seed();

echo "Seeding completed successfully!\n";