<?php
namespace App\Models;
use CodeIgniter\Model;
class UserModel extends Model
{
	protected $table = 'utilisateur';
	protected $allowedFields = [
		'name',
		'email',
		'password',
		'is_active',
		'activation_code',
		'created_at'
	];
	
}