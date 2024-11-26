<?php
	namespace App\Controllers\Utilisateur;
	use CodeIgniter\Controller;
	use App\Models\UserModel;
	use App\Controllers\BaseController;

	class SigninController extends BaseController
	{
		public function index()
		{
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