# Dev No Kage - Project Initializer

**Un starter kit PHP moderne** avec une architecture MVC minimaliste, une injection de dépendances simple via YAML, et des composants essentiels pour lancer rapidement vos projets PHP.

## 🚀 Fonctionnalités

- 🔧 Architecture **MVC** minimaliste (`app/controller`, `app/model`, `app/view`)
- ⚙️ **Injection de dépendances** basée sur YAML (`app/config/services.yaml`)
- 🔐 Chargement de configuration via **DotEnv**
- 🧩 Autoload PSR-4 conforme via Composer
- 🧪 Prêt pour les tests unitaires avec **PHPUnit**
- 🗂️ Structure claire du projet (`app/`, `public/`, `core/`)

---

## 📁 Structure du projet

project-root/
├── app/
│ ├── config/ # fichiers YAML, .env, helpers
│ ├── controller/ # contrôleurs
│ ├── model/ # modèles
│ ├── view/ # vues
│ └── core/ # cœur du framework
│ ├── abstract/
│ ├── enums/
│ └── interface/
├── public/ # point d'entrée (index.php)
├── composer.json
└── README.md


---

## ⚙️ Installation

1. **Cloner le dépôt :**
   ```bash
   git clone https://github.com/dev-no-kage/project-initializer.git

   composer create-project dev-no-kage/project-initializer nom-du-projet

   cd project-initializer
2. Installer les dépendances :
     composer install
3. Créer votre fichier .env : 
    cp app/config/.env.example .env
4. Démarrer un serveur local :
    php -S localhost:8000 -t public

🧠 Comment ça marche ?
🔁 Injection de dépendance
Définissez vos services dans app/config/services.yaml :

MaClasse:
  class: App\Service\MaClasse
  argument:
    - App\Repository\AutreClasse
⚙️ Système d’injection de dépendances
Le projet utilise un système simple mais puissant d’injection de dépendances, basé sur deux piliers :

1. services.yml (dans app/config)
C’est ici que tu déclares tous les services (classes) nécessaires à l’application, avec leurs dépendances.

🧱 Structure d’un service
yaml
Copy
Edit
CLE_UNIQUE:
  class: Namespace\De\La\Classe
  argument: [Liste, Des, Dépendances]
CLE_UNIQUE : identifiant du service (doit exister dans l’énum ClassName de app/core)

class : nom complet de la classe avec son namespace

argument : liste des arguments nécessaires au constructeur

2. Syntaxe des arguments dans argument
Les arguments peuvent être de différents types :

| Type                      | Syntaxe YAML          | Interprétation                        |
| ------------------------- | --------------------- | ------------------------------------- |
| Service (classe)          | `- "@NOM_DU_SERVICE"` | Injection d’une autre classe déclarée |
| Variable d’environnement  | `- "%NOM_DE_LA_VAR%"` | declaré comme constance dans env.php |
| Valeur simple (texte/num) | `- "valeur"`          | Transmise telle quelle                |


🔁 Exemples pratiques
✅ Injection sans dépendance

DATABASE:
  class: DevNoKage\Core\Database
  argument: []
✅ Injection d’un service

AUTH:
  class: DevNoKage\Core\ClassAuth
  argument:
    - "@DATABASE"


✅ Injection de valeurs littérales

DATABASE:
  class: DevNoKage\Core\Database
  argument:
    - "driver"
    - "localhost"
    - "5432"
    - "dbname"


✅ Injection de variables d’environnement

DATABASE:
  class: DevNoKage\Core\Database
  argument:
    - "%DB_DRIVER%"
    - "%DB_HOST%"
    - "%DB_PORT%"
    - "%DB_NAME%"
📂 Fichier concerné : services.yml
Déclare toutes tes classes et dépendances dans app/config/services.yml.