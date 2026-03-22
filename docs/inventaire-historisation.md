# Site modifiable — inventaire de l’historisation

**Date de rédaction :** 2026-03-22  
**Dernière mise à jour :** 2026-03-22 (snapshots entité + rollback)  
**Auteur :** Stephane H.

Ce document répond à la question : *tout ce qui est modifiable sur le site est-il « historisé » ?*  
La timeline back ([`HistoryMergeService`](../src/Service/HistoryMergeService.php)) fusionne **quatre** sources :

| Mécanisme | Stockage | Rollback contenu |
|-----------|----------|------------------|
| **CMS** (`ContentBlockHistory`) | Snapshot bloc + locale avant écrasement | Oui (bouton sur la ligne) |
| **Entités** (`EntitySnapshotHistory`) | JSON complet de l’état avant/après action (`change_kind`: update / delete / create) | Oui ([`EntitySnapshotRollbackService`](../src/Service/EntitySnapshotRollbackService.php), bouton sur la ligne) |
| **Audit admin** (`AdminAuditLog`) | Code action + payload JSON | Non (traçabilité) |
| **Fichiers** (`UploadDeletionHistory`) | Archivage sous `public/uploads/historique/...` | Fichier récupérable si archivé ; sinon ligne `fileMissing` |

**Synthèse :** l’audit seul ne suffit pas pour revenir en arrière sur le texte ; utiliser **CMS** ou **EntitySnapshotHistory**.

---

## 1. Commandes de vérification (code)

```bash
rg "adminAuditLogger->log" src/
rg "pushToHistory|savePageContentForLocale" src/Service/ContentBlockManager.php
rg "archiveAndRecord|archiveReplacement" src/
rg "EntitySnapshotRecorder" src/Controller/Back/
```

---

## 2. Matrice par module back-office (après snapshots entité)

Légende : **CMS** · **Snap** = `entity_snapshot_history` · **Audit** · **Fichier** · **—**

| Module | Snap + rollback entité | Notes |
|--------|------------------------|-------|
| Horaires | Oui | `EntitySnapshotDomain::HORAIRES` |
| Coordonnées | Oui | Première création + mises à jour |
| Avis / Messages | Oui | CRUD |
| Utilisateurs | Oui | Hash mot de passe dans snapshot (rollback delete recrée le compte) |
| Types devis | Oui | |
| Cartes « pourquoi nous » / étapes processus | Oui | Via `ContentController` |
| Ligne `Service` (vignette / hero) | Oui | `recordCurrentStateForPendingUpdate` avant changement d’image |
| Sliders About / Hero (photos + reorder) | Oui | Reorder = un snapshot par photo avant changement de position |
| Galerie | Oui | CRUD + images archivées comme avant |
| CMS blocs | Oui (CMS) | Inchangé |
| Cartes accueil (blocs `services.cardN.image`) | CMS + fichier | Inchangé |
| Maintenance / sauvegardes | Audit seulement | Pas de snapshot entité métier |
| Dashboard | — | Lecture seule |

---

## 3. Filtres timeline

- **Type** : `cms_block`, `upload`, `audit`, `entity_snapshot`
- **Domaine entité** : préfixe `entity:` + clé domaine ([`EntitySnapshotDomain`](../src/Service/EntitySnapshotDomain.php))

---

## 4. Hors périmètre « historique site »

| Zone | Raison |
|------|--------|
| Formulaires **contact** / **devis** publics | Pas de persistance BDD |
| **Téléchargement** sauvegarde | Lecture seule |
| **Setup** initial | Hors back-office courant |

---

## 5. Contrôle manuel (`/back/content/history`)

1. Vérifier les **quatre** types dans les filtres.
2. Modifier un horaire → ligne **entity_snapshot** + bouton **Restaurer l’état**.
3. Rollback : l’état précédent doit réapparaître dans le formulaire concerné.

---

## 6. SQL optionnel

```sql
SELECT COUNT(*) FROM content_block_history;
SELECT COUNT(*) FROM entity_snapshot_history;
SELECT COUNT(*) FROM admin_audit_log;
SELECT COUNT(*) FROM upload_deletion_history;
```

---

## 7. Sauvegardes ZIP (back-office)

Périmètre des archives (BDD + `public/uploads/`), reprise après sinistre complète : voir [`backup-restore.md`](backup-restore.md).

---

## 8. Pistes restantes

- `gallery.item.delete` dans l’audit uniquement (optionnel ; la suppression est déjà couverte par snapshot + archive fichier).
- Chaîne de rollbacks multiples (plusieurs restaurations successives) : dernier snapshot gagne ; documenter pour les éditeurs.
