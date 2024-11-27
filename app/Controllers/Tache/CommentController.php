<?php
	namespace App\Controllers\Tache;

	use App\Models\CommentModel;
	use CodeIgniter\Controller;
	use App\Controllers\BaseController;

	class CommentController extends BaseController
	{
		// Récupère les commentaires d'une tâche
		public function get($id_tache)
		{
			$model    = new CommentModel();
			$comments = $model->getCommentsByTask($id_tache);

			if (is_array($comments))
			{
				return $this->response->setJSON($comments);
			}
			else
			{
				return $this->response->setJSON(['error' => 'Aucun commentaire trouvé.']);
			}
		}

		// Ajoute un commentaire
		public function add()
		{
			$model = new CommentModel();
			$data = [
				'text_commentaire' => $this->request->getVar('text_commentaire'),
				'date_commentaire' => date('Y-m-d H:i:s'),
				'id_tache'         => $this->request->getVar('id_tache')
			];

			$model->insert($data);

			return $this->response->setJSON(['success' => true, 'message' => 'Commentaire ajouté avec succès.']);
		}
	}
?>