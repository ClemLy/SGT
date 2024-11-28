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

    public function getPaginatedTasks($perPage, $page)
    {
        return $this->select('tache.*, categorie.titre_categorie')
            ->join('categorie', 'tache.id_categorie = categorie.id_categorie', 'left')
            ->where('id_user', session()->get('id_user'))
            ->paginate($perPage, 'default', $page);
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