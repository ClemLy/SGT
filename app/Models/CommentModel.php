<?php
	namespace App\Models;

	use CodeIgniter\Model;

	class CommentModel extends Model
	{
		protected $table         = 'commentaire';
		protected $primaryKey    = 'id_commentaire';
		protected $allowedFields = ['text_commentaire', 'date_commentaire', 'id_tache'];

		public function getCommentsByTask($id_tache)
		{
			$db = \Config\Database::connect();
			$query = $db->query("SELECT * FROM get_task_comments(?)", [$id_tache]);
			return $query->getResultArray();
		}
	}
?>