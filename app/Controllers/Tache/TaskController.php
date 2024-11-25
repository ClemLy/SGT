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
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->findAll();

        $taskModel = new TaskModel();
        $tasksToDo = $taskModel->getTasksWithCategoriesByStatus('À faire');
        $tasksInProgress = $taskModel->getTasksWithCategoriesByStatus('En cours');
        $tasksCompleted = $taskModel->getTasksWithCategoriesByStatus('Terminée');


        echo view('Tache/index', [
            'categories' => $categories,
            'tasksToDo' => $tasksToDo,
            'tasksInProgress' => $tasksInProgress,
            'tasksCompleted' => $tasksCompleted        
        ]);
    }

    // Méthode pour afficher le modal de création
    public function create()
    {
        // Récupérer les catégories disponibles pour l'utilisateur afin de les afficher dans le formulaire
        $categoryModel = new CategoryModel();
        $categories = $categoryModel->findAll();

        return view('task/create', ['categories' => $categories]);
    }

    // Méthode pour enregistrer une nouvelle tâche
    public function store()
    {
        helper(['form', 'url']);
    
        // Validation des données du formulaire
        $validation = \Config\Services::validation();
        $validation->setRules([
            'titre' => 'required|min_length[3]|max_length[255]',
            'description_tache' => 'permit_empty|max_length[500]',
            'echeance_tache' => 'required|valid_date',
            'etat_tache' => 'required|in_list[À faire,En cours,Terminée]',
            'categorie' => 'required|min_length[3]|max_length[255]',  // Validation du titre_categorie
        ]);
    
        // Vérifier la validation du formulaire
        if (!$this->validate($validation->getRules())) {
            return redirect()->back()->with('errors', $this->validator->getErrors());
        }
    
        // Récupérer le titre de la catégorie depuis le formulaire
        $titreCategorie = $this->request->getPost('categorie');
    
        // Chercher la catégorie par titre
        $categoryModel = new CategoryModel();
        $category = $categoryModel->where('titre_categorie', $titreCategorie)->first();  // Vérifier si la catégorie existe
    
        // Si la catégorie n'existe pas, on la crée
        if (!$category) {
            $categoryModel->save(['titre_categorie' => $titreCategorie]);
    
            $idCategorie = $categoryModel->insertID();  // Obtenir l'ID de la catégorie ajoutée
        } else {
            $idCategorie = $category['id_categorie'];
        }
    
        $taskData = [
            'titre' => $this->request->getPost('titre'),
            'description_tache' => $this->request->getPost('description_tache'),
            'etat_tache' => $this->request->getPost('etat_tache'),
            'echeance_tache' => $this->request->getPost('echeance_tache'),
            'id_user' => session()->get('id_user'), 
            'id_categorie' => $idCategorie,  // Enregistrer l'ID de la catégorie
        ];
    
        $taskModel = new TaskModel();
        $taskModel->save($taskData);
    
        return redirect()->to('/tasks')->with('success', 'Tâche créée avec succès');
    }
    
}
