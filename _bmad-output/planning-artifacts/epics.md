---
stepsCompleted: [1, 2]
inputDocuments:
  - "prd.md"
  - "architecture.md"
  - "ux-design-specification.md"
---

# site-alexis - Epic Breakdown

## Overview

This document provides the complete epic and story breakdown for site-alexis, decomposing the requirements from the PRD, UX Design, and Architecture into implementable stories.

## Requirements Inventory

### Functional Requirements

FR1: Site public — pages selon maquette (index prioritaire), structure barre contact, hero, cartes services, Qui sommes-nous, témoignage, footer
FR2: Horaires dynamiques — affichage des horaires gérés par Alexis
FR3: Avis clients — affichage des avis (texte, auteur, note, date) saisis manuellement dans le back-office
FR4: Formulaire « Demander un devis » — champs véhicule, type prestation, message, coordonnées ; envoi par email à Alexis/garage
FR5: Messages à date bornée — Alexis peut en créer autant qu'il veut (souvent 0–2). Chaque message : contenu (ex. « Le garage sera fermé du 9 au 13 décembre »), date début d'affichage, date fin d'affichage. Affichage en rouge ; emplacement à définir.
FR6: Identité Alexis — nom, photo, texte, palmarès (3ᵉ mondial 2018) visible sur le site
FR7: Contact visible — téléphone, CTA devis
FR8: Back-office Alexis — authentification ; gestion horaires, avis, messages à date bornée (liste illimitée ; contenu, date début, date fin)
FR9: Back-office Admin — authentification ; gestion comptes ; supervision contenu et devis
FR10: i18n — structure multilingue (Symfony i18n, locales fr/de) ; contenu traduisible dès le MVP

### NonFunctional Requirements

NFR1: Symfony 8+ — code structuré, maintenable
NFR2: Sécurité — authentification (admin + Alexis), formulaire devis protégé (anti-spam, validation)
NFR3: Disponibilité — envoi d'email fiable pour le devis
NFR4: Affichage des messages — selon règles dates début/fin
NFR5: Responsive — mobile-first, Bootstrap breakpoints
NFR6: Accessibilité — WCAG AA

### Additional Requirements

**From Architecture:**
- Starter: `symfony new site-alexis --version="8.*" --webapp` — première story
- Doctrine, entités User, Horaires, Avis, Message (liste ; contenu, date_debut, date_fin ; affichage en rouge, emplacement à définir)
- Symfony Security, rôles ROLE_ADMIN, ROLE_ALEXIS
- Symfony Mailer pour formulaire devis
- Twig, Bootstrap, assets custom (thème maquette)
- Dark mode prévu (post-MVP ou dès MVP)

**From UX:**
- Bootstrap + fidélité maquette
- Tokens couleur extraits (#f5f5fa, etc.)
- Structure index : barre contact, hero, cartes, Qui sommes-nous, grille services, témoignage, footer
- Plan prévisionnel pages (Galerie, Avis, Services, etc.) — post-index

### FR Coverage Map

| FR | Epic(s) |
|----|---------|
| FR1 (Site public) | E5, E8 |
| FR2 (Horaires dynamiques) | E4, E5 |
| FR3 (Avis clients) | E4, E5 |
| FR4 (Formulaire devis) | E6 |
| FR5 (Messages à date bornée) | E4, E5 |
| FR6 (Identité Alexis) | E5 |
| FR7 (Contact visible) | E5, E8 |
| FR8 (Back-office Alexis) | E3, E4 |
| FR9 (Back-office Admin) | E3, E4 |
| FR10 (i18n) | E7 |

## Epic List

### Epic 1: Foundation — Symfony & Bootstrap

**Objectif :** Projet Symfony 8 initialisé avec Bootstrap et structure Twig.

| ID | Story | Critères d'acceptation |
|----|-------|------------------------|
| 1.1 | Initialiser le projet Symfony 8 | `symfony new site-alexis --version="8.*" --webapp` ; structure src/, templates/, config/ ; serveur dev fonctionnel |
| 1.2 | Intégrer Bootstrap et base Twig | Bootstrap (composer ou CDN) ; base.html.twig ; variables CSS alignées maquette (#f5f5fa, etc.) ; structure templates/public/, templates/back/ |

---

### Epic 2: Data Layer — Entités & Migrations

**Objectif :** Entités Doctrine (User, Horaires, Avis, Message) et migrations.

| ID | Story | Critères d'acceptation |
|----|-------|------------------------|
| 2.1 | Entité User | Champs email, password, roles ; rôles ROLE_ADMIN, ROLE_ALEXIS |
| 2.2 | Entité Horaires | Champs selon modèle (jours, heures ouverture/fermeture) ; table `horaires` |
| 2.3 | Entité Avis | Champs texte, auteur, note, date ; table `avis` |
| 2.4 | Entité Message | Champs contenu, date_debut, date_fin ; table `messages` ; liste illimitée |
| 2.5 | Migrations Doctrine | Migrations générées et exécutables ; SQLite dev, MySQL/PostgreSQL prod |

---

### Epic 3: Authentication & Security

**Objectif :** Authentification Symfony, rôles, protection back-office.

| ID | Story | Critères d'acceptation |
|----|-------|------------------------|
| 3.1 | Symfony Security | Authentification configurée ; formulaire login |
| 3.2 | Rôles et utilisateurs | ROLE_ADMIN, ROLE_ALEXIS ; création compte admin et Alexis |
| 3.3 | Firewall back-office | Routes /back/* protégées ; redirection si non authentifié |

---

### Epic 4: Back-office CRUD

**Objectif :** Gestion horaires, avis et messages à date bornée par Alexis.

| ID | Story | Critères d'acceptation |
|----|-------|------------------------|
| 4.1 | CRUD Horaires | Liste, création, modification, suppression ; accessible à Alexis |
| 4.2 | CRUD Avis | Liste, création, modification, suppression ; champs texte, auteur, note, date |
| 4.3 | CRUD Messages à date bornée | Liste illimitée ; contenu, date début, date fin ; accessible à Alexis |

---

### Epic 5: Site Public — Pages & Contenu

**Objectif :** Pages selon maquette, horaires, avis, messages, identité Alexis.

| ID | Story | Critères d'acceptation |
|----|-------|------------------------|
| 5.1 | Page index (maquette) | Barre contact, hero, cartes services, Qui sommes-nous, grille services, témoignage, footer ; fidélité maquette |
| 5.2 | Horaires dynamiques | Affichage horaires depuis BDD ; section dédiée |
| 5.3 | Avis clients | Affichage avis (texte, auteur, note, date) ; section ou bloc |
| 5.4 | Messages à date bornée | Affichage conditionnel selon date_debut/date_fin ; style rouge ; emplacement à définir (bannière ou encart) |
| 5.5 | Identité Alexis | Nom, photo, texte, palmarès (3ᵉ mondial 2018) ; section Qui sommes-nous ou dédiée |
| 5.6 | Contact visible | Téléphone, CTA « Demander un devis » ; barre contact et hero |

---

### Epic 6: Formulaire Devis & Email

**Objectif :** Formulaire devis fonctionnel avec envoi email.

| ID | Story | Critères d'acceptation |
|----|-------|------------------------|
| 6.1 | Formulaire devis | Champs véhicule, type prestation, message, coordonnées ; validation ; anti-spam (honeypot ou CAPTCHA léger) |
| 6.2 | Envoi email | Symfony Mailer ; email à Alexis/garage ; configuration SMTP ou service |

---

### Epic 7: i18n — Multilingue

**Objectif :** Structure multilingue (FR, DE) dès le MVP.

| ID | Story | Critères d'acceptation |
|----|-------|------------------------|
| 7.1 | Symfony Translation | Locales fr, de ; structure translations/ ; clés i18n |
| 7.2 | Contenu traduisible | Contenus éditables traduits ; traduction dans la foulée à chaque création/mise à jour |

---

### Epic 8: Polish — Responsive & Accessibilité

**Objectif :** Responsive mobile-first, WCAG AA.

| ID | Story | Critères d'acceptation |
|----|-------|------------------------|
| 8.1 | Responsive | Bootstrap breakpoints ; mobile (nav hamburger), tablet, desktop ; layout maquette |
| 8.2 | Accessibilité | Contrastes WCAG AA ; focus visible ; zones cliquables ≥ 44px ; navigation clavier |
