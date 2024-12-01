<?php
	namespace App\Controllers\Tache;

	use App\Models\TaskModel;
	use App\Models\UserModel;
	use App\Models\CategoryModel;
	use App\Models\CommentModel;
	use App\Controllers\BaseController;
	use Config\Pager;
	use DateTime;

	setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');

    class TaskController extends BaseController
	{
		public function __construct()
		{
			$this->sendRemindersForTasksDueIn48Hours();
		}

		// Méthode pour afficher les tâches
        public function index()
        {
            // Chargement des services et modèles
            $pager = \Config\Services::pager();
            $taskModel     = new TaskModel();
            $categoryModel = new CategoryModel();
			$commentModel  = new CommentModel();

            // Gestion des paramètres de pagination
            $perPage = (int) ($this->request->getGet('perPage') ?? 10);

            $currentPage = (int) ($this->request->getGet('page') ?? 1);
            $currentPage = $currentPage > 0 ? $currentPage : 1;


            // Gestion des critères et de l'ordre de tri
            $criteria = $this->request->getGet('criteria') ?? 'echeance_tache'; // Critère par défaut
            $order = $this->request->getGet('order') ?? 'asc';                 // Ordre par défaut

            // Gestion de la recherche
            $searchQuery = $this->request->getGet('search') ?? '';

            // Calcul des totaux par statut
            $totalTasksToDo = $taskModel->getTaskCount('À faire', $searchQuery);
            $totalTasksInProgress = $taskModel->getTaskCount('En cours', $searchQuery);
            $totalTasksCompleted = $taskModel->getTaskCount('Terminée', $searchQuery);
            $totalTasks = $totalTasksToDo + $totalTasksInProgress + $totalTasksCompleted;

            if($perPage==0)
            {
                $tasks =$taskModel->getTasksWithCategoriesByStatus(null, $criteria,$order,$searchQuery);
            }

            else{
                $tasks = $taskModel->getPaginatedTasks($perPage, $currentPage, $criteria, $order, $searchQuery);
            }


            if ($perPage > 0) {
                // Génère les liens de pagination seulement si perPage est supérieur à 0
                $pagerLinks = $pager->makeLinks($currentPage, $perPage, $totalTasks);
            } else {
                // Pas de pagination, car toutes les tâches sont affichées
                $pagerLinks = '';
            }
            // Récupération des catégories et tâches par statut (pour affichage classique)
            $categories = $categoryModel->findAll();
            $tasksToDo = $taskModel->getTasksWithCategoriesByStatus('À faire', $criteria,$order,$searchQuery);
            $tasksInProgress = $taskModel->getTasksWithCategoriesByStatus('En cours', $criteria,$order, $searchQuery);
            $tasksCompleted = $taskModel->getTasksWithCategoriesByStatus('Terminée', $criteria,$order, $searchQuery);

			// Ajouter le nombre de commentaires pour chaque tâche
			foreach ($tasksToDo as &$task)
			{
				$task['comment_count'] = $commentModel->getCommentCountByTask($task['id_tache']);
			}

			foreach ($tasksInProgress as &$task)
			{
				$task['comment_count'] = $commentModel->getCommentCountByTask($task['id_tache']);
			}

			foreach ($tasksCompleted as &$task)
			{
				$task['comment_count'] = $commentModel->getCommentCountByTask($task['id_tache']);
			}

            // Vérifier si la requête est AJAX
            if ($this->request->isAJAX()) {
                // Générer le tableau des tâches (vue partielle)
                $tasksTableur = view('Tache/tableur', [
                    'tasks' => $tasks,
                    'currentPage' => $currentPage,
                    'criteria' => $criteria,
                    'order' => $order,
                    'searchQuery' => $searchQuery,
                    'pagerLinks' => $pagerLinks,
                    'tasksToDo' => $tasksToDo,
                    'tasksInProgress' => $tasksInProgress,
                    'tasksCompleted' => $tasksCompleted,
                    'categories' => $categories,
                    'pager' => $pager,

                ]);

                $tasksTableau = view('Tache/tableau', [
                    'tasks' => $tasks,
                    'currentPage' => $currentPage,
                    'criteria' => $criteria,
                    'order' => $order,
                    'searchQuery' => $searchQuery,
                    'pagerLinks' => $pagerLinks,
                    'tasksToDo' => $tasksToDo,
                    'tasksInProgress' => $tasksInProgress,
                    'tasksCompleted' => $tasksCompleted,
                    'categories' => $categories,
                    'pager' => $pager,

                ]);

                return $this->response->setJSON([
                    'tasksTableau' => $tasksTableau ?? '<p>Aucune tâche trouvée.</p>',
                    'tasksTableur' => $tasksTableur ?? '<p>Aucune tâche trouvée.</p>',
                    'pagerLinks' => $pagerLinks ?? '',
                ]);
            }

            // Chargement de la vue complète
            return view('Tache/index', [
                'categories' => $categories,
                'tasksToDo' => $tasksToDo,
                'tasksInProgress' => $tasksInProgress,
                'tasksCompleted' => $tasksCompleted,
                'tasks' => $tasks,
                'perPage' => $perPage,
                'currentPage' => $currentPage,
                'criteria' => $criteria,
                'order' => $order,
                'searchQuery' => $searchQuery,
                'totalTasksToDo' => $totalTasksToDo,
                'totalTasksInProgress' => $totalTasksInProgress,
                'totalTasksCompleted' => $totalTasksCompleted,
                'pagerLinks' => $pagerLinks,
                'pager' => $pager,
                'totalTasks' => $totalTasks,


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
				'importance_tache'  => 'required|in_list[Faible,Modéré,Fort]',
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
			$titreCategorie    = $this->request->getPost('categorie');
			$id_user_categorie = session()->get('id_user');

			// Chercher la catégorie par titre
			$categoryModel = new CategoryModel();
			$category      = $categoryModel->where('titre_categorie', $titreCategorie)
										   ->where('id_user_categorie', $id_user_categorie)->first();

			// Si la catégorie n'existe pas, on la crée
			if ($category == NULL && !empty($titreCategorie))
			{
				$categoryModel->save([
					'titre_categorie'   => $titreCategorie,
					'id_user_categorie' => $id_user_categorie
				]);
				$idCategorie = $categoryModel->insertID();
			}
			else if ($category)
			{
				$idCategorie = $category['id_categorie'];
			}
			else
			{
				$idCategorie = NULL;
			}

			// Préparer les données de la tâche à enregistrer
			$taskData = [
				'titre'             => htmlspecialchars($this->request->getPost('titre')),
				'importance_tache'  => $this->request->getPost('importance_tache'),
				'description_tache' => htmlspecialchars($this->request->getPost('description_tache')),
				'etat_tache'        => $this->request->getPost('etat_tache'),
				'echeance_tache'    => $this->request->getPost('echeance_tache'),
				'id_user'           => session()->get('id_user'),  // Utilisation de l'utilisateur connecté
				'id_categorie'      => $idCategorie, // ID de la catégorie liée à la tâche
			];

			// Enregistrer la tâche dans la base de données
			$taskModel = new TaskModel();
			$taskModel->save($taskData);

            $currentView = $this->request->getPost('current_view') ?? 'tableau';
            return redirect()->to('/tasks#' . $currentView)->with('success', 'Tâche créée avec succès');
        }

		public function update()
		{
			$taskModel     = new TaskModel();
			$categoryModel = new CategoryModel();

			// Récupération des données du formulaire
			$taskId         = $this->request->getPost('task_id');
			$titre          = htmlspecialchars($this->request->getPost('titre'));
			$importance     = $this->request->getPost('importance_tache');
			$description    = htmlspecialchars($this->request->getPost('description_tache'));
			$echeance       = $this->request->getPost('echeance_tache');
			$etat           = $this->request->getPost('etat_tache');
			$titreCategorie = $this->request->getPost('categorie');

			// Validation des données
			$validation = \Config\Services::validation();
			$validation->setRules([
				'titre'             => 'required|min_length[3]|max_length[255]',
				'importance_tache'  => 'required|in_list[Faible,Modéré,Fort]',
				'description_tache' => 'permit_empty|max_length[100]',
				'echeance_tache'    => 'required|valid_date',
				'etat_tache'        => 'required|in_list[À faire,En cours,Terminée]',
				'categorie'         => 'permit_empty|min_length[3]|max_length[255]',
			]);

			if (!$this->validate($validation->getRules()))
			{
				return redirect()->back()->with('errors', $this->validator->getErrors());
			}

			// Récupérer le titre de la catégorie depuis le formulaire
			$titreCategorie    = $this->request->getPost('categorie');
			$id_user_categorie = session()->get('id_user');

			// Chercher la catégorie par titre
			$categoryModel = new CategoryModel();
			$category      = $categoryModel->where('titre_categorie', $titreCategorie)
										   ->where('id_user_categorie', $id_user_categorie)->first();

			// Si la catégorie n'existe pas, on la crée
			if ($category == NULL && !empty($titreCategorie))
			{
				$categoryModel->save([
					'titre_categorie'   => $titreCategorie,
					'id_user_categorie' => $id_user_categorie
				]);
				$idCategorie = $categoryModel->insertID();
			}
			else if ($category)
			{
				$idCategorie = $category['id_categorie'];
			}
			else
			{
				$idCategorie = NULL;
			}

			// Préparation des données pour la mise à jour
			$data = [
				'titre'             => $titre,
				'description_tache' => $description,
				'importance_tache'  => $importance,
				'echeance_tache'    => $echeance,
				'etat_tache'        => $etat,
				'id_categorie'      => $idCategorie,
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

            if ($taskModel->markAsCompleted($id))
			{
                return redirect()->to('/tasks')->with('success', 'Tâche mise à jour avec succès');
            }

            return redirect()->back()->with('error', 'Erreur lors de la mise à jour.');
        }
        public function searchTasks()
        {
            if (!$this->request->isAJAX()) {
                return redirect()->to('/tasks');
            }

            $searchQuery = $this->request->getGet('search') ?? '';

            $taskModel = new \App\Models\TaskModel();

            // Filtrer les tâches par statut et recherche
            $tasksToDo = $taskModel->getTasksWithCategoriesByStatus('À faire', $searchQuery);
            $tasksInProgress = $taskModel->getTasksWithCategoriesByStatus('En cours', $searchQuery);
            $tasksCompleted = $taskModel->getTasksWithCategoriesByStatus('Terminée', $searchQuery);

            // Charger les catégories (utilisées dans les vues)
            $categories = (new \App\Models\CategoryModel())->findAll();

            // Capturer la vue tableau.php (vue Kanban)
            ob_start();
            require(APPPATH . 'Views/Tache/tableau.php');
            $kanbanHtml = ob_get_clean();

            // Capturer la vue tableur.php
            ob_start();
            require(APPPATH . 'Views/Tache/tableur.php');
            $tableHtml = ob_get_clean();

            return $this->response->setJSON([
                'kanban' => $kanbanHtml, // Contenu HTML du tableau Kanban
                'table' => $tableHtml   // Contenu HTML du tableau
            ]);
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

		public function sendRemindersForTasksDueIn48Hours()
		{
			$taskModel = new TaskModel();
			$userId = session()->get('id_user');
			$tasksDueIn48Hours = $taskModel->getTasksDueIn48Hours($userId);

			foreach ($tasksDueIn48Hours as $task)
			{
				$this->sendReminderEmail($task);
			}
        }

		private function sendReminderEmail($task)
		{
			$userModel = new UserModel();
			$user = $userModel->where('id_user', session()->get('id_user'))->first();

			if ($user)
			{
				$dateEcheance = esc(strftime(
					(date('Y') === (new DateTime($task['echeance_tache']))->format('Y') ? '%A %d %B' : '%A %d %B %Y'),
					(new DateTime($task['echeance_tache']))->getTimestamp()
				));

				$email = \Config\Services::email();
				$email->setFrom('XtrayShow@yahoo.fr', 'TaskPlanner');
				$email->setTo($user['email_user']); // Adresse e-mail de l'utilisateur
				$email->setSubject('Rappel : Tâche à échéance dans 2 jours');
				$email->setMessage("
					Bonjour {$user['prenom_user']},

					la tâche \"{$task['titre']}\" est prévue pour bientôt ({$dateEcheance}).
					Merci de prendre les mesures nécessaires.

					Bonne journée !
				");

				if (!$email->send())
				{
					log_message('error', 'Erreur lors de l\'envoi de l\'email : ' . $email->printDebugger(['headers']));
				}
			}
		}

        public function getPaginatedTasks($limit, $offset, $searchQuery = '')
        {
            $builder = $this->db->table($this->table)
                ->select('tache.*, categorie.titre_categorie')
                ->join('categorie', 'tache.id_categorie = categorie.id_categorie', 'left')
                ->where('tache.id_user', session()->get('id_user'));

            if (!empty($searchQuery)) {
                $builder->groupStart()
                    ->like('tache.titre', $searchQuery)
                    ->orLike('tache.description_tache', $searchQuery)
                    ->orLike('categorie.titre_categorie', $searchQuery)
                    ->groupEnd();
            }

            return $builder->limit($limit, $offset)->get()->getResultArray();
        }

        public function getTaskCount($searchQuery = '')
        {
            $builder = $this->db->table($this->table)
                ->join('categorie', 'tache.id_categorie = categorie.id_categorie', 'left')
                ->where('tache.id_user', session()->get('id_user'));

            if (!empty($searchQuery)) {
                $builder->groupStart()
                    ->like('tache.titre', $searchQuery)
                    ->orLike('tache.description_tache', $searchQuery)
                    ->orLike('categorie.titre_categorie', $searchQuery)
                    ->groupEnd();
            }

            return $builder->countAllResults();
        }


	}
?>