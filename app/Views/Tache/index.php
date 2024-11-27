<?php
setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr');
echo view('commun/header', ['pageTitle' => 'Gestion des Tâches']);
?>

<?php
function renderTaskColumn($title, $tasks, $statusId, $modalTarget)
{
	?>
	<div class="col-md-3 task-status p-3" ondragover="allowDrop(event)"
		ondrop="drop(event, '<?= esc($statusId); ?>')">
		<div class="d-flex justify-content-between align-items-center">
			<h3><?= esc($title); ?></h3>
			<button class="btn" data-bs-toggle="modal" data-bs-target="<?= $modalTarget ?>" onclick="setTaskStatus('<?= esc($title); ?>')">+</button>
		</div>
		<div id="<?= esc($statusId); ?>">
			<?php if (!empty($tasks)): ?>
				<?php foreach ($tasks as $task): ?>
					<?php

					$isOverdue = false;
					if (isset($task['echeance_tache']) && !empty($task['echeance_tache'])) {
						try {
							$dueDate = new DateTime($task['echeance_tache']);
							$currentDate = new DateTime();
							$isOverdue = $dueDate < $currentDate;
						} catch (Exception $e) {

							$isOverdue = false;
						}
					}
					?>
					<div class="card my-2 border-0 <?= $isOverdue ? 'overdue' : ''; ?> " draggable="true"
						ondragstart="drag(event)"
						data-task-id="<?= esc($task['id_tache']); ?>">
						<div class="card-body p-3">
							<div class="d-flex justify-content-between align-items-center w-100 mb-1">
								<p><small class="text-uppercase"><?= esc($task['titre_categorie'] ?? ''); ?></small></p>
								<div class="d-flex justify-content-between align-items-center">
									<p class="date border border-dark p-1" style="border-radius:15px">
										<?= esc(strftime(
											(date('Y') === (new DateTime($task['echeance_tache']))->format('Y') ? '%A %d %B' : '%A %d %B %Y'),
											(new DateTime($task['echeance_tache']))->getTimestamp()
										)); ?>
									</p>
									<button class="bi bi-three-dots btn btn-link ps-1 pe-1" data-bs-toggle="dropdown" aria-expanded="false"></button>
									<ul class="dropdown-menu">
										<li>
											<a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editTaskModal" onclick="loadTaskData(<?= $task['id_tache']; ?>, '<?= esc($task['titre']); ?>', '<?= esc($task['description_tache']); ?>', '<?= esc($task['echeance_tache']); ?>', '<?= esc($task['etat_tache']); ?>', '<?= esc($task['titre_categorie']); ?>')">Modifier</a>
										</li>
										<li>
											<a class="dropdown-item" href="<?= site_url('tasks/complete/' . $task['id_tache']); ?>" >Marquer comme terminée</a>
										</li>
										<li>
											<a class="dropdown-item" href="<?= site_url('tasks/delete/' . $task['id_tache']); ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?');">Supprimer</a>
										</li>
									</ul>
								</div>
							</div>
							<div style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#taskDetailModal"
								onclick="loadTaskDetails('<?= esc($task['titre']); ?>', '<?= esc($task['description_tache']); ?>', '<?= esc($task['echeance_tache']); ?>', '<?= esc($task['etat_tache']); ?>', '<?= esc($task['titre_categorie']); ?>', '<?= esc($task['id_tache']); ?>')">
								<h3 class="card-title mb-2"><?= esc($task['titre']); ?></h3>
								<p class="card-text"><?= esc($task['description_tache']); ?></p>
							</div>
						</div>
					</div>
				<?php endforeach; ?>
			<?php else: ?>
				<p>Aucune tâche <?= strtolower($title); ?>.</p>
			<?php endif; ?>
		</div>
	</div>
	<?php
}
?>

<?php require 'navbar.php' ?>
<?php require 'popup.php'; ?>

<body>
<script>
function loadTaskData(taskId, titre, description, echeance, etat, categorie)
{
	document.getElementById('task_id').value            = taskId;
	document.getElementById('edit_titre').value         = titre;
	document.getElementById('edit_description').value   = description;
	document.getElementById('edit_echeance').value      = echeance;
	document.getElementById('edit_etat_tache').value    = etat;
	document.getElementById('edit_categorie').value     = categorie;
}

function loadTaskDetails(titre, description, echeance, etat, categorie, id_tache)
{
	document.getElementById('detail_titre').innerText       = titre;
	document.getElementById('detail_description').innerText = description;
	document.getElementById('detail_echeance').innerText    = echeance;
	document.getElementById('detail_etat').innerText        = etat;
	document.getElementById('detail_categorie').innerText   = categorie;
	document.getElementById('comment_task_id').value        = id_tache;

	// Récupérer les commentaires pour cette tâche
	fetch('<?= site_url('comment/get/') ?>' + id_tache)
		.then(response => response.json())
		.then(data => {
			const commentsSection = document.getElementById('commentaires_section');
			// Parcourir les commentaires et les afficher
			data.forEach(comment => {
				const commentHTML = `
					<div class="mb-3 border p-2">
						<p><strong>${comment.date_commentaire}</strong></p>
						<p>${comment.text_commentaire}</p>
					</div>
				`;
				commentsSection.innerHTML += commentHTML;
			});
		})
		.catch(error => {
			console.error("Erreur lors de la récupération des commentaires:", error);
		});
	
	// Ajouter un événement au formulaire pour envoyer un nouveau commentaire
	const form = document.getElementById('addCommentForm');
	form.addEventListener('submit', function(event)
	{
		event.preventDefault(); // Empêcher la soumission classique du formulaire

		const formData = new FormData(form);
		
		// Envoi de la requête pour ajouter un commentaire
		fetch('<?= site_url('comment/add'); ?>', {
			method: 'POST',
			body: formData
		})
		.then(response => response.json())
		.then(data => {
			if (data.success)
			{
				// Créer un message de succès dans la popup
				const successMessage = document.createElement('div');
				successMessage.id = 'success_message';
				successMessage.classList.add('alert', 'alert-success');
				successMessage.innerText = 'Commentaire ajouté avec succès !';
				// Ajouter ce message juste en dessous du formulaire des commentaires
				document.getElementById('commentaires_section').prepend(successMessage);
				// Réinitialiser le formulaire
				document.getElementById('comment_text').value = '';
				// Mettre à jour la liste des commentaires
				loadTaskDetails(titre, description, echeance, etat, categorie, id_tache);
			}
			else
			{
				const errorMessage = document.createElement('div');
				errorMessage.classList.add('alert', 'alert-danger');
				errorMessage.innerText = 'Une erreur est survenue lors de l\'ajout du commentaire.';
				document.getElementById('commentaires_section').prepend(errorMessage);
			}
		})
		.catch(error => {
			console.error("Erreur lors de l'ajout du commentaire :", error);
		});
	});
}


function switchView(view)
{
	const tableauView = document.getElementById('tableauView');
	const tableurView = document.getElementById('tableurView');
	const navLinks = document.querySelectorAll('.nav-link');

	if (view === 'tableau')
	{
		tableauView.style.display = 'block';
		tableurView.style.display = 'none';
	}
	else
	{
		tableauView.style.display = 'none';
		tableurView.style.display = 'block';
	}

	navLinks.forEach(link => link.classList.remove('active'));
	document.getElementById('view' + (view === 'tableau' ? 'Tableau' : 'Tableur')).classList.add('active');
}

</script>
<div class="content m-3 w-100" >
	<h1>Gestion des Tâches</h1>

	<div id="tableauView" class="mb-5">

		<div class="row w-100 d-flex justify-content-between" style="height:100vh">
			<?php
			renderTaskColumn("À faire", $tasksToDo, "À faire", "#taskModal");
			renderTaskColumn("En cours", $tasksInProgress, "En cours", "#taskModal");
			renderTaskColumn("Terminée", $tasksCompleted, "Terminée", "#taskModal");
			?>
		</div>
	</div>

	<!-- Vue Tableur -->
	<div id="tableurView" style="display: none;">
			<table class="table table-striped">
				<thead>
					<tr>
						<th>Nom de la Tâche</th>
						<th>Catégorie</th>
						<th>Statut</th>
						<th>Date d'Échéance</th>
					</tr>
				</thead>
				<tbody>
					<?php foreach (array_merge($tasksToDo, $tasksInProgress, $tasksCompleted) as $task): ?>
						<tr>
							<td><?= esc($task['titre']); ?></td>
							<td><?= esc($task['titre_categorie'] ?? ''); ?></td>
							<td><?= esc($task['etat_tache']); ?></td>
							<td><?= esc(strftime('%A %d %B %Y', (new DateTime($task['echeance_tache']))->getTimestamp())); ?></td>
						</tr>
					<?php endforeach; ?>
				</tbody>
			</table>
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
							<input type="text" class="form-control" name="categorie" id="categorie" list="categories-list" required>
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

	<script>
		// Fonction pour définir le statut par défaut en fonction de la colonne
		function setTaskStatus(status) {
			document.getElementById('etat_tache').value = status;
			document.getElementById('statut_par_defaut').value = status;
		}
	</script>


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
						<input type="text" class="form-control" name="categorie" id="edit_categorie" list="categories-list">
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
<script>
	let draggedTask = null;

	function drag(event) {
		draggedTask = event.target;
		const taskId = draggedTask.getAttribute('data-task-id');
		event.dataTransfer.setData("text/plain", taskId);
	}

	function allowDrop(event) {
		event.preventDefault();
	}

	function drop(event, newStatusId) {
		event.preventDefault();
		const taskId = event.dataTransfer.getData("text/plain");

		if (draggedTask) {
			// Déplacer visuellement la tâche dans la nouvelle colonne
			const targetColumn = event.target.closest('.task-status').querySelector('#' + newStatusId);
			if (targetColumn) {
				targetColumn.appendChild(draggedTask);
			}

			// Appeler le backend pour mettre à jour le statut
			updateTaskStatus(taskId, newStatusId); // C'est ici que la requête est envoyée
		}
	}
	function updateTaskStatus(taskId, newStatusId) {
		fetch('<?= site_url('tasks/updateStatus'); ?>', {
			method: 'POST',
			headers: {
				'Content-Type': 'application/x-www-form-urlencoded',
				'X-Requested-With': 'XMLHttpRequest', // AJAX request
				'X-CSRF-Token': '<?= csrf_hash(); ?>'
			},
			body: new URLSearchParams({
				id_tache: taskId,
				etat_tache: newStatusId
			})
		})
			.then(response => response.json())
			.then(data => {
				if (data.success) {
					console.log('Tâche mise à jour avec succès');
					if (data.refresh) {
						// Recharger la page
						location.reload();
					}
				} else {
					console.error('Erreur serveur :', data.message);
				}
			})
			.catch(error => console.error('Erreur AJAX :', error));
	}
</script>

</div>

</div>


</body>

</html>
