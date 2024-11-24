<?php
	namespace App\Controllers;
	use App\Models\UserModel;
	use App\Models\UserModelB;
	use CodeIgniter\Controller;

	class ResetPasswordController extends Controller
	{
		public function index($token)
		{
			helper(['form']);
			$userModel = new UserModelB();
			$user = $userModel->where('reset_token', $token)

			->where('reset_token_expiration >', date('Y-m-d H:i:s'))
			->first();

			if ($user)
			{
				return view('reset_password', ['token' => $token]);
			}
			else
			{
				return 'Lien de réinitialisation non valide.';
			}
		}

		public function updatePassword()
		{
			$token = $this->request->getPost('token');
			$password = $this->request->getPost('password');
			$confirmPassword = $this->request->getPost('confirm_password');
			
			// Vérification de la validité du token
			$userModelB = new UserModelB();
			$user = $userModelB->where('reset_token', $token)
							   ->where('reset_token_expiration >', date('Y-m-d H:i:s'))
							   ->first();
		
			if ($user && $password === $confirmPassword) {
				// Hachage du nouveau mot de passe
				$hashedPassword = password_hash($password, PASSWORD_DEFAULT);
		
				// Mise à jour dans la table users via UserModel
				$userModel = new UserModel();
				$userModel->set('password', $hashedPassword) // Met à jour le mot de passe
						  ->where('email', $user['email']) // Utiliser l'email de l'utilisateur pour trouver l'enregistrement
						  ->update();
		
				// Mise à jour dans la table mdp via UserModelB
				$userModelB->set('password', $hashedPassword) // Met à jour le mot de passe
							->set('reset_token', null) // Invalider le token
							->set('reset_token_expiration', null) // Réinitialiser l'expiration
							->where('email', $user['email']) // Utiliser l'email pour trouver l'enregistrement dans mdp
							->update();
		
				return 'Mot de passe réinitialisé avec succès.';
			}
			else
			{
				return 'Erreur lors de la réinitialisation du mot de passe.';
			}
		}
	}
?>