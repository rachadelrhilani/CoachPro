/* CREATE DATABASE coachpro; */
USE coachpro;
CREATE TABLE role (
    id_role INT AUTO_INCREMENT PRIMARY KEY,
    nom_role VARCHAR(50) NOT NULL UNIQUE
);
CREATE TABLE personne (
    id_personne INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(50) NOT NULL,
    prenom VARCHAR(50) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    mot_de_passe VARCHAR(255) NOT NULL,
    telephone VARCHAR(20),
    id_role INT NOT NULL,
    CONSTRAINT fk_personne_role
        FOREIGN KEY (id_role) REFERENCES role(id_role)
);
CREATE TABLE sportif (
    id_sportif INT AUTO_INCREMENT PRIMARY KEY,
    id_personne INT NOT NULL UNIQUE,
    date_inscription DATE NOT NULL,
    CONSTRAINT fk_sportif_personne
        FOREIGN KEY (id_personne) REFERENCES personne(id_personne)
);
CREATE TABLE coach (
    id_coach INT AUTO_INCREMENT PRIMARY KEY,
    id_personne INT NOT NULL UNIQUE,
    photo VARCHAR(255),
    biographie TEXT,
    annees_experience INT CHECK (annees_experience >= 0),
    statut VARCHAR(30),
    CONSTRAINT fk_coach_personne
        FOREIGN KEY (id_personne) REFERENCES personne(id_personne)
);
CREATE TABLE discipline (
    id_discipline INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL UNIQUE
);
CREATE TABLE certification (
    id_certification INT AUTO_INCREMENT PRIMARY KEY,
    nom VARCHAR(100) NOT NULL,
    organisme VARCHAR(100) NOT NULL
);

CREATE TABLE coach_discipline (
    id_coach INT NOT NULL,
    id_discipline INT NOT NULL,
    PRIMARY KEY (id_coach, id_discipline),
    CONSTRAINT fk_cd_coach
        FOREIGN KEY (id_coach) REFERENCES coach(id_coach),
    CONSTRAINT fk_cd_discipline
        FOREIGN KEY (id_discipline) REFERENCES discipline(id_discipline)
);
CREATE TABLE coach_certification (
    id_coach INT NOT NULL,
    id_certification INT NOT NULL,
    PRIMARY KEY (id_coach, id_certification),
    CONSTRAINT fk_cc_coach
        FOREIGN KEY (id_coach) REFERENCES coach(id_coach),
    CONSTRAINT fk_cc_certification
        FOREIGN KEY (id_certification) REFERENCES certification(id_certification)
);
CREATE TABLE disponibilite (
    id_disponibilite INT AUTO_INCREMENT PRIMARY KEY,
    date DATE NOT NULL,
    heure_debut TIME NOT NULL,
    heure_fin TIME NOT NULL,
    statut VARCHAR(30),
    id_coach INT NOT NULL,
    CONSTRAINT fk_disponibilite_coach
        FOREIGN KEY (id_coach) REFERENCES coach(id_coach),
    CONSTRAINT chk_heure CHECK (heure_fin > heure_debut)
);
CREATE TABLE reservation (
    id_reservation INT AUTO_INCREMENT PRIMARY KEY,
    date_reservation DATETIME NOT NULL,
    statut VARCHAR(30),
    id_sportif INT NOT NULL,
    id_coach INT NOT NULL,
    id_disponibilite INT NOT NULL,
    CONSTRAINT fk_res_sportif
        FOREIGN KEY (id_sportif) REFERENCES sportif(id_sportif),
    CONSTRAINT fk_res_coach
        FOREIGN KEY (id_coach) REFERENCES coach(id_coach),
    CONSTRAINT fk_res_disponibilite
        FOREIGN KEY (id_disponibilite) REFERENCES disponibilite(id_disponibilite)
);
