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
				'nom_user'        => 'required|min_length[2]|max_length[50]',
				'prenom_user'     => 'required|min_length[2]|max_length[50]',
				'email_user'      => 'required|min_length[4]|max_length[100]|valid_email|is_unique[users.email]',
				'password'        => 'required|min_length[4]|max_length[50]',
				'confirmpassword' => 'matches[password]'
			];

			if ($this->validate($rules))
			{
				$userModel = new UserModel();

				$data = [
					'nom_user'        => $this->request->getVar('nom_user'),
					'prenom_user'     => $this->request->getVar('prenom_user'),
					'email_user'      => $this->request->getVar('email_user'),
					'password'        => password_hash($this->request->getVar('password'), PASSWORD_DEFAULT),
					'activation_code' => bin2hex(random_bytes(16)) // Code unique
				];

				$userModel->save($data);

				// Envoyer l'email
				$email = \Config\Services::email();
				$email->setFrom('XtrayShow@yahoo.fr', 'SGT');
				$email->setTo($data['email_user']);
				$email->setSubject('Activation du compte');
				$email->setMessage('Cliquez sur ce lien pour activer votre compte : ' . site_url('activate/' . $data['activation_code']));
				$email->send();

				if ($email->send())
				{
					return redirect()->to('/signup')->with('message', 'Un email de vérification a été envoyé.');
				}
				else
				{
					return redirect()->to('/signup')->with('error', 'Erreur lors de l\'envoi de l\'email.');
				}
			}
			else
			{
				$data['validation'] = $this->validator;
				echo view('Utilisateur/signup', $data);
			}
		}

		public function activate($activation_code)
		{
			$userModel = new UserModel();

			// Recherche l'utilisateur avec le code d'activation
			$user = $userModel->where('activation_code', $activation_code)->first();

			if ($user)
			{
				// Mettre à jour l'utilisateur comme vérifié
				$userModel->set('is_verified', true)
						->set('activation_code', null) // Supprime le code d'activation
						->where('id_user', $user['id_user'])
						->update();

				return redirect()->to('/signin')->with('message', 'Votre compte a été activé avec succès.');
			}
			else
			{
				return redirect()->to('/signup')->with('error', 'Lien d\'activation invalide ou expiré.');
			}
		}
	}
?>