<?php
namespace App\Models;
use CodeIgniter\Model;
class CategoryModel extends Model
{
	protected $table = 'categorie';
	protected $primaryKey = 'id_categorie';
	
	protected $allowedFields = [
		'titre_categorie',
		'id_user_categorie'
	];
}