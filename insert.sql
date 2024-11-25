-- Insertion dans la table UTILISATEUR
INSERT INTO UTILISATEUR (email_user, prenom_user, nom_user, password)
VALUES
('alice.miller@example.com', 'Alice', 'Miller', 'password123'),
('bob.jones@example.com', 'Bob', 'Jones', 'securepwd'),
('carol.wilson@example.com', 'Carol', 'Wilson', '12345'),
('dave.clarkson@example.com', 'Dave', 'Clarkson', 'davepass'),
('eve.taylor@example.com', 'Eve', 'Taylor', 'evet123'),
('frank.hall@example.com', 'Frank', 'Hall', 'frankpwd'),
('grace.hill@example.com', 'Grace', 'Hill', 'grace2024'),
('henry.brown@example.com', 'Henry', 'Brown', 'hbrown123'),
('ivy.green@example.com', 'Ivy', 'Green', 'ivysecure'),
('jack.king@example.com', 'Jack', 'King', 'jackpass'),
('kate.lee@example.com', 'Kate', 'Lee', 'katel789'),
('leo.smith@example.com', 'Leo', 'Smith', 'leo456');

-- Insertion dans la table CATEGORIE
INSERT INTO CATEGORIE (titre_categorie)
VALUES
('Développement'),
('Marketing'),
('Administration'),
('Recherche'),
('Design'),
('Support Client'),
('Ressources Humaines'),
('Qualité'),
('Formation'),
('Vente'),
('Finances'),
('Gestion de Projet');

-- Insertion de 100 données dans la table TACHE
INSERT INTO TACHE (titre, description_tache, etat_tache, echeance_tache, id_user, id_categorie)
VALUES
('Créer une API REST', 'Développer une API REST pour le projet principal', 'En cours', '2024-12-15', 1, 1),
('Créer une base de données', 'Concevoir une base de données relationnelle', 'Terminé', '2024-11-10', 1, 1),
('Planifier une campagne', 'Élaborer une stratégie marketing efficace', 'À faire', '2024-11-30', 2, 2),
('Réaliser des wireframes', 'Créer des maquettes pour l’application mobile', 'En cours', '2024-12-01', 5, 5),
('Rédiger un rapport', 'Synthèse des résultats de la dernière enquête', 'À faire', '2024-11-20', 3, 4),
('Répondre aux tickets', 'Gérer les tickets clients en attente', 'En cours', '2024-11-25', 6, 6),
('Organiser une réunion', 'Préparer la prochaine réunion d’équipe', 'À faire', '2024-12-05', 4, 3),
('Créer un logo', 'Conception du logo pour un client', 'En cours', '2024-11-27', 5, 5),
('Tester une fonctionnalité', 'Effectuer des tests QA sur la nouvelle feature', 'Terminé', '2024-11-15', 1, 1),
('Écrire un article', 'Créer un article pour le blog entreprise', 'À faire', '2024-12-10', 2, 2),
('Créer un tableau de bord', 'Développer un tableau de bord pour la gestion des ventes', 'En cours', '2024-12-20', 1, 6),
('Analyser des données financières', 'Examen des revenus et dépenses trimestriels', 'En cours', '2024-12-05', 11, 11),
('Organiser une formation', 'Planifier et animer une session de formation', 'Terminé', '2024-11-10', 9, 8),
('Concevoir une campagne publicitaire', 'Lancer une campagne promotionnelle sur les réseaux sociaux', 'En cours', '2024-12-25', 2, 2),
('Corriger un bug', 'Résoudre un problème critique sur le module utilisateur', 'Terminé', '2024-11-18', 1, 1),
('Proposer des améliorations', 'Rédiger des suggestions pour optimiser l’application', 'À faire', '2024-11-29', 8, 11),
('Définir des KPIs', 'Mettre en place des indicateurs de performance pour l’équipe', 'En cours', '2024-11-30', 10, 11),
('Effectuer une analyse concurrentielle', 'Étudier les stratégies marketing des concurrents', 'En cours', '2024-12-05', 2, 2),
('Mettre à jour la documentation', 'Compléter la documentation pour le module admin', 'À faire', '2024-12-12', 1, 1),
('Créer un formulaire en ligne', 'Développer un formulaire interactif pour le site web', 'Terminé', '2024-11-22', 5, 5),
-- Ajout de tâches diversifiées
('Écrire une newsletter', 'Créer une newsletter mensuelle pour les clients', 'En cours', '2024-11-28', 2, 2),
('Mettre à jour un serveur', 'Configurer et mettre à jour le serveur principal', 'En cours', '2024-12-10', 1, 1),
('Effectuer un audit interne', 'Vérifier la conformité des processus internes', 'À faire', '2024-12-15', 3, 7),
('Lancer une campagne emailing', 'Créer et envoyer des emails pour une campagne promotionnelle', 'En cours', '2024-12-01', 2, 2),
('Créer un chatbot', 'Développer un chatbot pour le support client', 'En cours', '2024-12-20', 6, 6),
('Préparer une analyse SWOT', 'Effectuer une analyse SWOT pour le département marketing', 'À faire', '2024-12-05', 2, 2),
('Développer une fonctionnalité', 'Implémenter une fonctionnalité de recherche avancée', 'En cours', '2024-12-18', 1, 1),
('Optimiser un algorithme', 'Améliorer les performances de l’algorithme de recherche', 'Terminé', '2024-11-25', 1, 1),
('Créer une vidéo promotionnelle', 'Produire une vidéo pour le lancement d’un produit', 'À faire', '2024-12-08', 5, 5),
('Rédiger une politique de confidentialité', 'Créer une politique RGPD conforme', 'En cours', '2024-12-22', 3, 4),
('Créer une maquette', 'Préparer une maquette pour un nouveau site web', 'À faire', '2024-11-30', 5, 5),
('Effectuer des tests utilisateurs', 'Collecter des retours sur une version beta', 'En cours', '2024-12-14', 8, 2),
('Faire une analyse de risques', 'Identifier les risques d’un projet en cours', 'En cours', '2024-11-29', 10, 11),
('Développer une application mobile', 'Créer une application pour Android et iOS', 'En cours', '2024-12-25', 1, 1),
('Gérer des retours clients', 'Analyser et résoudre les retours négatifs', 'À faire', '2024-11-28', 6, 6),
('Créer un outil interne', 'Développer un outil pour automatiser les tâches répétitives', 'Terminé', '2024-11-22', 1, 1),
('Réaliser un audit SEO', 'Analyser les performances SEO du site', 'En cours', '2024-12-05', 2, 2),
('Effectuer une migration de données', 'Migrer des données vers une nouvelle base', 'À faire', '2024-12-10', 1, 1),
('Organiser un webinaire', 'Préparer un webinaire pour présenter un produit', 'À faire', '2024-12-02', 7, 8),
('Corriger un défaut de design', 'Apporter des corrections au design de l’application', 'En cours', '2024-11-30', 5, 5),
('Réaliser un benchmark', 'Comparer les produits concurrents pour trouver des points d’amélioration', 'En cours', '2024-12-05', 2, 2),
('Intégrer une API tierce', 'Ajouter une API pour la gestion des paiements', 'Terminé', '2024-11-20', 1, 1),
('Proposer une solution innovante', 'Présenter une nouvelle solution pour réduire les coûts', 'En cours', '2024-12-10', 9, 7),
('Concevoir une stratégie de communication', 'Créer un plan pour améliorer la communication interne', 'À faire', '2024-12-15', 3, 3);

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
('Logo validé par le client.', 8),
('La présentation du tableau de bord est en cours d’optimisation.', 11),
('Le contrat doit inclure des clauses spécifiques.', 12),
('Les participants ont apprécié la formation.', 13),
('Quelques erreurs identifiées dans les calculs.', 14),
('Des suggestions d’amélioration ajoutées pour le plan marketing.', 15),
('Des KPIs supplémentaires pourraient être nécessaires.', 16);
