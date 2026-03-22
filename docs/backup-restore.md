# Sauvegarde et restauration (back-office)

**Date :** 2026-03-22  
**Dernière mise à jour :** 2026-03-22 (manifest `backup-manifest.json`)  
**Auteur :** Stephane H.

Ce document décrit le périmètre des archives ZIP générées depuis le back-office et la procédure de **reprise après sinistre** complète (hors seule restauration applicative).

## Contenu d’une archive (format v0.2+)

| Élément | Inclus |
|---------|--------|
| **`backup-manifest.json`** | Métadonnées JSON (format **0.2**) : `formatVersion`, `createdAt`, taille de `database.sql`, nombre de fichiers et octets sous `uploads/`. Présent sur les **nouvelles** sauvegardes créées avec l’application à jour. |
| **Base de données** | Toutes les tables du schéma connecté (structure + données), fichier `database.sql` dans le ZIP. |
| **Fichiers utilisateur** | **Intégralité** de `public/uploads/` (récursif) : galeries, vignettes, hero, historique des fichiers remplacés sous `uploads/historique/`, etc. |

La **restauration** réinjecte le SQL puis **remplace tout le contenu** de `public/uploads/` par une copie miroir de l’arborescence `uploads/` extraite du ZIP (aligné sur la sauvegarde).

### Manifeste et validation

- Si **`backup-manifest.json` est absent** (archives **0.2** créées avant l’introduction du manifeste, ou ZIP externes) : restauration **sans** contrôle strict du manifeste (comportement historique).
- Si le fichier est **présent** : le code vérifie que `formatVersion` est **supportée** (voir `BackupService::BACKUP_MANIFEST_SUPPORTED_FORMATS` / `isBackupFormatSupported()`), puis que les tailles et compteurs correspondent au contenu extrait (cohérence anti-fichier tronqué ou archive altérée).

### Rétrocompatibilité (baseline v0.2)

- Les archives **v0.2** **sans** manifeste restent restaurables.
- Les archives **v0.2** **avec** manifeste sont validées puis restaurées comme ci-dessus.
- Les archives **v0.1** (restauration partielle d’`uploads/` côté ancien code) : le ZIP contient en général déjà tout l’arbre `uploads/` ; avec l’application à jour, la restauration réécrit **l’ensemble** de `public/uploads/`.

### Évolution des versions (0.3+)

- Lors d’un changement de structure d’archive : incrémenter `BACKUP_ARCHIVE_FORMAT_VERSION`, enrichir le schéma du manifeste, ajouter la version à la liste des formats supportés et adapter la validation dans [`BackupService`](../src/Service/BackupService.php).
- Documenter ici tout changement incompatible ou toute procédure de migration manuelle.

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
