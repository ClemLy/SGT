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
			echo view('Utilisateur/signin');
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
						'id_user'         => $data['id_user'],
						'nom_user'       => $data['nom_user'],
						'prenom_user'       => $data['prenom_user'],
						'email_user'      => $data['email_user'],
						'isLoggedIn' => TRUE
					];

					$session->set($ses_data);
					return redirect()->to('/profile');
				}
				else
				{
					$session->setFlashdata('msg', 'Mot de passe incorrect.');
					return redirect()->to('/signin');
				}
			}
			else
			{
				$session->setFlashdata('msg', 'Email exite pas.');
				return redirect()->to('/signin');
			}
		}
	}
?>