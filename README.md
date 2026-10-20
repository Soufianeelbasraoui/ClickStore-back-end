# ClickStore — API Backend

API REST e-commerce construite avec Laravel 12 et PHP 8.2+. Elle fournit l'authentification, le catalogue de produits, les commandes et les fonctionnalités d'administration de ClickStore.

## Prérequis

- PHP 8.2 ou supérieur, avec les extensions requises par Laravel et MySQL)
- Composer
- Node.js et npm, uniquement pour compiler les ressources front-end

## Installation locale

Depuis la racine du projet :

```powershell
composer install
Copy-Item .env.example .env
php artisan key:generate
php artisan migrate --seed
```

La configuration d'exemple utilise SQLite. Le fichier `database/database.sqlite` est un fichier local généré et n'est pas destiné à être versionné. Pour utiliser MySQL, modifiez les variables `DB_*` dans `.env` avant les migrations.

Pour lancer l'API :

```powershell
php artisan serve --port=8000
```

L'API est alors disponible à `http://127.0.0.1:8000/api`.

Pour installer et compiler les ressources Vite du projet :

```powershell
npm install
npm run build
```

## Tests

```powershell
composer test
```

Les tests sont configurés pour utiliser une base SQLite en mémoire.

## API

### Authentification

- `POST /register` — créer un compte
- `POST /login` — se connecter et obtenir un jeton
- `POST /logout` — révoquer le jeton (authentification requise)
- `GET /user` — consulter le profil connecté (authentification requise)

### Produits et commandes

- `GET /products` — lister les produits, avec filtres possibles `category` et `search`
- `GET /products/{id}` — consulter un produit
- `GET /offers` — consulter les offres
- `POST /products`, `PUT /products/{id}`, `DELETE /products/{id}` — gérer les produits (authentification requise)
- `POST /orders` — créer une commande
- `GET /orders`, `GET /orders/{id}` — consulter ses commandes (authentification requise)

### Administration

Les routes d'administration sont sous `/admin` et nécessitent une authentification :

- `GET /admin/stats` — statistiques
- `GET /admin/users` — lister les utilisateurs
- `PUT /admin/users/{id}/role`, `DELETE /admin/users/{id}` — gérer les utilisateurs
- `GET /admin/orders` — lister les commandes
- `PUT /admin/orders/{id}/status` — modifier le statut d'une commande

Les routes API sont définies dans `routes/api.php`. Les requêtes protégées utilisent un jeton Sanctum Bearer.

## Données de démonstration

`php artisan migrate --seed` crée des produits, des commandes et des comptes de démonstration. Ces données et les identifiants définis par le seeder sont uniquement destinés au développement local : ne lancez pas ce seeder en production et ne réutilisez pas ces identifiants dans un environnement exposé.

## Publication sur GitHub

- Ne versionnez jamais `.env` : il contient la clé d'application et peut contenir des identifiants.
- Les dépendances (`vendor`, `node_modules`), les fichiers compilés et la base SQLite locale sont exclus par `.gitignore`.
- Conservez `.env.example` comme modèle sans secret.
- Choisissez et ajoutez une licence si vous souhaitez autoriser explicitement la réutilisation du code.
