<?php setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr'); ?>
<?php $perPage = $perPage ?? 10; ?>
<div id="tableTasksBody">
    <!-- Formulaire pour le nombre de tâches par page -->
    <form id="perPageForm" method="get" action="<?= current_url(); ?>">
        <label for="perPage">Tâches par page :</label>
        <select name="perPage" id="perPage">
            <option value="0" <?= $perPage == 0 ? 'selected' : '' ?>>Tout</option>
            <option value="5" <?= $perPage == 5 ? 'selected' : '' ?>>5</option>
            <option value="10" <?= $perPage == 10 ? 'selected' : '' ?>>10</option>
            <option value="15" <?= $perPage == 15 ? 'selected' : '' ?>>15</option>
        </select>
        <!-- Champs cachés pour conserver les autres paramètres -->
        <input type="hidden" name="criteria" value="<?= $criteria; ?>">
        <input type="hidden" name="order" value="<?= $order; ?>">
        <input type="hidden" name="searchQuery" value="<?= $searchQuery; ?>">
    </form>

    <button class="btn" data-bs-toggle="modal" data-bs-target="#taskModal">+ (Ajouter tâche)</button>

    <!-- Tableau des tâches -->
    <table class="table table-striped">
        <thead>
        <tr>
            <th>Titre</th>
            <th>Catégorie</th>
            <th>État</th>
            <th>Échéance</th>
            <th>Importance</th>

        </tr>
        </thead>
        <tbody>
        <?php
        $allTasks = $tasks ?? array_merge($tasksToDo ?? [], $tasksInProgress ?? [], $tasksCompleted ?? []);
        if (!empty($allTasks)): ?>
            <?php foreach ($allTasks as $task): ?>
                <?php
                $isOverdue = false;
                if (isset($task['echeance_tache']) && !empty($task['echeance_tache']) && $task['etat_tache'] != 'Terminée') {
                    try {
                        $dueDate = new DateTime($task['echeance_tache']);
                        $dueDate->modify('+1 day'); // Ajouter un jour à la date d'échéance
                        $currentDate = new DateTime();
                        $isOverdue = $dueDate < $currentDate;
                    } catch (Exception $e) {
                        $isOverdue = false;
                    }
                }
                ?>

                <tr class="<?= $isOverdue ? 'overdue' : '' ?>"
                    onclick="handleRowClick(event, <?= $task['id_tache']; ?>, '<?= esc($task['titre']); ?>', '<?= esc($task['description_tache']); ?>', '<?= esc($task['echeance_tache']); ?>', '<?= esc($task['etat_tache']); ?>', '<?= esc($task['titre_categorie']); ?>')">
                    <td><?= esc($task['titre']); ?></td>
                    <td><?= esc($task['titre_categorie'] ?? ''); ?></td>
                    <td><?= esc($task['etat_tache']); ?></td>
                    <td><?= esc(strftime((date('Y') === (new DateTime($task['echeance_tache']))->format('Y') ? '%A %d %B' : '%A %d %B %Y'), (new DateTime($task['echeance_tache']))->getTimestamp())); ?></td>
                    <td><?= esc($task['importance_tache']);?></td>
                    <td class="no-click">
                        <button class="bi bi-three-dots btn btn-link ps-3 pe-1" data-bs-toggle="dropdown" aria-expanded="false"></button>
                        <ul class="dropdown-menu">
                            <li>
                                <a class="dropdown-item" data-bs-toggle="modal" data-bs-target="#editTaskModal" onclick="loadTaskData(<?= $task['id_tache']; ?>, '<?= esc($task['titre']); ?>', '<?= esc($task['description_tache']); ?>', '<?= esc($task['echeance_tache']); ?>', '<?= esc($task['etat_tache']); ?>', '<?= esc($task['titre_categorie']); ?>')">Modifier</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= site_url('tasks/complete/' . $task['id_tache']); ?>">Marquer comme terminée</a>
                            </li>
                            <li>
                                <a class="dropdown-item" href="<?= site_url('tasks/delete/' . $task['id_tache']); ?>" onclick="return confirm('Êtes-vous sûr de vouloir supprimer cette tâche ?');">Supprimer</a>
                            </li>
                        </ul>
                    </td>
                </tr>
            <?php endforeach; ?>
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">Aucune tâche trouvée</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>

    <!-- Pagination -->
    <div id="paginationContainer">
        <?php if ($pager->getPageCount() > 1): ?>
            <nav aria-label="Pagination">
                <ul class="pagination">
                    <?php if ($pager->getPreviousPageURI()): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $pager->getPreviousPageURI() . '&' . http_build_query([
                                'criteria' => $criteria,
                                'order' => $order,
                                'searchQuery' => $searchQuery,
                                'perPage' => $perPage,
                            ]); ?>" aria-label="Précédent">&laquo;</a>
                        </li>
                    <?php endif; ?>
                    <?php for ($page = 1; $page <= $pager->getPageCount(); $page++): ?>
                        <li class="page-item <?= $page == $pager->getCurrentPage() ? 'active' : ''; ?>">
                            <a class="page-link" href="<?= $pager->getPageURI($page) . '&' . http_build_query([
                                'criteria' => $criteria,
                                'order' => $order,
                                'searchQuery' => $searchQuery,
                                'perPage' => $perPage,
                            ]); ?>"><?= $page; ?></a>
                        </li>
                    <?php endfor; ?>
                    <?php if ($pager->getNextPageURI()): ?>
                        <li class="page-item">
                            <a class="page-link" href="<?= $pager->getNextPageURI() . '&' . http_build_query([
                                'criteria' => $criteria,
                                'order' => $order,
                                'searchQuery' => $searchQuery,
                                'perPage' => $perPage,
                            ]); ?>" aria-label="Suivant">&raquo;</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </nav>
        <?php endif; ?>
    </div>
</div>

<script>
    document.getElementById('perPage').addEventListener('change', function () {
        const url = new URL(window.location.href);

        // Mettre à jour le paramètre `perPage` dans l'URL
        url.searchParams.set('perPage', this.value);
        url.searchParams.set('page', 1); // Réinitialise à la première page

        // Mettre à jour l'URL visible sans recharger la page
        window.history.pushState({}, '', url);

        console.log('Nombre de tâches par page modifié : URL mise à jour :', url.toString());

        // Effectuer une requête AJAX pour mettre à jour les tâches
        updateTasks();
    });

    function updateTasks() {
        const url = new URL(window.location.href);

        console.log('Requête AJAX vers URL :', url.toString()); // Debug pour voir l'URL utilisée

        fetch(url.toString(), {
            method: 'GET',
            headers: { 'X-Requested-With': 'XMLHttpRequest' }, // Signale une requête AJAX
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Erreur HTTP : ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Réponse JSON reçue :', data); // Vérifie le contenu de la réponse

                // Mise à jour du tableau des tâches
                if (data.tasksTable) {
                    document.getElementById('tableTasksBody').innerHTML = data.tasksTable;
                } else {
                    console.error('Erreur : tasksTable manquant dans la réponse');
                }

                // Mise à jour de la pagination
                if (data.pagerLinks) {
                    const paginationContainer = document.getElementById('paginationContainer');
                    if (paginationContainer) {
                        paginationContainer.innerHTML = data.pagerLinks;
                    } else {
                        console.error('Erreur : Conteneur de pagination introuvable');
                    }
                }
            })
            .catch(error => console.error('Erreur lors de la mise à jour des tâches :', error));
    }

</script>