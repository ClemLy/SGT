<?php
	namespace App\Controllers\Tache;

	use App\Models\TaskModel;
	use App\Models\CategoryModel;
	use App\Controllers\BaseController;
	use Config\Pager;

	class TaskController extends BaseController
	{
		public function __construct()
		{
			// $this->sendReminders();
			// $this->page();
		}

		// Méthode pour afficher les tâches
		public function index()
		{
			// Chargement des modèles
			$categoryModel = new CategoryModel();
			$taskModel     = new TaskModel();

			// Récupération de toutes les catégories
			$categories = $categoryModel->getCategoriesByUser(session()->get('id_user'));

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


		public function sendReminders()
		{
			$taskModel = new TaskModel();
			$userModel = new \App\Models\UserModel();
			
			// Récupérer les tâches de l'utilisateur (ici, vous filtrez déjà par 'etats_tache')
			$tasks = $taskModel->where('etat_tache !=', 'Terminée')->findAll();

			// Filtrer les tâches dont l'échéance est dans moins de 48 heures
			$tasksDueSoon = [];
			$currentDate  = new DateTime();
			
			foreach ($tasks as $task)
			{
				$dueDate = new DateTime($task['echeance_tache']);
				
				// Calcul de la différence en secondes
				$secondsLeft   = $dueDate->getTimestamp() - $currentDate->getTimestamp();
				$isOverdue     = $dueDate < $currentDate;
				$isOverdueSoon = !$isOverdue && $secondsLeft <= 86400*2;

				// Affichage de la valeur de isOverdueSoon
        		log_message('error', "Task ID: " . $task['id_tache'] . " - isOverdueSoon: " . ($isOverdueSoon ? 'true' : 'false'));
				
				// Vérifier si la tâche arrive à échéance dans les 48 heures
				if ($isOverdueSoon)
				{
					$tasksDueSoon[] = $task;
				}
			}

			// Si des tâches arrivent à échéance dans moins de 48 heures, envoyer un email à l'utilisateur
			if (!empty($tasksDueSoon))
			{
				foreach ($tasksDueSoon as $task)
				{
					$this->sendReminderEmail($task);
				}
			}

			echo 'Rappels envoyés pour les tâches avec échéance dans moins de 48 heures.';
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

			echo 'Rappels envoyés pour les tâches se terminant dans moins de 24 heures.';
		}

		private function sendReminderEmail($task)
		{
			$userModel = new \App\Models\UserModel();
			$user = $userModel->find($task['id_user']); // Récupérer l'utilisateur lié à la tâche

			if ($user) 
			{
				$email = \Config\Services::email();
				$email->setFrom('XtrayShow@yahoo.fr', 'SGT');
				$email->setTo($user['email_user']); // Adresse e-mail de l'utilisateur
				$email->setSubject('Rappel : Tâche à échéance dans 2 jours');
				$email->setMessage("
					Bonjour {$user['prenom_user']},

					La tâche \"{$task['titre']}\" est prévue pour bientôt ({$task['echeance_tache']}).
					Merci de prendre les mesures nécessaires.

					Bonne journée !
				");

				if (!$email->send())
				{
					log_message('error', 'Erreur lors de l\'envoi de l\'email : ' . $email->printDebugger(['headers']));
				}
			}
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