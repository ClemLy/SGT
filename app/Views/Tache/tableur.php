<!-- Vue Tableur -->
 <script>
	console.log(' ta mere')
	</script>
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