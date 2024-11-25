<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Connexion
$routes->get('/', 'Utilisateur\SigninController::index');
$routes->get('/signin', 'Utilisateur\SigninController::index');
$routes->match(['get', 'post'], 'signin/auth', 'Utilisateur\SigninController::loginAuth');

// Inscription
$routes->get('/signup', 'Utilisateur\SignupController::index');
$routes->match(['get', 'post'], 'signup/store', 'Utilisateur\SignupController::store');
$routes->get('/activate/(:any)', 'Utilisateur\SignupController::activate/$1');

// Profil
$routes->get('/profile', 'Utilisateur\UserController::profile', ['filter' => 'authguard']);
$routes->post('/profile/update', 'Utilisateur\UserController::updateProfile');

// Réinitialisation de mot de passe
$routes->get('/forgot-password', 'Utilisateur\ForgotPasswordController::index');
$routes->post('/forgot-password/send-reset-link', 'Utilisateur\ForgotPasswordController::sendResetLink');
$routes->get('/reset-password/(:any)', 'Utilisateur\ResetPasswordController::index/$1');
$routes->post('/reset-password/update', 'Utilisateur\ResetPasswordController::updatePassword');


// Taches
$routes->get('/tasks', 'Tache\TaskController::index');  
$routes->post('task/store', 'Tache\TaskController::store');
$routes->get('/tasks/create', 'Tache\TaskController::create'); 
$routes->post('tasks/store', 'Tache\TaskController::store');
