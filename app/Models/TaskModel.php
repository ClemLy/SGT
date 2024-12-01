<?php
	namespace App\Models;
	use CodeIgniter\Model;

    class TaskModel extends Model
	{
        protected $table      = 'tache';
        protected $primaryKey = 'id_tache';

        protected $allowedFields = [
            'titre',
            'description_tache',
            'importance_tache',
            'etat_tache',
            'echeance_tache',
            'id_user',
            'id_categorie'
        ];

        public function getTasksWithCategoriesByStatus($etatTache , $criteria = 'echeance_tache', $order = 'asc', $searchQuery = '')
        {
            $validCriteria = ['titre', 'titre_categorie', 'echeance_tache', 'importance_tache'];
            $validOrders = ['asc', 'desc'];

            // Validation des entrées
            if (!in_array($criteria, $validCriteria)) {
                $criteria = 'echeance_tache'; // Par défaut : tri par échéance
            }
            if (!in_array(strtolower($order), $validOrders)) {
                $order = 'asc'; // Par défaut : ordre croissant
            }

            // Construction de la requête
            $builder = $this->db->table('tache')
                ->select('tache.*, categorie.titre_categorie')
                ->join('categorie', 'tache.id_categorie = categorie.id_categorie', 'left')
                ->where('tache.id_user', session()->get('id_user'));

            // Filtrer par état de tâche si spécifié
            if (!is_null($etatTache)) {
                $builder->where('tache.etat_tache', $etatTache);
            }

            // Ajouter la recherche si nécessaire
            if (!empty($searchQuery)) {
                $builder->groupStart()
                    ->like('tache.titre', $searchQuery)
                    ->orLike('categorie.titre_categorie', $searchQuery)
                    ->orLike('tache.description_tache', $searchQuery)
                    ->groupEnd();
            }

            // Appliquer le tri
            $builder->orderBy($criteria, $order);

            // Exécuter et retourner les résultats
            return $builder->get()->getResultArray();
        }

        public function getPaginatedTasks($perPage, $page, $criteria = 'date', $order = 'asc', $searchQuery = '')
        {
            $validCriteria = ['titre', 'titre_categorie', 'echeance_tache','importance_tache']; // Critères valides
            if (!in_array($criteria, $validCriteria)) {
                $criteria = 'echeance_tache'; // Par défaut : tri par date
            }

            $builder = $this->select('tache.*, categorie.titre_categorie')
                ->join('categorie', 'tache.id_categorie = categorie.id_categorie', 'left')
                ->where('tache.id_user', session()->get('id_user'));

            if (!empty($searchQuery)) {
                $builder->groupStart()
                    ->like('tache.titre', $searchQuery)
                    ->orLike('categorie.titre_categorie', $searchQuery)
                    ->orLike('tache.description_tache', $searchQuery)
                    ->groupEnd();
            }

            // Appliquer le tri
            $builder->orderBy($criteria, $order);

            // Pagination
            return $builder->paginate($perPage, 'default', $page);
        }
        

        public function getTotalTasks($searchQuery = '')
        {
            $builder = $this->db->table('tache')
                ->where('tache.id_user', session()->get('id_user'));

            if (!empty($searchQuery)) {
                $builder->groupStart()
                    ->like('tache.titre', $searchQuery)
                    ->orLike('categorie.titre_categorie', $searchQuery)
                    ->orLike('tache.description_tache', $searchQuery)
                    ->groupEnd();
            }

            return $builder->countAllResults();
        }
        


        public function getAllTaskCount($searchQuery = '')
        {
            $builder = $this->db->table('tache')
                ->where('tache.id_user', session()->get('id_user'));

            if (!empty($searchQuery)) {
                $builder->groupStart()
                    ->like('tache.titre', $searchQuery)
                    ->orLike('categorie.titre_categorie', $searchQuery)
                    ->orLike('tache.description_tache', $searchQuery)
                    ->groupEnd();
            }

            return $builder->countAllResults();
        }

        public function markAsCompleted($id)
        {
            return $this->update($id, ['etat_tache' => 'Terminée']);
        }

        public function getTasksDueIn48Hours($userId)
        {
            $db = \Config\Database::connect();
            $query = $db->query("SELECT * FROM get_tasks_due_in_48_hours(?)", [$userId]);
            return $query->getResultArray();
        }

        public function getTaskCount($status, $searchQuery = '')
        {
            $builder = $this->builder()->where('etat_tache', $status);
            $builder ->where('id_user', session()->get('id_user'));

            if (!empty($searchQuery)) {
                $builder->groupStart()
                    ->like('titre', $searchQuery)
                    ->orLike('description_tache', $searchQuery)
                    ->orLike('titre_categorie', $searchQuery)
                    ->groupEnd();
            }

            return $builder->countAllResults();
        }

        public function getSearch($status, $searchQuery = '')
        {
            $builder = $this->select('tache.*, categorie.titre_categorie')
                ->join('categorie', 'tache.id_categorie = categorie.id_categorie', 'left')
                ->where('etat_tache', $status)
                ->where('id_user', session()->get('id_user'));

            if (!empty($searchQuery)) {
                $builder->groupStart()
                    ->like('titre', $searchQuery)
                    ->orLike('titre_categorie', $searchQuery)
                    ->orLike('description_tache', $searchQuery)
                    ->groupEnd();
            }

            return $builder->get()->getResultArray();
        }
}