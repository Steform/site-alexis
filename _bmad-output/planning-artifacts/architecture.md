---
stepsCompleted: [1, 2, 3, 4, 5, 6, 7, 8]
inputDocuments:
  - "_bmad-output/planning-artifacts/prd.md"
  - "_bmad-output/planning-artifacts/ux-design-specification.md"
workflowType: 'architecture'
lastStep: 8
status: 'complete'
completedAt: '2026-03-11'
project_name: 'site-alexis'
user_name: 'Eoran'
date: '2026-03-11'
---

# Architecture Decision Document

_This document builds collaboratively through step-by-step discovery. Sections are appended as we work through each architectural decision together._

---

## Project Context Analysis

### Requirements Overview

**Functional Requirements:**
- **Site public** : Pages selon maquette (index prioritaire), horaires dynamiques, avis clients (saisie manuelle), CTA devis, messages à date bornée (liste, affichage rouge, emplacement à définir), identité Alexis (photo, palmarès).
- **Formulaire devis** : Envoi par email ; pas de CRM ni suivi en v1.
- **Back-office** : Admin (gestion globale) ; Alexis (horaires, avis, messages à date bornée — liste illimitée, contenu + dates). Autonomie sans dev.
- **i18n** : Structure multilingue dès le MVP (Symfony i18n, locales) ; FR prioritaire, DE prévu.

**Non-Functional Requirements:**
- **Stack** : Symfony 8+.
- **Sécurité** : Authentification (admin + Alexis), formulaire protégé (anti-spam, validation).
- **Disponibilité** : Envoi d’email fiable ; affichage des messages selon dates début/fin.
- **UX** : Bootstrap, responsive, WCAG AA, dark mode prévu.

**Scale & Complexity:**
- **Primary domain** : Full-stack web (Symfony + Twig + front).
- **Complexity level** : Low (PRD classification).
- **Architectural components** : Site public, back-office, entités (horaires, avis, messages) ; formulaire, email, i18n.

### Technical Constraints & Dependencies

- **Symfony 8+** : Framework imposé.
- **Email** : Envoi fiable pour le devis ; choix du service (Mailer, SMTP, etc.) à décider.
- **i18n** : Symfony locales dès le départ ; structure de contenu traduisible.
- **Pas d’API Google Avis** : Avis saisis manuellement.

### Cross-Cutting Concerns Identified

- **Authentification / rôles** : Admin vs Alexis ; permissions back-office.
- **i18n** : Tous les contenus éditables ; structure de traduction.
- **Messages à date bornée** : Logique d’affichage selon dates (start/end).
- **Responsive / accessibilité** : Mobile-first, WCAG AA, dark mode.

---

## Starter Template Evaluation

### Primary Technology Domain

**Full-stack web (Symfony)** — PRD impose Symfony 8+ ; site vitrine + back-office, Twig, Bootstrap (UX spec).

### Starter Options Considered

- **symfony new --webapp** : Skeleton officiel avec Twig, structure assets, config. Pas Bootstrap par défaut — à ajouter.
- **symfony/skeleton + webapp** : Équivalent via Composer.
- **Alternatives** : Symfony Demo (--demo) — trop lourd pour un site vitrine simple.

### Selected Starter: Symfony Webapp

**Rationale for Selection:**
- Aligné avec le PRD (Symfony 8+).
- Twig inclus ; structure `templates/`, `assets/`, `config/` prête.
- Bootstrap à ajouter manuellement (aligné UX spec).
- Léger, maintenu, standard Symfony.

**Initialization Command:**

```bash
symfony new site-alexis --version="8.*" --webapp
```

*Ou si Symfony CLI non installé :*
```bash
composer create-project symfony/skeleton:"8.*" site-alexis
cd site-alexis
composer require webapp
```

### Architectural Decisions Provided by Starter

**Language & Runtime:** PHP 8.2+, Symfony 8.x

**Templating:** **Twig** — moteur de templates Symfony ; utilisé pour le site public, le back-office et l’i18n (locales + traduction)

**Styling:** À ajouter — Bootstrap (composer require twbs/bootstrap ou Webpack Encore)

**Build Tooling:** Structure assets/ ; Webpack Encore optionnel pour Bootstrap/JS

**Code Organization:** src/, templates/, config/, assets/ ; conventions Symfony

**Development Experience:** symfony server:start ; hot reload si Encore

**Note:** L’initialisation du projet avec cette commande doit être la première story d’implémentation.

---

## Core Architectural Decisions

### Decision Priority Analysis

**Critical Decisions (Block Implementation):**
- Symfony 8.*, Twig, Bootstrap
- Base de données (Doctrine)
- Authentification (Symfony Security)
- Envoi email (Symfony Mailer)

**Important Decisions (Shape Architecture):**
- Structure entités (horaires, avis, messages)
- i18n (Symfony Translation)
- Gestion des assets (Bootstrap, thème custom)

**Deferred Decisions (Post-MVP):**
- Hébergement détaillé, CI/CD
- SEO avancé, microdonnées

### Data Architecture

- **ORM** : Doctrine (inclus Symfony)
- **Base** : SQLite (dev) ; MySQL ou PostgreSQL (prod) — à configurer
- **Entités** : User (admin, Alexis), Horaires, Avis (texte, auteur, note, date), Message (contenu, date_debut, date_fin — liste illimitée ; affichage en rouge, emplacement à définir)
- **Migrations** : Doctrine migrations
- **i18n** : Symfony Translation + locales (fr, de) ; contenu traduisible

### Authentication & Security

- **Auth** : Symfony Security (native)
- **Rôles** : ROLE_ADMIN, ROLE_ALEXIS (ou rôle dédié)
- **Formulaire devis** : Validation, protection CSRF ; anti-spam (honeypot ou CAPTCHA léger si besoin)
- **Back-office** : Accès restreint par rôle

### API & Communication Patterns

- **API publique** : Aucune en v1
- **Email** : Symfony Mailer ; configuration SMTP ou service (à définir)
- **Formulaire devis** : Soumission → Mailer → envoi à Alexis/garage

### Frontend Architecture

- **Templates** : Twig (site public + back-office)
- **CSS/JS** : Bootstrap + assets custom (thème maquette)
- **Assets** : Webpack Encore ou inclusion Bootstrap CDN + CSS custom
- **Responsive** : Bootstrap breakpoints ; mobile-first
- **Dark mode** : Prévoir variables CSS / thème (post-MVP ou dès MVP)

### Infrastructure & Deployment

- **Hébergement** : À définir (shared, VPS, PaaS)
- **Environnements** : dev, prod
- **CI/CD** : À définir post-MVP

### Decision Impact Analysis

**Implementation Sequence:**
1. Symfony project init (starter)
2. Bootstrap + Twig base
3. Entités + migrations
4. Auth + rôles
5. CRUD horaires, avis, messages
6. Formulaire devis + Mailer
7. i18n
8. Front (pages, maquette)

**Cross-Component Dependencies:**
- Auth → back-office (tous les CRUD)
- Entités → formulaire devis (données), messages (affichage conditionnel)
- i18n → tous les contenus éditables

---

## Implementation Patterns & Consistency Rules

### Pattern Categories Defined

**Critical Conflict Points Identified:** Conventions Symfony/Doctrine/Twig à respecter pour éviter les divergences entre agents.

### Naming Patterns

**Database (Doctrine):**
- Tables : snake_case pluriel (`users`, `avis`, `messages`)
- Colonnes : snake_case (`created_at`, `date_debut`, `date_fin`)
- Entités : PascalCase singulier (`User`, `Avis`, `Message`)

**Code (PHP/Symfony):**
- Controllers : `*Controller` suffix ; méthodes en camelCase
- Services : suffix `*Service` ou nom descriptif
- Routes : snake_case ou kebab-case (`app_devis`, `app_back_avis`)

**Twig :**
- Templates : snake_case (`base.html.twig`, `devis_form.html.twig`)
- Blocks : camelCase ou snake_case cohérent

### Structure Patterns

**Project Organization:**
- `src/Controller/` : controllers par domaine (Public/, Back/)
- `src/Entity/` : entités Doctrine
- `src/Repository/` : repositories
- `templates/` : structure miroir des controllers
- `assets/` : CSS, JS, images (Bootstrap + custom)

**Fichiers :**
- Config : `config/` ; env : `.env`, `.env.local`
- Migrations : `migrations/`

### Format Patterns

**Formulaires :**
- Validation : contraintes Symfony (Assert)
- Messages d’erreur : clés de traduction (`devis.email.invalid`)

**Dates :**
- Stockage : `datetime` ou `date` Doctrine
- Affichage : format i18n via Twig `|date`

### Process Patterns

**Error Handling:**
- Exceptions : laisser remonter ; ErrorController pour 404/500
- Formulaires : `form.vars.errors` dans Twig

**Validation :**
- Côté serveur uniquement (formulaires Symfony)
- Messages traduits (Symfony Translation)

### Enforcement Guidelines

**All AI Agents MUST:**
- Suivre les conventions Doctrine (snake_case DB, PascalCase entités)
- Respecter la structure Symfony (Controller, Entity, Repository)
- Utiliser les contraintes de validation Symfony
- Traduire les messages (clés i18n)

**Pattern Enforcement:**
- PHPStan / PHP CS Fixer si configuré
- Revue de code sur les entités et controllers

### Pattern Examples

**Good:** `Avis` entity avec `author`, `rating`, `created_at` ; table `avis`  
**Avoid:** `Avis` avec colonnes `Author`, `Rating` (PascalCase en DB)

---

## Project Structure & Boundaries

### Directory Structure (Symfony)

```
site-alexis/
├── bin/
├── config/
│   ├── packages/
│   ├── routes/
│   └── services.yaml
├── migrations/
├── public/
│   ├── build/          # assets compilés (Encore)
│   └── index.php
├── src/
│   ├── Controller/
│   │   ├── Public/     # index, devis, services, etc.
│   │   └── Back/       # back-office (horaires, avis, messages)
│   ├── Entity/
│   │   ├── User.php
│   │   ├── Horaires.php
│   │   ├── Avis.php
│   │   └── Message.php
│   ├── Repository/
│   ├── Form/
│   └── Service/
├── templates/
│   ├── base.html.twig
│   ├── public/         # pages site public
│   │   ├── index.html.twig
│   │   └── devis/
│   └── back/           # back-office
├── assets/
│   ├── styles/         # Bootstrap + custom
│   ├── images/
│   └── app.js
├── translations/
│   ├── messages.fr.yaml
│   └── messages.de.yaml
├── .env
├── .env.local
├── composer.json
└── symfony.lock
```

### Requirements Mapping

| FR | Composant |
|----|-----------|
| Site public (index, pages) | `Controller/Public/`, `templates/public/` |
| Formulaire devis | `Controller/Public/DevisController`, `Form/DevisType` |
| Back-office (horaires, avis, messages) | `Controller/Back/`, `templates/back/` |
| Auth (admin, Alexis) | Symfony Security, `User` |
| i18n | `translations/`, Twig `|trans` |
| Email devis | `Service/MailerService` ou Mailer Symfony |

### Integration Boundaries

- **Front/Back** : Controllers séparés ; Security firewall pour `/back`
- **Data** : Repository par entité ; pas de logique métier dans les controllers
- **Assets** : `public/build/` ou CDN Bootstrap + CSS custom
