<?php
namespace App\Models;
use CodeIgniter\Model;
class CategoryModel extends Model
{
	protected $table = 'categorie';
	protected $allowedFields = [
		'titre_categorie'
	];
	



}