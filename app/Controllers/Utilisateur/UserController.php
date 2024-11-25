<?php
	namespace App\Controllers\Utilisateur;
	use App\Models\UserModel;
	use App\Controllers\BaseController;

	class UserController extends BaseController
	{
		public function profile()
		{
			$session   = session();
			$userModel = new UserModel();
			$data['utilisateur'] = $userModel->find($session->get('id_user'));
			echo view('Utilisateur/profile', $data);
		}


		public function updateProfile()
		{
			$session = session();
			$userModel = new UserModel();

			$data = [
				'nom_user'   => $this->request->getVar('nom_user'),
				'email_user' => $this->request->getVar('email_user')
			];

			$userModel->update($session->get('id_user'), $data);
			return redirect()->to('/profile');
		}
	}
?>