<?php
setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');
echo view('commun/header', ['pageTitle' => 'Gestion des Tâches']);
?>

<body>
	<!-- Navbar -->
	<?php require 'navbar.php'; ?>

	<!-- Popup -->
	<?php require 'popup.php'; ?>

	<!-- Contenu Principal -->
	<div class="content m-3 w-100">
		<h1 class="mb-5">Gestion des Tâches</h1>

		<!-- Vue Tableau -->
		<div id="tableauView" >
			<div class="row w-100 d-flex justify-content-between" style="height:100vh">
				<?php require 'tableau.php'; ?>
			</div>
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
							<input type="text" class="form-control" name="titre" id="edit_titre" required>
						</div>
						<div class="mb-3">
							<label for="edit_description" class="form-label">Description</label>
							<textarea class="form-control" name="description_tache" id="edit_description"></textarea>
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
								type="text" 
								class="form-control" 
								name="categorie" 
								id="edit_categorie" 
								list="categories-list" 
								placeholder="Choisissez ou ajoutez une catégorie" 
								oninput="checkAddCategory(this.value)" 
								onchange="addNewCategory(this.value)">
							<datalist id="categories-list">
								<?php foreach ($categories as $categorie): ?>
									<option value="<?= esc($categorie['titre_categorie']); ?>"></option>
								<?php endforeach; ?>
							</datalist>
						</div>
						<button type="submit" class="btn btn-primary">Mettre à jour</button>
					</form>
				</div>
			</div>
		</div>
	</div>

</body>

</html>