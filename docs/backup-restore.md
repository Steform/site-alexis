# Sauvegarde et restauration (back-office)

**Date :** 2026-03-22  
**Auteur :** Stephane H.

Ce document décrit le périmètre des archives ZIP générées depuis le back-office et la procédure de **reprise après sinistre** complète (hors seule restauration applicative).

## Contenu d’une archive (format v0.2+)

| Élément | Inclus |
|---------|--------|
| **Base de données** | Toutes les tables du schéma connecté (structure + données), fichier `database.sql` dans le ZIP. |
| **Fichiers utilisateur** | **Intégralité** de `public/uploads/` (récursif) : galeries, vignettes, hero, historique des fichiers remplacés sous `uploads/historique/`, etc. |

La **restauration** réinjecte le SQL puis **remplace tout le contenu** de `public/uploads/` par une copie miroir de l’arborescence `uploads/` extraite du ZIP (aligné sur la sauvegarde).

## Hors périmètre du ZIP (reprise « site complet »)

| Zone | Raison |
|------|--------|
| **Code source**, `vendor/`, `config/` | Reprise via dépôt Git + `composer install`. |
| **`public/build/`** (assets compilés) | Régénération (`npm` / Encore selon le projet). |
| **`public/images/`** (SVG statiques, etc.) | Généralement versionnés dans Git. |
| **`.env` / `.env.local`** | Secrets : **ne pas** les stocker dans un ZIP téléchargeable depuis le web ; sauvegarde côté serveur / coffre-fort. |
| **`var/cache`**, **`var/log`**, **`var/sessions`** | Régénérables ; exclus. |

## Procédure de reprise après sinistre (recommandée)

1. Déployer le code (checkout Git sur la branche / tag souhaité).
2. Installer les dépendances PHP : `composer install --no-dev` (ou avec dev selon environnement).
3. Reconstruire les assets front si nécessaire (ex. `npm ci && npm run build`).
4. Placer les fichiers d’**environnement** (`.env.local`) sur le serveur **sans** les commiter.
5. Importer la base et les fichiers soit via l’écran **Sauvegardes** du back-office (upload du ZIP), soit en extrayant manuellement le ZIP et en important `database.sql` + en copiant `uploads/` vers `public/uploads/`.
6. Vider le cache Symfony : `php bin/console cache:clear` (environnement adapté).

Les archives **v0.1** (restauration partielle de `uploads/`) restent exploitables : le ZIP contient déjà tout l’arbre `uploads/` ; avec l’application à jour (format **v0.2**), la restauration réécrit désormais **l’ensemble** de ce périmètre.

## Voir aussi

- Inventaire historisation : [`inventaire-historisation.md`](inventaire-historisation.md)
