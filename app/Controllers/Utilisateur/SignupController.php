<?php
	namespace App\Controllers;
	use App\Models\UserModel;

	class SignupController extends BaseController
	{
		public function index()
		{
			helper(['form']);
			echo view('Utilisateur/signup');
		}

		public function store()
		{
			helper(['form']);
			$rules = [
				'name'            => 'required|min_length[2]|max_length[50]',
				'email'           => 'required|min_length[4]|max_length[100]|valid_email|is_unique[users.email]',
				'password'        => 'required|min_length[4]|max_length[50]',
				'confirmpassword' => 'matches[password]'
			];

			if ($this->validate($rules))
			{
				$userModel = new UserModel();

				$data = [
					'name'            => $this->request->getVar('name'),
					'email'           => $this->request->getVar('email'),
					'password'        => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
					'is_active'       => 0, // Par défaut, non activé
					'activation_code' => bin2hex(random_bytes(16)) // Code unique
				];

				$userModel->save($data);

				// Envoyer l'email
				$email = \Config\Services::email();
				$email->setFrom('no-reply@yourdomain.com', 'Task Manager');
				$email->setTo($data['email']);
				$email->setSubject('Activate your account');
				$email->setMessage('Click here to activate your account: ' . site_url('activate/' . $data['activation_code']));
				$email->send();

				return redirect()->to('/signin');
			}
			else
			{
				$data['validation'] = $this->validator;
				echo view('signup', $data);
			}
		}
	}
?>