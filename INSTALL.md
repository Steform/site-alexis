# Installation — Carrosserie Lino

## Étape 1 : Installer les dépendances

Ouvrez un terminal (CMD ou PowerShell) dans le dossier du projet et exécutez :

```bash
composer install
```

## Étape 2 : Configurer l'environnement

Créez un fichier `.env.local` (copie de `.env`) et modifiez si nécessaire :

- `APP_SECRET` : générez une clé aléatoire (ex. `openssl rand -hex 32`)
- `DATABASE_URL` : SQLite par défaut pour le dev

## Étape 3 : Lancer le serveur

```bash
php -S localhost:8000 -t public
```

Puis ouvrez http://localhost:8000 dans votre navigateur.

## Alternative : Symfony CLI

Si vous avez [Symfony CLI](https://symfony.com/download) installé :

```bash
symfony server:start
```
