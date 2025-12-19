# CoachPro – Plateforme de Coaching Sportif

CoachPro est une plateforme web professionnelle qui met en relation des **coachs sportifs** et des **sportifs**, permettant la gestion des profils, des disponibilités, des réservations et des disciplines sportives dans une interface moderne et intuitive.

---

## Fonctionnalités principales

### Authentification & Sécurité
- Inscription et connexion sécurisées
- Hashage des mots de passe avec `password_hash`
- Gestion des rôles : **Coach / Sportif**
- Protection des pages par session
- Validation des formulaires avec **Regex**
- Notifications d’erreurs modernes (pop-up Tailwind)

---

### Gestion des profils

#### Coach
- Photo de profil (URL)
- Biographie
- Années d’expérience
- Statut (Freelance, Certifié…)
- Disciplines sportives
- Certifications
- Modification du profil

#### Sportif
- Profil simple
- Accès aux coachs et réservations

---

### Disponibilités & Réservations
- Création des disponibilités par les coachs
- Affichage des disponibilités
- Réservation par les sportifs
- Modification / Annulation de réservation
- Gestion des contraintes de clés étrangères (MySQL)

---

### Dashboard Coach
- Liste des disponibilités
- Réservations associées
- Interface responsive
- Menu latéral dynamique

---

### UI / UX
- Design moderne avec **Tailwind CSS**
- Background flou (blur)
- Cartes glassmorphism
- Scroll personnalisé
- Pop-up de notification animées
- Responsive desktop & mobile

---

## Architecture du projet

```CoachPro/
│
├── auth/
│ ├── login.php
│ ├── register.php
│
├── coach/
│ ├── dashboard.php
│ ├── profil.php
│ ├── modifier_profil.php
│ ├── disponibilites.php
│ ├── modifier_reservation.php
│ └── annuler_reservation.php
│
├── sportif/
│ └── dashboard.php
│
├── Components/
│ ├── aside_coach.php
│ └── aside_sportif.php
│
├── Connectdb/
│ └── connect.php
│
├── images/
│ └── sportback.jpg
│
└── README.md 
```

---

## Base de données (MySQL)

### Tables principales
- `personne`
- `role`
- `coach`
- `sportif`
- `discipline`
- `certification`
- `coach_discipline`
- `coach_certification`
- `disponibilite`
- `reservation`

### Relations clés
- `personne` → `role`
- `coach` → `personne`
- `sportif` → `personne`
- `reservation` → `disponibilite`
- `coach` ↔ `discipline`
- `coach` ↔ `certification`

---

## Technologies utilisées

| Technologie | Description |
|-----------|------------|
| PHP 8 | Backend |
| MySQL | Base de données |
| mysqli | Connexion DB sécurisée |
| Tailwind CSS | UI moderne |
| JavaScript | Validation & interactions |
| HTML5 | Structure |
| CSS | Animations & effets |

---

## Validation des formulaires

- Regex simples et compréhensibles
- Vérification côté client
- Blocage de l’envoi si erreur
- Affichage des erreurs via **pop-up Tailwind animée**

---

## Installation

1. Cloner le projet :
git clone https://github.com/ton-compte/CoachPro.git
2. Importer la base de données MySQL
3. Configurer la connexion :
`Connectdb/connect.php`
4. Lancer avec Laragon / XAMPP / WAMP
5. Accéder :
 `http://localhost/CoachPro`

