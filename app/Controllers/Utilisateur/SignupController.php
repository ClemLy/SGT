<?php
	namespace App\Controllers\Utilisateur;
	use App\Models\UserModel;
	use App\Controllers\BaseController;

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
				'nom_user'            => 'required|min_length[2]|max_length[50]',
				'prenom_user'            => 'required|min_length[2]|max_length[50]',
				'email_user'           => 'required|min_length[4]|max_length[100]|valid_email|is_unique[users.email]',
				'password'        => 'required|min_length[4]|max_length[50]',
				'confirmpassword' => 'matches[password]'
			];

			if ($this->validate($rules))
			{
				$userModel = new UserModel();

				$data = [
					'nom_user'            => $this->request->getVar('nom_user'),
					'prenom_user'            => $this->request->getVar('prenom_user'),
					'email_user'           => $this->request->getVar('email_user'),
					'password'        => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
					'activation_code' => bin2hex(random_bytes(16)) // Code unique
				];

				$userModel->save($data);

				// Envoyer l'email
				$email = \Config\Services::email();
				$email->setFrom('no-reply@yourdomain.com', 'Task Manager');
				$email->setTo($data['email_user']);
				$email->setSubject('Activate your account');
				$email->setMessage('Click here to activate your account: ' . site_url('activate/' . $data['activation_code']));
				$email->send();

				return redirect()->to('/signin');
			}
			else
			{
				$data['validation'] = $this->validator;
				echo view('Utilisateur/signup', $data);
			}
		}
	}
?>