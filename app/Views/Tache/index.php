<?php
setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');
echo view('commun/header', ['pageTitle' => 'Gestion des Tâches']);
function cleanUrl($newParams = []) {
    $queryParams = $_GET; // Récupère les paramètres actuels
    $queryParams = array_merge($queryParams, $newParams); // Met à jour les nouveaux paramètres
    return current_url() . '?' . http_build_query($queryParams); // Reconstruit l'URL proprement
}
?>



<body>
	<!-- Navbar -->
	<?php require 'navbar.php'; ?>

	<!-- Popup -->
	<?php require 'popup.php'; ?>

	<!-- Contenu Principal -->
	<div class="content m-3 w-100">
        <div class="d-flex justify-content-between mb-3">
            <input type="text" id="taskSearchInput" class="form-control w-25" placeholder="Rechercher une tâche..." oninput="searchTasks()">
        </div>
        <div class="d-flex justify-content-between ">
            <h1 class="mb-5">Gestion des Tâches</h1>
            <div class="dropdown">
                <button class="btn dropdown-toggle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                    Trier par
                </button>
                <ul class="dropdown-menu">
                    <li>
                        <a href="<?= cleanUrl(['criteria' => 'titre', 'order' => ($criteria === 'titre' && $order === 'asc') ? 'desc' : 'asc']); ?>">
                            Trier par Titre
                        </a>
                    </li>
                    <li>
                        <a href="<?= current_url() . '?' . http_build_query(array_merge($_GET, ['criteria' => 'echeance_tache', 'order' => ($criteria === 'echeance_tache' && $order === 'asc') ? 'desc' : 'asc'])); ?>" class="dropdown-item">
                            Date <?= ($criteria === 'echeance_tache') ? ($order === 'asc' ? '↑' : '↓') : ''; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?= current_url() . '?' . http_build_query(array_merge($_GET, ['criteria' => 'titre_categorie', 'order' => ($criteria === 'titre_categorie' && $order === 'asc') ? 'desc' : 'asc'])); ?>" class="dropdown-item">
                            Catégorie <?= ($criteria === 'titre_categorie') ? ($order === 'asc' ? '↑' : '↓') : ''; ?>
                        </a>
                    </li>
                    <li>
                        <a href="<?= current_url() . '?' . http_build_query(array_merge($_GET, ['criteria' => 'importance_tache', 'order' => ($criteria === 'importance_tache' && $order === 'asc') ? 'desc' : 'asc'])); ?>" class="dropdown-item">
                            Importance <?= ($criteria === 'importance_tache') ? ($order === 'asc' ? '↑' : '↓') : ''; ?>
                        </a>
                    </li>
                </ul>
            </div>
        </div>

		<!-- Vue Tableau -->
		<div id="tableauView" >
				<?php require 'tableau.php'; ?>
		</div>

		<!-- Vue Tableur -->
		<div id="tableurView" style="display: none;">
			<?php require 'tableur.php'; ?>
		</div>
	</div>

	<!-- Modal de création et édition de tâche -->
	<div class="modal fade" id="taskModal" tabindex="-1" aria-labelledby="taskModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="taskModalLabel">Créer une Tâche</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form action="<?= site_url('tasks/store'); ?>" method="POST">
						<?= csrf_field(); ?>
						<div class="mb-3">
							<label for="titre" class="form-label">Titre</label>
							<input type="text" class="form-control" name="titre" id="titre" required>
						</div>
						<div class="mb-3">
							<label for="description_tache" class="form-label">Description</label>
							<textarea class="form-control" name="description_tache" id="description_tache"></textarea>
							<div id="charCount" class="mt-2 text-muted">0/100</div>
						</div>
						<div class="mb-3">
						<label for="importance_tache" class="form-label">Importance</label>
							<select class="form-control" name="importance_tache" id="importance_tache" required>
								<option value="Faible">Faible</option>
								<option value="Modéré">Modéré</option>
								<option value="Fort">Fort</option>
							</select>
						</div>
						<div class="mb-3">
							<label for="echeance_tache" class="form-label">Échéance</label>
							<input type="date" class="form-control" name="echeance_tache" id="echeance_tache" required>
						</div>
						<div class="mb-3">
							<label for="etat_tache" class="form-label">Statut</label>
							<select class="form-control" name="etat_tache" id="etat_tache" required>
								<option value="À faire">À faire</option>
								<option value="En cours">En cours</option>
								<option value="Terminée">Terminée</option>
							</select>
						</div>
						<div class="mb-3">
							<label for="categorie" class="form-label">Catégorie</label>
							<input type="text" class="form-control" name="categorie" id="categorie" list="categories-list">
							<datalist id="categories-list">
								<?php foreach ($categories as $categorie): ?>
									<option value="<?= esc($categorie['titre_categorie']); ?>"></option>
								<?php endforeach; ?>
							</datalist>
						</div>
                        <input type="hidden" id="currentView" name="current_view" value="tableau">
                        <button type="submit" class="btn btn-primary">Créer</button>
					</form>
				</div>
			</div>
		</div>
	</div>

	<!-- Modal de modification -->
	<div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="editTaskModalLabel">Modifier la Tâche</h5>
					<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
				</div>
				<div class="modal-body">
					<form action="<?= site_url('tasks/update'); ?>" method="POST">
						<?= csrf_field(); ?>
						<input type="hidden" name="task_id" id="task_id">
						<div class="mb-3">
							<label for="edit_titre" class="form-label">Titre</label>
							<input autocomplete="off" type="text" class="form-control" name="titre" id="edit_titre" required>
						</div>
						<div class="mb-3">
							<label for="edit_description" class="form-label">Description</label>
							<textarea class="form-control" name="description_tache" id="edit_description"></textarea>
							<div id="charCount" class="mt-2 text-muted">0/100</div>
						</div>
						<div class="mb-3">
							<label for="edit_importance_tache" class="form-label">Importance</label>
							<select class="form-control" name="importance_tache" id="edit_importance_tache" required>
								<option value="Faible">Faible</option>
								<option value="Modéré">Modéré</option>
								<option value="Fort">Fort</option>
							</select>
						</div>
						<div class="mb-3">
							<label for="edit_echeance" class="form-label">Échéance</label>
							<input type="date" class="form-control" name="echeance_tache" id="edit_echeance" required>
						</div>
						<div class="mb-3">
							<label for="edit_etat_tache" class="form-label">Statut</label>
							<select class="form-control" name="etat_tache" id="edit_etat_tache" required>
								<option value="À faire">À faire</option>
								<option value="En cours">En cours</option>
								<option value="Terminée">Terminée</option>
							</select>
						</div>
						<div class="mb-3">
							<label for="edit_categorie" class="form-label">Catégorie</label>
							<input 
								autocomplete="off"
								type="text" 
								class="form-control" 
								name="categorie" 
								id="edit_categorie" 
								list="categories-list" 
								placeholder="Choisissez ou ajoutez une catégorie">
							<datalist id="categories-list">
								<?php foreach ($categories as $categorie): ?>
									<option value="<?= esc($categorie['titre_categorie']); ?>"></option>
								<?php endforeach; ?>
							</datalist>
						</div>
                        <input type="hidden" name="redirect_url" value="<?= current_url() . '#' . ($viewMode ?? 'tableau'); ?>">
                        <button type="submit" class="btn btn-primary">Mettre à jour</button>
					</form>
				</div>
			</div>
		</div>
	</div>



	<div class="modal fade" id="deleteAccountModal" tabindex="-1" aria-labelledby="deleteAccountModalLabel" aria-hidden="true">
		<div class="modal-dialog">
			<div class="modal-content">
			<div class="modal-header">
				<h5 class="modal-title" id="deleteAccountModalLabel">Confirmer la suppression de votre compte</h5>
				<button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
			</div>
			<div class="modal-body">
				<p>Vouslez vous vraiment supprimer votre compte ? Cette action est irréversible.</p>
			</div>
			<div class="modal-footer">
				<button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Annuler</button>
				<!-- Formulaire pour supprimer le compte -->
				<form action="<?= site_url('profile/delete'); ?>" method="POST">
				<button type="submit" class="btn btn-danger" name="delete_account">Supprimer le compte</button>
				</form>
			</div>
			</div>
		</div>
	</div>

    <script>
        let searchTimeout; // Variable pour gérer le délai
        let isFetching = false; // État pour savoir si une requête est en cours
        let lastSearchValue = ""; // Dernière valeur de recherche à envoyer

        function searchTasks() {
            const searchValue = document.getElementById('taskSearchInput').value;

            // Stocke la dernière valeur de recherche
            lastSearchValue = searchValue;

            // Annule tout timeout précédent
            clearTimeout(searchTimeout);

            // Démarre un nouveau timeout
            searchTimeout = setTimeout(() => {
                // Si une requête est déjà en cours, on attend qu'elle soit terminée
                if (isFetching) {
                    return;
                }

                // Indique qu'une requête est en cours
                isFetching = true;

                fetch(`<?= site_url('tasks/searchTasks'); ?>?search=${encodeURIComponent(lastSearchValue)}`, {
                    method: 'GET',
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                    .then(response => response.json())
                    .then(data => {
                        // Mise à jour de la vue Kanban (tableau.php)
                        const taskColumns = document.getElementById('taskColumns');
                        if (data.kanban && taskColumns) {
                            taskColumns.innerHTML = data.kanban; // Remplacement complet
                        }

                        // Mise à jour de la vue Tableur (tableur.php)
                        const tableBody = document.getElementById('tableTasksBody');
                        if (data.table && tableBody) {
                            tableBody.innerHTML = data.table; // Remplacement complet
                        }

                        // Réinitialise l'état une fois la requête terminée
                        isFetching = false;

                        // Si la valeur de recherche a changé pendant la requête, relance la recherche
                        if (lastSearchValue !== searchValue) {
                            searchTasks();
                        }
                    })
                    .catch(error => {
                        console.error('Erreur:', error);
                        isFetching = false; // Réinitialise même en cas d'erreur
                    });
            }, 1000);
        }

        function toggleSort(criteria) {
            const url = new URL(window.location.href); // Récupère l'URL actuelle
            const currentOrder = url.searchParams.get('order') || 'asc'; // Récupère l'ordre actuel

            // Inverser l'ordre si le critère reste le même
            const newOrder = (url.searchParams.get('criteria') === criteria && currentOrder === 'asc') ? 'desc' : 'asc';

            // Mettre à jour les paramètres dans l'URL
            url.searchParams.set('criteria', criteria);
            url.searchParams.set('order', newOrder);

            // Éviter de dupliquer les autres paramètres (comme `searchQuery` ou `perPage`)
            window.location.href = url.toString(); // Redirige vers la nouvelle URL
        }



    </script>
</body>

</html>
