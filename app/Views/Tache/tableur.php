<!-- Vue Tableur -->

<?php if (isset($tasksToDo, $tasksInProgress, $tasksCompleted)): ?>
    <?php setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr'); ?>
    <div id="tableTasksBody">
        <button class="btn" data-bs-toggle="modal" data-bs-target="#taskModal">+ (Ajouter tache)</button>
        <table class="table table-striped">
            <thead>
            <tr>
                <th>Nom de la Tâche</th>
                <th>Catégorie</th>
                <th>Statut</th>
                <th>Date d'Échéance</th>
                <th>Actions</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach (array_merge($tasksToDo, $tasksInProgress, $tasksCompleted) as $task): ?>
                <?php
                $isOverdue = false;
                if (isset($task['echeance_tache']) && !empty($task['echeance_tache']))
                {
                    try
                    {
                        $dueDate     = new DateTime($task['echeance_tache']);
                        $currentDate = new DateTime();
                        $isOverdue   = $dueDate < $currentDate;
                    }
                    catch (Exception $e)
                    {
                        $isOverdue = false;
                    }
                }
                ?>
                <tr class="cursor: pointer; <?= $isOverdue ? 'overdue' : '' ?>" onclick="handleRowClick(event, <?= $task['id_tache']; ?>, '<?= esc($task['titre']); ?>', '<?= esc($task['description_tache']); ?>', '<?= esc($task['echeance_tache']); ?>', '<?= esc($task['etat_tache']); ?>', '<?= esc($task['titre_categorie']); ?>')">
                    <td class="<?= $isOverdue ? 'bi bi-alarm-fill' : ''; ?>"><?= esc($task['titre']); ?></td>
                    <td><?= esc($task['titre_categorie'] ?? ''); ?></td>
                    <td><?= esc($task['etat_tache']); ?></td>
                    <td><?= esc(strftime((date('Y') === (new DateTime($task['echeance_tache']))->format('Y') ? '%A %d %B' : '%A %d %B %Y'), (new DateTime($task['echeance_tache']))->getTimestamp())); ?></td>
                    <td class="no-click">
                        <button class="bi bi-three-dots btn btn-link ps-3 pe-1" data-bs-toggle="dropdown" aria-expanded="false"></button>
                        <ul class="dropdown-menu">
                            <li><a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editTaskModal" onclick="loadTaskData(<?= $task['id_tache']; ?>, '<?= esc($task['titre']); ?>', '<?= esc($task['description_tache']); ?>', '<?= esc($task['echeance_tache']); ?>', '<?= esc($task['etat_tache']); ?>', '<?= esc($task['titre_categorie']); ?>')">Modifier</a>
                            </li>
                            <li><a class="dropdown-item" href="<?= site_url('tasks/complete/' . $task['id_tache']); ?>">Marquer comme terminée</a></li>
                            <li><a class="dropdown-item" href="<?= site_url('tasks/delete/' . $task['id_tache']); ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?');">Supprimer</a></li>
                        </ul>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<script>
    function handleRowClick(event, id, title, description, dueDate, status, category) {
        if (event.target.closest('.no-click')) {
            return; // Ignorer le clic
        }
        loadTaskDetails(title, description, dueDate, status, category, id);
    }
</script>
<?php else: ?>
    <p>Erreur : Les données des tâches ne sont pas définies.</p>
<?php endif; ?>


