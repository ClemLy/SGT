<?php
namespace App\Models;
use CodeIgniter\Model;
class UserModel extends Model
{
	protected $table = 'utilisateur';
	protected $allowedFields = [
		'nom_user',
		'prenom_user',
		'email_user',
		'password',
		'created_at'
	];
	
}