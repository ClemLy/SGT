-- Insertion dans la table UTILISATEUR
INSERT INTO UTILISATEUR (email_user, prenom_user, nom_user, password)
VALUES
('alice.miller@example.com', 'Alice', 'Miller', 'password123'),
('bob.jones@example.com', 'Bob', 'Jones', 'securepwd'),
('carol.wilson@example.com', 'Carol', 'Wilson', '12345'),
('dave.clarkson@example.com', 'Dave', 'Clarkson', 'davepass'),
('eve.taylor@example.com', 'Eve', 'Taylor', 'evet123'),
('frank.hall@example.com', 'Frank', 'Hall', 'frankpwd');

-- Insertion dans la table CATEGORIE
INSERT INTO CATEGORIE (titre_categorie)
VALUES
('Développement'),
('Marketing'),
('Administration'),
('Recherche'),
('Design'),
('Support Client');

-- Insertion dans la table TACHE
INSERT INTO TACHE (titre, description_tache, etat_tache, echeance_tache, id_user, id_categorie)
VALUES
('Créer une API REST', 'Développer une API REST pour le projet principal', 'En cours', '2024-12-15', 1, 1),
('Créer une base de données', 'Concevoir une base de données relationnelle', 'Terminé', '2024-11-10', 1, 1),
('Planifier une campagne', 'Élaborer une stratégie marketing efficace', 'À faire', '2024-11-30', 2, 2),
('Réaliser des wireframes', 'Créer des maquettes pour lapplication mobile', 'En cours', '2024-12-01', 5, 5),
('Rédiger un rapport', 'Synthèse des résultats de la dernière enquête', 'À faire', '2024-11-20', 3, 4),
('Répondre aux tickets', 'Gérer les tickets clients en attente', 'En cours', '2024-11-25', 6, 6),
('Organiser une réunion', 'Préparer la prochaine réunion déquipe', 'À faire', '2024-12-05', 4, 3),
('Créer un logo', 'Conception du logo pour un client', 'En cours', '2024-11-27', 5, 5),
('Tester une fonctionnalité', 'Effectuer des tests QA sur la nouvelle feature', 'Terminé', '2024-11-15', 1, 1),
('Écrire un article', 'Créer un article pour le blog entreprise', 'À faire', '2024-12-10', 2, 2);

-- Insertion dans la table COMMENTAIRE
INSERT INTO COMMENTAIRE (text_commentaire, id_tache)
VALUES
('API presque terminée, quelques tests restants.', 1),
('Base de données conçue avec succès.', 2),
('La stratégie marketing doit inclure les réseaux sociaux.', 3),
('Wireframes validés par léquipe design.', 4),
('Les données collectées sont prêtes pour lanalyse.', 5),
('Plusieurs tickets résolus, reste à vérifier.', 6),
('Ajouté des points pour discussion lors de la réunion.', 7),
('Première version du logo prête pour les retours.', 8),
('Tous les tests QA sont passés sans problème.', 9),
('Article structuré, manque quelques sections.', 10),
('Une idée de collaboration a été proposée.', 3),
('Un bug a été identifié dans la tâche.', 1),
('Relecture nécessaire avant validation.', 10),
('Logo validé par le client.', 8);
