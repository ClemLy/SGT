<?php
	namespace App\Controllers\Tache;

	use App\Models\CommentModel;
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
		
			// Retourner une réponse JSON avec succès explicite
			return $this->response->setJSON([
				'success' => true,
				'message' => 'Commentaire ajouté avec succès.',
				'comment' => $data
			]);
		}

		// Supprime un commentaire
		public function delete($id_commentaire)
		{
			$model = new CommentModel();
			$comment = $model->find($id_commentaire);
		
			if ($comment)
			{
				$model->delete($id_commentaire);
		
				// Retourner une réponse JSON avec succès explicite
				return $this->response->setJSON([
					'success' => true,
					'message' => 'Commentaire supprimé avec succès.'
				]);
			}
			else
			{
				// Retourner une réponse JSON avec erreur explicite
				return $this->response->setJSON([
					'success' => false,
					'error'   => 'Commentaire non trouvé.'
				]);
			}
		}


		public function getPaginated($id_tache)
		{
			$model   = new CommentModel();
			$perPage = $this->request->getGet('perPage') ?? 3; // Nombre de commentaires par page
			$page    = $this->request->getGet('page') ?? 1;

			$comments = $model->getPaginatedCommentsByTask($id_tache, $perPage, $page);


			return $this->response->setJSON([
				'comments' => $comments,
				'pager'    => $model->pager->getDetails(),
			]);
			
		}

	}
?>