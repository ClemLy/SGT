<?php
	namespace App\Controllers\Utilisateur;
	use CodeIgniter\Controller;
	use App\Models\UserModelB;
	use App\Controllers\BaseController;

	class ForgotPasswordController extends Controller
	{
		public function index()
		{
			helper(['form']);
			return view('Utilisateur/forgot_password');
		}

		public function sendResetLink()
		{
			$email = $this->request->getPost('email_user');
			$userModel = new UserModelB();
			$user = $userModel->where('email_user', $email)->first();

			// Dans la méthode sendResetLink du contrôleur ForgotPasswordController
			$email = $this->request->getPost('email_user');
			echo 'Adresse e-mail soumise : ' . $email;

			if ($user)
			{
				// Générer un jeton de réinitialisation de MDP et enregistrer-le dans BD
				$token = bin2hex(random_bytes(16));
				$expiration = date('Y-m-d H:i:s', strtotime('+1 hour'));
				$userModel->set('reset_token', $token)
				->set('reset_token_exp', $expiration)
				->update($user['id_user']);

				// Envoyer l'e-mail avec le lien de réinitialisation
				$resetLink = site_url("reset-password/$token");
				$message = "Cliquez sur le lien suivant pour réinitialiser votre mot de passe : $resetLink";
				
				// Utilisez la classe Email de CodeIgniter pour envoyer l'e-mail
				$emailService = \Config\Services::email();
				
				//paramètres du mail
				$from ='XtrayShow@yahoo.fr';
				$to = $this->request->getPost('to');
				$subject = $this->request->getPost('subject');
				
				//envoi du mail
				$emailService->setTo($email);
				$emailService->setFrom($from);
				$emailService->setSubject('Réinitialisation de mot de passe');
				$emailService->setMessage($message);
				
				if ($emailService->send())
				{
					echo ' | E-mail envoyé avec succès.';
				}
				else
				{
					echo $emailService->printDebugger();
				}
			} 
			else
			{
				echo ' | Adresse e-mail non valide.';
			}
		}
	}
?>