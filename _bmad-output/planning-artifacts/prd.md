---
stepsCompleted: ['step-01-init', 'step-02-discovery', 'step-02b-vision', 'step-02c-executive-summary', 'step-03-success', 'step-04-journeys', 'step-05-domain', 'step-06-innovation']
classification:
  projectType: web_app
  domain: general
  complexity: low
  projectContext: greenfield
inputDocuments:
  - "Maquette/maquette.png (maquette du site)"
  - "Maquette/logo vectoriel et image/ (logo PNG multi-tailles + logo carrosserie lino.svg)"
workflowType: 'prd'
briefCount: 0
researchCount: 0
brainstormingCount: 0
projectDocsCount: 0
---

# Product Requirements Document - site-alexis

**Author:** Eoran
**Date:** 2026-02-27

**Contexte produit (découverte Step 1):** Site internet pour **Carrosserie Lino** à Baldersheim. Assets fournis : maquette (Maquette/maquette.png), logo au format PNG (plusieurs tailles) et SVG (Maquette/logo vectoriel et image/).

**Contexte commanditaire :** **Alexis Haffner** a racheté le garage en 2022. Il demande de l'aide car il n'a pas la même vision que l'ancien propriétaire ; le site actuel affiche encore le visage de l'ancien propriétaire. Objectif : refondre le site pour refléter le nouveau visage et la nouvelle vision (Alexis).

---

## 5 Whys — Justification du projet (Advanced Elicitation)

**Chaîne :** Pourquoi un site ? → pour être trouvable en ligne → pour que les clients potentiels choisissent en connaissance de cause → pour qu'ils fassent confiance avant de contacter → pour générer des demandes qualifiées.

**Cause racine :** Rendre l'activité **trouvable**, **crédible** et **contactable** pour des gens qui cherchent déjà un carrossier.

**Implications pour la solution :**
- **Trouvable :** contenu et structure adaptés au référencement local (Baldersheim, carrosserie).
- **Crédible :** identité visuelle actuelle (maquette, logo), infos utiles (horaires, zone, types de prestations), ton professionnel.
- **Contactable :** moyen de contact évident (téléphone, formulaire ou email) sans friction.
- Pas de sur-dimensionnement côté public : site vitrine centré sur visibilité et contact ; back-office réservé à la gestion du contenu.

---

## Stack et périmètre technique (précision)

- **Stack :** Projet **Symfony** en dernière version (8+).
- **Comptes et rôles :**
  - **Administrateur du site** : compte admin (gestion globale du site).
  - **Alexis** : son propre compte ; il peut **modifier les horaires** et **ajouter des avis (commentaires)**.
- **Avis / commentaires :** Il s'agit des **avis Google** (reviews). L'API Google Avis étant trop coûteuse, les avis sont **saisis manuellement** dans le back-office (ex. texte, auteur, note, date) puis affichés sur le site pour renforcer la crédibilité.
- **Demander un devis (maquette) :** Le bouton ouvre un formulaire ; la soumission **envoie un email** (à Alexis / au garage). Pas de suivi de devis ni CRM côté site en v1 — réception et traitement des demandes par email.
- **Messages à date bornée :** Alexis peut créer **autant de messages qu'il veut** (souvent 0–2). Chaque message : contenu (ex. « Le garage sera fermé du 9 au 13 décembre »), **date et heure de début d'affichage**, **date et heure de fin d'affichage**. Emplacement à définir en phase de réalisation. Affichage **en rouge**.
- **Traduction / i18n :** La traduction est **prévue dès le départ**. On se concentre sur le **français** en priorité ; la **structure multilingue** (Symfony i18n, locales) est en place dès le MVP. À chaque ajout ou mise à jour de contenu, on fait la **traduction dans la foulée** (ex. allemand pour viser Allemagne / Suisse) — pas de refonte ultérieure pour ajouter les langues.

---

## Executive Summary

Refondre le site de la **Carrosserie Lino** (Baldersheim) pour qu'il porte la **nouvelle identité d'Alexis Haffner** (propriétaire depuis 2022) et qu'il soit **trouvable**, **crédible** et **contactable** pour les particuliers qui cherchent un carrossier en local. Le site actuel affiche encore l'ancien propriétaire ; l'enjeu est de donner le bon visage et la bonne vision tout en gardant la notoriété du nom Lino. **Utilisateurs cibles :** particuliers en recherche locale (cible principale) ; administrateur du site et Alexis (back-office). **Problème adressé :** visibilité et confiance en ligne — besoin d'un site à jour (identité, horaires, avis) et d'un contact simple (téléphone + demande de devis) pour générer des demandes qualifiées.

### What Makes This Special

- **Identité claire :** mise en avant d'Alexis (photo, texte, ton), plus l'ancien propriétaire.
- **Autonomie :** Alexis met à jour horaires et avis (saisie manuelle type Google) sans dépendre du dev ; gestion des messages à date bornée (ex. fermeture pour congés).
- **Simplicité technique :** formulaire « Demander un devis » → envoi par email ; pas d'API Google Avis ni de CRM en v1.
- **Crédibilité :** horaires à jour, avis affichés, CTA devis visible (aligné maquette).

### Project Classification

- **Type :** application web (site vitrine + back-office).
- **Domaine :** général (activité locale, pas de secteur régulé).
- **Complexité :** faible.
- **Contexte :** greenfield (refonte complète).

---

## Success Criteria

### User Success

- **Visiteur :** trouve Carrosserie Lino (Baldersheim), voit une identité claire (Alexis), des horaires à jour, des avis, et peut contacter ou demander un devis sans blocage.
- **Alexis :** met à jour horaires, avis et messages à date bornée seul, sans repasser par un dev.
- **Admin :** gère les comptes et le site si besoin.

### Business Success

- Le site affiche la **nouvelle identité** (Alexis, plus l'ancien propriétaire).
- **Demandes qualifiées :** les visiteurs utilisent le formulaire devis ou le téléphone.
- À 3–6 mois : site en ligne, contenu à jour, quelques demandes de devis ou appels attribuables au site.

### Technical Success

- **Symfony 8+ :** code structuré, maintenable.
- **Sécurité :** authentification (admin + Alexis), formulaires publics (**devis** et **contact**) protégés (validation, anti-spam basique, **captcha**).
- **Disponibilité :** envoi d'email fiable pour le devis et le contact ; affichage des messages selon les dates.

### Measurable Outcomes

- Site en production avec maquette appliquée, logo et identité Alexis.
- Au moins un canal de contact fonctionnel (devis ou téléphone) utilisé.
- Alexis peut modifier horaires, avis et messages à date bornée via le back-office.

---

## Product Scope

### MVP - Minimum Viable Product

- Site public : pages selon maquette, horaires, avis (saisis manuellement), CTA « Demander un devis » → email, messages à date bornée (affichage rouge, emplacement à définir).
- Back-office : compte admin, compte Alexis ; Alexis peut gérer horaires, avis, messages à date bornée (liste illimitée, contenu + dates).
- **i18n :** Structure multilingue en place dès le MVP (Symfony i18n, locales). Contenu rédigé d'abord en français ; traduction (ex. allemand) prévue dans la foulée à chaque création ou mise à jour de contenu.
- Stack : Symfony 8+, hébergement adapté, envoi d'emails opérationnel.

### Pages à créer

- **Mentions légales** : page `/mentions-legales` (lien dans le footer après « Baldersheim »). Contenu à rédiger ultérieurement.

### Growth Features (Post-MVP)

- SEO local (baldage, microdonnées), y compris par langue (FR / DE).
- Amélioration formulaire devis (champs, notifications).
- Option : types d'annonces si besoin.

### Vision (Future)

- Intégrations optionnelles (ex. rappel Google, autre) seulement si besoin métier clair.

---

## User Journeys

### 1. Visiteur — Parcours principal (demander un devis)

*Marie, 34 ans, a accroché son véhicule ; elle cherche un carrossier proche de Baldersheim.*

- **Ouverture :** Elle cherche « carrosserie Baldersheim » ou « Carrosserie Lino ». Elle atterrit sur le site, voit le nom, le logo et comprend tout de suite qu'il s'agit du bon garage.
- **Montée en charge :** Elle parcourt la page : horaires, zone d'intervention, éventuellement une courte présentation d'Alexis. Elle voit des avis clients (saisis manuellement type Google) et se rassure. Un bandeau ou encart peut afficher un message à date (ex. fermeture exceptionnelle) si pertinent.
- **Climax :** Elle clique sur « Demander un devis », remplit le formulaire (véhicule, type de prestation, message, coordonnées), envoie. Elle reçoit une confirmation (si prévue) ou note le numéro pour rappeler.
- **Résolution :** Elle a transmis sa demande ou sait comment joindre le garage. Elle a une image claire de qui gère le garage (Alexis) et des infos à jour.

### 2. Visiteur — Cas secondaire (vérifier horaires / fermeture)

*Thomas veut passer en fin de semaine ; il vérifie si le garage est ouvert.*

- **Ouverture :** Il arrive sur le site (recherche ou lien favori) pour vérifier les horaires.
- **Montée en charge :** Il consulte la section horaires (à jour, gérée par Alexis). Si une fermeture est prévue (ex. congés), il voit le **message à date bornée** (« Le garage sera fermé du 12 au 22 février pour cause de congé ») bien visible.
- **Climax :** Il sait s'il peut se présenter ou s'il doit attendre / rappeler plus tard.
- **Résolution :** Pas de déplacement inutile ; confiance renforcée grâce à une info claire et à jour.

### 3. Alexis — Mise à jour du contenu (horaires, avis, message de fermeture)

*Alexis est propriétaire ; il veut mettre à jour les horaires, ajouter un avis Google recopié et annoncer une fermeture.*

- **Ouverture :** Il se connecte au back-office avec son compte (pas celui de l'admin).
- **Montée en charge :** Il accède à la gestion des horaires, les modifie et enregistre. Il ouvre la gestion des avis, crée un nouvel avis (texte, auteur, note, date) pour un avis Google qu'il recopie. Il ouvre la gestion des « messages à date bornée », crée ou modifie un message (ex. « Garage fermé du 12 au 22 février pour congé ») et définit les dates d'affichage.
- **Climax :** Chaque modification est sauvegardée ; il peut se déconnecter en sachant que le site public affichera les bonnes infos (et les messages uniquement dans la période choisie).
- **Résolution :** Il garde le site à jour sans faire appel au dev ; les visiteurs voient toujours une information fiable.

### 4. Admin — Gestion du site et des accès

*L'administrateur du site (toi ou un tiers) assure la configuration et la sécurité.*

- **Ouverture :** Il se connecte au back-office avec le compte administrateur.
- **Montée en charge :** Il peut gérer les comptes (ex. réinitialiser le mot de passe d'Alexis), vérifier que le formulaire devis envoie bien les emails, et éventuellement superviser le contenu (horaires, avis, messages) si besoin.
- **Climax :** Il garantit que le site et les accès sont sous contrôle et que les demandes de devis arrivent bien à destination.
- **Résolution :** Le site reste exploitable et sécurisé ; Alexis peut travailler en autonomie sur le contenu.

### Journey Requirements Summary

- **Site public :** pages alignées sur la maquette ; horaires dynamiques ; affichage des avis (saisis manuellement) ; affichage conditionnel des messages à date bornée (rouge, emplacement à définir) ; formulaires « Demander un devis » et **« Contact »** avec envoi d'email et **captcha anti-spam** ; contact visible (téléphone, etc.) ; identité Alexis (nom, photo, texte).
- **Back-office Alexis :** authentification ; écrans de gestion des horaires, des avis et des messages à date bornée (liste illimitée, contenu + dates) ; pas d'accès à la gestion des autres comptes.
- **Back-office Admin :** authentification ; gestion des comptes (et des rôles) ; possibilité de vérifier ou superviser le contenu et le bon fonctionnement (ex. envoi des devis).
- **Technique :** Symfony 8+ ; envoi d'emails fiable ; règles d'affichage des messages selon les dates ; sécurité (auth, validation formulaire, **captcha** et anti-spam basique sur les formulaires publics).
