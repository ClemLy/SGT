# **SGT**

TaskPlanner | SGT est une application développée en **PHP** avec le framework **CodeIgniter 4**. Elle utilise une base de données **PostgreSQL** et inclut un système d'envoi d'email configuré via des variables d'environnement.

---

## **Pré-requis**

Avant d'installer l'application, assurez-vous d'avoir les éléments suivants :

- **PHP 8.1 ou supérieur**
- **Composer** (Gestionnaire de dépendances PHP)
- **PostgreSQL** (Base de données)
- **Serveur Web** (Apache, Nginx ou PHP intégré)

---

## **Installation**

1. **Cloner le dépôt**

   ```bash
   git clone git@github.com:ClemLy/SGT.git
   cd SGT

2. **Installer les dépendances**

   ```bash
    composer install

3. **Configuration de l'environnement**

   Créer un fichier `.env` directement dans le dossier `SGT`.
   Ce fichier contiendra ceci :
   
   ```env
   #--------------------------------------------------------------------
   # DATABASE
   #--------------------------------------------------------------------
   
   CI_ENVIRONMENT=production             # Mode de l'application : 'development' ou 'production'
   db_hostname=localhost                 # Hôte de la base de données (souvent 'localhost')
   db_username=..                        # Nom d'utilisateur pour la connexion à la base de données
   db_password=..                        # Mot de passe de l'utilisateur de la base de données
   db_DBDriver=Postgre                   # Type de base de données (PostgreSQL dans ce cas)
   db_name=..                            # Nom de la base de données
   db_port=..                            # Port de connexion à la base de données (souvent 5432 pour PostgreSQL)
   
   #--------------------------------------------------------------------
   # Email
   #--------------------------------------------------------------------
   
   email_host=.....                      # Hôte du serveur SMTP pour l'envoi d'emails (ex : smtp.gmail.com)
   email_user=.....                      # Adresse email utilisée pour envoyer les emails (ex : votre-email@gmail.com)
   email_password=...                    # Mot de passe de l'email pour l'authentification SMTP
   email_port=587                        # Port pour l'envoi d'emails (587 est généralement utilisé pour TLS)
   ```

4. **Exécuter l'environnement**

   Afin d'exécuter votre fichier `.env`, installez la bibliothèque vlucas/phpdotenv en exécutant cette commande dans le terminal SGT :
   ```cmd
    composer require vlucas/phpdotenv
   ```

5. **Démarrer le serveur local**
  
   ```bash
    php spark serve
   ```
   
   L'application sera disponible à l'adresse : http://localhost:8080

---

## **Configurer un mot de passe d'application pour Yahoo**

Yahoo nécessite l'utilisation d'un mot de passe d'application plutôt que votre mot de passe principal. Cela permet de renforcer la sécurité des comptes de messagerie en limitant l'accès aux applications tierces. Voici comment générer un mot de passe d'application pour Yahoo :

1. **Connectez-vous à votre compte Yahoo**  
   Rendez-vous sur [Yahoo Mail](https://mail.yahoo.com) et connectez-vous à votre compte.

2. **Accédez aux paramètres de sécurité**  
   Allez dans les paramètres de votre compte en cliquant sur votre avatar dans le coin supérieur droit, puis sélectionnez "Paramètres du compte" ou "Compte". Ensuite, allez dans **Sécurité du compte**.

3. **Générez un mot de passe d'application**  
   Sous l'option "Mots de passe d'application", vous trouverez la possibilité de générer un mot de passe spécifique pour une application tierce. Cliquez sur cette option.

4. **Sélectionnez l'application**  
   Choisissez l'application pour laquelle vous souhaitez générer un mot de passe d'application. Si "CodeIgniter" ou un autre nom spécifique n'est pas proposé, sélectionnez "Autre (personnalisé)".

5. **Suivez les instructions**  
   Après avoir choisi l'application, Yahoo vous demandera de confirmer votre action et générera un mot de passe d'application. Ce mot de passe est à usage unique et ne peut pas être récupéré une fois généré.

6. **Utilisez ce mot de passe dans votre configuration SMTP**  
   Remplacez le mot de passe de votre compte Yahoo dans la configuration SMTP de CodeIgniter par le mot de passe d'application nouvellement généré.

**Pourquoi un mot de passe d'application ?**  
Cette approche est plus sécurisée car elle limite l'accès de l'application tierce (CodeIgniter) aux fonctionnalités spécifiques dont elle a besoin sans compromettre votre mot de passe principal Yahoo. Ce mot de passe d'application est également limité et ne pourra pas être utilisé ailleurs.

**Important :**  
Gardez ce mot de passe d'application en lieu sûr, car une fois généré, vous ne pourrez pas le récupérer. Si vous perdez ce mot de passe, vous devrez en générer un nouveau.

---

## Déploiement

Pour déployer l'application en production :

1. Configurer votre serveur avec la base de données dans votre fichier `.env`.
2. Configurer votre adresse mail avec les paramètres email corrects dans votre fichier `.env` (Voir tutoriel Yahoo).
3. Utiliser un serveur web comme Nginx ou Apache pour servir l'application.

---

## Utilisation

1. Créer des utilisateurs et des tâches depuis l'interface.
2. Gérer les tâches avec des statuts : **À faire**, **En cours**, **Terminée**.
3. Recevoir des rappels par email pour les tâches arrivant à échéance dans 48 heures.

---

### Points à noter :
- **Clonage du dépôt** : Assurez-vous de bien indiquer les bonnes informations pour accéder à votre dépôt (si vous êtes sur GitHub, par exemple, utilisez le lien HTTPS si vous n'avez pas configuré les clés SSH).
- **Configuration de l'environnement** : Assurez-vous que les variables `db_*` et `email_*` soient configurées correctement avant de faire fonctionner l'application en local ou en production.
