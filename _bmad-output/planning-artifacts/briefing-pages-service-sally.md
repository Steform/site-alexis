# Briefing pour Sally — Pages détail des services

**Contexte :** Site Carrosserie Lino (garage automobile, Baldersheim). Pages publiques FR/DE avec routes SEO.

**Problème :** Les pages détail des services (`/services/{slug}` et `/de/leistungen/{slug}`) sont décevantes actuellement.

## État actuel

### Structure de la page détail (show.html.twig)

- **Titre** : nom du service (ex. « Réparation carrosserie & peinture »)
- **Image** : une seule image à gauche
- **Contenu** : bloc description (HTML) ou, si vide, « Description à venir. »
- **CTA** : bouton « Demander un devis »

### Ce qui manque / déçoit

1. **Contenu après le teasing** : Sur la liste des services (`/services`), chaque service a un teaser court (ex. « Un rendu impeccable pour votre véhicule »). Sur la page détail, on passe directement à la description longue ou à « Description à venir » — pas de transition, pas de valeur ajoutée claire.
2. **Expérience utilisateur** : Mise en page très simple (image + texte), peu d’éléments pour rassurer, différencier ou guider vers le devis.
3. **SEO / conversion** : Peu de contenu structuré pour convaincre et orienter vers l’action.

## Demande

**Sally, peux-tu proposer une meilleure structure et un meilleur contenu pour les pages détail des services, en particulier pour la partie « après le teasing » ?**

On souhaite :

- Une proposition UX claire (sections, ordre, types de contenu)
- Des idées de contenu pour chaque section (ex. avantages, processus, garanties, FAQ, témoignages, etc.)
- Une approche qui reste cohérente avec un petit garage de proximité (pas trop corporate)
- Une structure réutilisable pour les 5 services : réparation carrosserie & peinture, débosselage, pare-brise & optique, entretien & mécanique, véhicule de prêt courtoisie

## Données disponibles par service

- `titre` / `titreDe` (FR/DE)
- `description` / `descriptionDe` (HTML, optionnel)
- `image` (chemin)
- `slug` / `slugDe` (URLs)

Pas de champs supplémentaires pour l’instant, mais on peut en ajouter si ta proposition le justifie.
