<?php
	namespace App\Controllers\Utilisateur;
	use CodeIgniter\Controller;
	use App\Models\UserModel;
	use App\Controllers\BaseController;

	class SigninController extends BaseController
	{
		public function index()
		{
			if (session()->get('isLoggedIn'))
			{
				// Si la session a été marquée pour la redirection
				if (session()->get('auto_redirect'))
				{
					return redirect()->to('/tasks');
				}
			}


			helper(['form']);

			$data = [
				'pageTitle' => 'Connexion'
			];
		
			echo view('commun/header', $data);
			echo view('Utilisateur/signin');
			echo view('commun/footer');
		}


		public function loginAuth()
		{
			$session   = session();
			$userModel = new UserModel();
			$email     = $this->request->getVar('email_user');
			$password  = $this->request->getVar('password');
			$remember  = $this->request->getVar('remember');


			$data      = $userModel->where('email_user', $email)->first();
			
			if($data)
			{
				$pass = $data['password'];
				$authenticatePassword = password_verify($password, $pass);

				if($authenticatePassword)
				{
					$ses_data = [
						'id_user'     => $data['id_user'],
						'nom_user'    => $data['nom_user'],
						'prenom_user' => $data['prenom_user'],
						'email_user'  => $data['email_user'],
						'isLoggedIn'  => TRUE
					];

					$session->set($ses_data);

					if ($data['is_verified'] == 'f')
					{
						$session->setFlashdata('msg', 'Votre compte n\'est pas encore activé. Veuillez vérifier votre boîte mail.');
						return redirect()->to('/signin');
					}

					// Gestion du "Se souvenir de moi"
					if ($remember)
					{
						// Génère un token unique pour "Se souvenir de moi"
						$rememberToken = bin2hex(random_bytes(16)); // Génère un token unique
		
						// Met à jour le `remember_token` dans la base de données
						$userModel->update($data['id_user'], ['remember_token' => $rememberToken]);
		
						// Enregistre le token dans un cookie (expire dans 24 heures)
						set_cookie('remember_cookie', $rememberToken, 86400, '/', '/', false, true); // Expire dans 24 heures
					}

					return redirect()->to('/tasks');
				}
				else
				{
					$session->setFlashdata('msg', 'Mot de passe incorrect.');
					return redirect()->to('/signin');
				}
			}
			else
			{
				$session->setFlashdata('msg', `Cet email n'existe pas.`);
				return redirect()->to('/signin');
			}
		}
	}
?>