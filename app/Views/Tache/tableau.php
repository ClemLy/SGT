<?php
function renderTaskColumn($title, $tasks, $statusId, $modalTarget)
{
	?>
	<div class="col-md-3 task-status p-4 " style="overflow-y:scroll" ondragover="allowDrop(event)"
		ondrop="drop(event, '<?= esc($statusId); ?>')">
		<div class="d-flex justify-content-between align-items-center mb-3">
			<h3><?= esc($title); ?></h3>
			<button class="btn" data-bs-toggle="modal" data-bs-target="<?= $modalTarget ?>" onclick="setTaskStatus('<?= esc($title); ?>')">+</button>
		</div>
		<div id="<?= esc($statusId); ?>" class="class_taches pe-0">
			<?php if (!empty($tasks)): ?>
				<?php foreach ($tasks as $task): ?>
					<?php

					$isOverdue = false;
					if (isset($task['echeance_tache']) && !empty($task['echeance_tache']))
					{
						try
						{
							$dueDate = new DateTime($task['echeance_tache']);
							$currentDate = new DateTime();
							$isOverdue = $dueDate < $currentDate;
						}
						catch (Exception $e)
						{

							$isOverdue = false;
						}
					}
					?>
					<div class="card my-2 border-0 mb-4 <?= $isOverdue ? 'overdue' : ''; ?> " draggable="true"
						ondragstart="drag(event)"
						data-task-id="<?= esc($task['id_tache']); ?>">
						<div class="card-body p-4">
							<div class="d-flex justify-content-between align-items-center w-100 mb-3">
								<p><small class="text-uppercase"><?= esc($task['titre_categorie'] ?? ''); ?></small></p>
								<div class="d-flex justify-content-between align-items-center">
									<p class="date border border-dark <?= $isOverdue ? 'bi bi-alarm-fill' : ''; ?>" style="border-radius:15px">
										<?= esc(strftime(
											(date('Y') === (new DateTime($task['echeance_tache']))->format('Y') ? '%A %d %B' : '%A %d %B %Y'),
											(new DateTime($task['echeance_tache']))->getTimestamp()
										)); ?>
									</p>
									<button class="bi bi-three-dots btn btn-link ps-3 pe-1" data-bs-toggle="dropdown" aria-expanded="false"></button>
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

<?php
	renderTaskColumn("À faire", $tasksToDo, "À faire", "#taskModal");
	renderTaskColumn("En cours", $tasksInProgress, "En cours", "#taskModal");
	renderTaskColumn("Terminée", $tasksCompleted, "Terminée", "#taskModal");
?>