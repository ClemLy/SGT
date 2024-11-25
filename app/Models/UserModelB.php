<?php
	namespace App\Models;
	use CodeIgniter\Model;

	class UserModelB extends Model
	{
		protected $table = 'mdp';
		protected $primaryKey = 'id';
		protected $allowedFields = ['email_user', 'password', 'reset_token', 'reset_token_expiration'];
		
		// Autres propriétés du modèle, méthodes de validation, etc.
		public function getUserByEmail($email)
		{
			return $this->where('email_user', $email)->first();
		}

		// Ajouter d'autres méthodes de modèle en fonction de vos besoins
	}
?>