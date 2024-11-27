<!-- Vue Tableur -->


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
			<tr style="cursor: pointer;" data-bs-toggle="modal" data-bs-target="#taskDetailModal"
				onclick="loadTaskDetails(
					'<?= esc($task['titre']); ?>', 
					'<?= esc($task['description_tache']); ?>', 
					'<?= esc($task['echeance_tache']); ?>', 
					'<?= esc($task['etat_tache']); ?>', 
					'<?= esc($task['titre_categorie']); ?>', 
					'<?= esc($task['id_tache']); ?>')">
				<td><?= esc($task['titre']); ?></td>
				<td><?= esc($task['titre_categorie'] ?? ''); ?></td>
				<td><?= esc($task['etat_tache']); ?></td>
				<td><?= esc(strftime('%A %d %B %Y', (new DateTime($task['echeance_tache']))->getTimestamp())); ?></td>
			</tr>
		<?php endforeach; ?>
	</tbody>
</table>

</div>