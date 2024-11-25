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
			$data['utilisateur'] = $userModel->find($session->get('id'));
			echo view('Utilisateur/profile', $data);
		}


		public function updateProfile()
		{
			$session = session();
			$userModel = new UserModel();

			$data = [
				'name' => $this->request->getVar('name'),
				'email' => $this->request->getVar('email')
			];

			$userModel->update($session->get('id'), $data);
			return redirect()->to('/profile');
		}
	}
?>