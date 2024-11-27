<?php
	namespace App\Controllers\Tache;

	use App\Models\TaskModel;
	use App\Models\CategoryModel;
	use App\Controllers\BaseController;

	class TaskController extends BaseController
	{
		// Méthode pour afficher les tâches
		public function index()
		{
			// Chargement des modèles
			$categoryModel = new CategoryModel();
			$taskModel     = new TaskModel();

			// Récupération de toutes les catégories
			$categories = $categoryModel->findAll();

			// Récupération des tâches par statut
			$tasksToDo       = $taskModel->getTasksWithCategoriesByStatus('À faire');
			$tasksInProgress = $taskModel->getTasksWithCategoriesByStatus('En cours');
			$tasksCompleted  = $taskModel->getTasksWithCategoriesByStatus('Terminée');

			// Retourner la vue avec les données nécessaires
			return view('Tache/index', [
				'categories'      => $categories,
				'tasksToDo'       => $tasksToDo,
				'tasksInProgress' => $tasksInProgress,
				'tasksCompleted'  => $tasksCompleted
			]);
		}

		// Méthode pour enregistrer une nouvelle tâche
		public function store()
		{
			helper(['form', 'url']);

			// Validation des données du formulaire
			$validation = \Config\Services::validation();
			$validation->setRules([
				'titre'             => 'required|min_length[3]|max_length[255]',
				'description_tache' => 'permit_empty|max_length[500]',
				'echeance_tache'    => 'required|valid_date',
				'etat_tache'        => 'required|in_list[À faire,En cours,Terminée]',
				'categorie'         => 'permit_empty|min_length[3]|max_length[255]',
			]);

			// Vérifier la validation du formulaire
			if (!$this->validate($validation->getRules()))
			{
				return redirect()->back()->with('errors', $this->validator->getErrors());
			}

			// Récupérer le titre de la catégorie depuis le formulaire
			$titreCategorie = $this->request->getPost('categorie');

			// Chercher la catégorie par titre
			$categoryModel = new CategoryModel();
			$category      = $categoryModel->where('titre_categorie', $titreCategorie)->first();

			// Si la catégorie n'existe pas, on la crée
			if (!$category)
			{
				$categoryModel->save(['titre_categorie' => $titreCategorie]);
				$idCategorie = $categoryModel->insertID();
			}
			else
			{
				$idCategorie = $category['id_categorie'];
			}

			// Préparer les données de la tâche à enregistrer
			$taskData = [
				'titre'             => $this->request->getPost('titre'),
				'description_tache' => $this->request->getPost('description_tache'),
				'etat_tache'        => $this->request->getPost('etat_tache'),
				'echeance_tache'    => $this->request->getPost('echeance_tache'),
				'id_user'           => session()->get('id_user'),  // Utilisation de l'utilisateur connecté
				'id_categorie'      => $idCategorie, // ID de la catégorie liée à la tâche
			];

			// Enregistrer la tâche dans la base de données
			$taskModel = new TaskModel();
			$taskModel->save($taskData);

			// Rediriger avec un message de succès
			return redirect()->to('/tasks')->with('success', 'Tâche créée avec succès');
		}

		public function update()
		{
			$taskModel     = new TaskModel();
			$categoryModel = new CategoryModel();
		
			// Récupération des données du formulaire
			$taskId         = $this->request->getPost('task_id');
			$titre          = $this->request->getPost('titre');
			$description    = $this->request->getPost('description_tache');
			$echeance       = $this->request->getPost('echeance_tache');
			$etat           = $this->request->getPost('etat_tache');
			$titreCategorie = $this->request->getPost('categorie');
		
			// Validation des données
			$validation = \Config\Services::validation();
			$validation->setRules([
				'titre'             => 'required|min_length[3]|max_length[255]',
				'description_tache' => 'permit_empty|max_length[500]',
				'echeance_tache'    => 'required|valid_date',
				'etat_tache'        => 'required|in_list[À faire,En cours,Terminée]',
				'categorie'         => 'permit_empty|min_length[3]|max_length[255]',
			]);
		
			if (!$this->validate($validation->getRules()))
			{
				return redirect()->back()->with('errors', $this->validator->getErrors());
			}
		
			// Vérification ou création de la catégorie
			$category = $categoryModel->where('titre_categorie', $titreCategorie)->first();
		
			if (!$category)
			{
				$categoryModel->save(['titre_categorie' => $titreCategorie]);
				$categoryId = $categoryModel->insertID();
			}
			else
			{
				$categoryId = $category['id_categorie'];
			}
		
			// Préparation des données pour la mise à jour
			$data = [
				'titre'             => $titre,
				'description_tache' => $description,
				'echeance_tache'    => $echeance,
				'etat_tache'        => $etat,
				'id_categorie'      => $categoryId,
			];
		
			// Mise à jour de la tâche
			if ($taskModel->update($taskId, $data))
			{
				return redirect()->to('/tasks')->with('success', 'Tâche mise à jour avec succès');
			}
			else
			{
				return redirect()->back()->with('error', 'Échec de la mise à jour de la tâche.');
			}
		}
        public function markAsCompleted($id)
        {
            $taskModel = new \App\Models\TaskModel();

            if ($taskModel->markAsCompleted($id)) {
                return redirect()->to('/tasks')->with('success', 'Tâche mise à jour avec succès');
            }

            return redirect()->back()->with('error', 'Erreur lors de la mise à jour.');
        }
        public function updateStatus()
        {
            $taskId = $this->request->getPost('id_tache');
            $newStatus = $this->request->getPost('etat_tache');

            if (!$taskId || !$newStatus) {
                return $this->response->setJSON([
                    'success' => false,
                    'message' => 'Données invalides.'
                ]);
            }

            $taskModel = new TaskModel();
            if ($taskModel->update($taskId, ['etat_tache' => $newStatus])) {
                return $this->response->setJSON([
                    'success' => true,
                    'message' => 'Statut mis à jour avec succès.',
                    'refresh' => true
                ]);
            }

            return $this->response->setJSON([
                'success' => false,
                'message' => 'Erreur lors de la mise à jour.'
            ]);
        }

		// Méthode pour supprimer une tâche
		public function delete($id)
		{
			$taskModel = new TaskModel();
			$taskModel->delete($id);

			// Rediriger avec un message de succès
			return redirect()->to('/tasks')->with('success', 'Tâche supprimée avec succès');
		}

		public function page()
		{
			$taskModel = new TaskModel();

			// Récupérer le nombre de tâches par page depuis la requête GET (par défaut 5)
			$perPage = $this->request->getGet('perPage') ?? 5;

			// Récupérer la page actuelle depuis la requête GET (par défaut 1)
			$page = $this->request->getGet('page') ?? 1;

			// Récupérer les tâches paginées
			$tasks = $taskModel->getPaginatedTasks($perPage, $page);
			
			// Charger la vue avec les tâches et les liens de pagination
			return view('Tache/tableur', [
				'tasks'   => $tasks,
				'pager'   => $taskModel->pager,
				'perPage' => $perPage
			]);
		}
	}
?>