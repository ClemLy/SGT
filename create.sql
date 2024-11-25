-- Supprimer les tables existantes
DROP TABLE IF EXISTS COMMENTAIRE   CASCADE;
DROP TABLE IF EXISTS TACHE     CASCADE;
DROP TABLE IF EXISTS CATEGORIE CASCADE;
DROP TABLE IF EXISTS UTILISATEUR CASCADE;

-- Création de la table UTILISATEUR
CREATE TABLE UTILISATEUR (
	id_user     SERIAL PRIMARY KEY,
	email_user  VARCHAR(255) UNIQUE NOT NULL,
	prenom_user VARCHAR(100) NOT NULL,
	nom_user    VARCHAR(100) NOT NULL,
	password    VARCHAR(255) NOT NULL,
	reset_token VARCHAR(255),
	reset_token_exp TIMESTAMP,
	activation_code VARCHAR(255),
	is_verified BOOLEAN DEFAULT FALSE
);

-- Création de la table CATEGORIE
CREATE TABLE CATEGORIE (
	id_categorie    SERIAL PRIMARY KEY,
	titre_categorie VARCHAR(100) NOT NULL
);

-- Création de la table TACHE
CREATE TABLE TACHE (
	id_tache          	SERIAL PRIMARY KEY,
	titre             	VARCHAR(255) NOT NULL,
	description_tache 	TEXT,
    etat_tache 			VARCHAR(50) NOT NULL CHECK (etat_tache IN ('À faire', 'En cours', 'Terminée')),
	echeance_tache    	DATE,
	id_user           	INT NOT NULL,
	id_categorie      	INT,
	FOREIGN KEY (id_user)      REFERENCES UTILISATEUR(id_user) ON DELETE CASCADE,   -- Si un utilisateur est supprimé, ses tâches le sont aussi
	FOREIGN KEY (id_categorie) REFERENCES CATEGORIE(id_categorie) ON DELETE SET NULL -- Si une catégorie est supprimée, sa valeur est NULL dans TÂCHE
);

-- Création de la table COMMENT
CREATE TABLE COMMENTAIRE (
	id_commentaire   	SERIAL PRIMARY KEY,
	text_commentaire 	TEXT NOT NULL,
	id_tache     		INT NOT NULL,
	FOREIGN KEY (id_tache) REFERENCES TACHE(id_tache) ON DELETE CASCADE -- Si une tâche est supprimée, ses commentaires le sont aussi
); 
