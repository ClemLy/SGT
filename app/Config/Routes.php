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

// Profil
$routes->get('/profile', 'Utilisateur\UserController::profile', ['filter' => 'authguard']);
$routes->post('/profile/update', 'Utilisateur\UserController::updateProfile');