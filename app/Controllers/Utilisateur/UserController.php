<?php
	namespace App\Controllers\Utilisateur;
	use App\Models\UserModel;
	use App\Controllers\BaseController;

	class UserController extends BaseController
	{
		public function profile()
		{
			$session = session();
			$userModel = new UserModel();
			
			// Récupère l'utilisateur connecté
			$userId = $session->get('id_user');
			$user   = $userModel->find($userId);
			
			if (!$user)
			{
				return redirect()->to('/signin')->with('error', 'Utilisateur non trouvé.');
			}

			// Passe l'utilisateur à la vue
			return view('Utilisateur/profile', ['user' => $user]);
		}
	}
?>