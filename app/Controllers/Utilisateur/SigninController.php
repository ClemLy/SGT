<?php
	namespace App\Controllers;
	use CodeIgniter\Controller;
	use App\Models\UserModel;

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
			$email     = $this->request->getVar('email');
			$password  = $this->request->getVar('password');
			$data      = $userModel->where('email', $email)->first();
			
			if($data)
			{
				$pass = $data['password'];
				$authenticatePassword = password_verify($password, $pass);

				if($authenticatePassword)
				{
					$ses_data = [
						'id'         => $data['id'],
						'name'       => $data['name'],
						'email'      => $data['email'],
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