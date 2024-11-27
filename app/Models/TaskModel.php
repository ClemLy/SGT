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

	public function getTasksWithCategoriesByStatus($etatTache)
	{
		return $this->select('tache.*, categorie.titre_categorie')
			->join('categorie', 'tache.id_categorie = categorie.id_categorie', 'left')
			->where('etat_tache', $etatTache) // Condition sur l'état de la tâche
			->where('id_user', session()->get('id_user')) // Condition sur l'utilisateur
			->findAll();
	}
    public function markAsCompleted($id)
    {
        return $this->update($id, ['etat_tache' => 'Terminée']);
    }

	public function getPaginatedTasks($perPage, $page)
	{
		return $this->paginate($perPage, 'default', $page);
	}
}