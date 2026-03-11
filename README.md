# Carrosserie Lino - Site vitrine

Site internet pour **Carrosserie Lino** à Baldersheim. Alexis Haffner, propriétaire depuis 2022.

## Prérequis

- PHP 8.2+
- Composer
- SQLite (dev) ou MySQL/PostgreSQL (prod)

## Installation

```bash
composer install
```

## Configuration

Copier `.env` vers `.env.local` et adapter :

- `APP_SECRET` : clé secrète
- `DATABASE_URL` : connexion BDD (SQLite par défaut en dev)

## Lancer le serveur

```bash
php -S localhost:8000 -t public
```

Ou avec Symfony CLI :

```bash
symfony server:start
```

## Structure

- `src/Controller/Public/` — Controllers site public
- `src/Controller/Back/` — Controllers back-office
- `templates/public/` — Templates site public
- `templates/back/` — Templates back-office
- `Maquette/` — Maquette et assets design

## Documentation planning

- `_bmad-output/planning-artifacts/prd.md` — PRD
- `_bmad-output/planning-artifacts/architecture.md` — Architecture
- `_bmad-output/planning-artifacts/epics.md` — Epics & Stories
