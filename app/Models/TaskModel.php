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
            'etat_tache',
            'echeance_tache',
            'id_user',
            'id_categorie'
        ];

        public function getTasksWithCategoriesByStatus($etatTache, $searchQuery = '')
        {
            $builder = $this->select('tache.*, categorie.titre_categorie')
                ->join('categorie', 'tache.id_categorie = categorie.id_categorie', 'left')
                ->where('etat_tache', $etatTache)
                ->where('id_user', session()->get('id_user'));

            if (!empty($searchQuery)) {
                $builder->groupStart()
                    ->like('titre', $searchQuery)
                    ->orLike('titre_categorie', $searchQuery)
                    ->orLike('description_tache', $searchQuery)
                    ->groupEnd();
            }

            return $builder->findAll();
        }


        public function getPaginatedTasks($perPage, $offset, $searchQuery = '')
        {
            // Convertir les paramètres pour s'assurer qu'ils sont valides
            $offset = max((int)$offset, 0); // Assure que l'offset est au minimum 0
            $perPage = max((int)$perPage, 1); // Assure qu'au moins une tâche est récupérée

            $builder = $this->db->table('tache')
                ->select('tache.*, categorie.titre_categorie')
                ->join('categorie', 'tache.id_categorie = categorie.id_categorie', 'left')
                ->where('tache.id_user', session()->get('id_user'));

            // Appliquer la recherche si une requête est fournie
            if (!empty($searchQuery)) {
                $builder->groupStart()
                    ->like('tache.titre', $searchQuery)
                    ->orLike('categorie.titre_categorie', $searchQuery)
                    ->orLike('tache.description_tache', $searchQuery)
                    ->groupEnd();
            }

            // Appliquer les limites de pagination
            $builder->limit($perPage, $offset);

            // Récupérer les résultats
            $results = $builder->get()->getResultArray();

            return $results ?: []; // Retourner un tableau vide si aucun résultat
        }
        public function getAllTasksPaginated($perPage, $offset, $searchQuery = '')
        {
            $offset = (int)$offset;
            $perPage = (int)$perPage;

            $builder = $this->db->table('tache')
                ->select('tache.*, categorie.titre_categorie')
                ->join('categorie', 'tache.id_categorie = categorie.id_categorie', 'left')
                ->where('tache.id_user', session()->get('id_user'))
                ->limit($perPage, $offset);

            if (!empty($searchQuery)) {
                $builder->groupStart()
                    ->like('tache.titre', $searchQuery)
                    ->orLike('categorie.titre_categorie', $searchQuery)
                    ->orLike('tache.description_tache', $searchQuery)
                    ->groupEnd();
            }

            return $builder->get()->getResultArray();
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