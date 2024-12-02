<?php setlocale(LC_TIME, 'fr_FR.UTF-8', 'fr_FR', 'fr'); ?>
<?php
$perPage = $perPage ?? 0;
$criteria = $criteria ?? 'echeance_tache';
$order = $order ?? 'asc';

?>


<div id="tableTasksBody">
    <!-- Formulaire pour le nombre de tâches par page -->
    <form id="perPageForm" class="perPageForm" method="get" action="<?= current_url(); ?>">
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

    <button class="btn-ajoutTableur" data-bs-toggle="modal" data-bs-target="#taskModal">+</button>

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
<<<<<<< HEAD
            }
            ?>
            <tr class="<?= $isOverdue ? 'overdue' : '' ?>"
                onclick="handleRowClick(event, <?= $task['id_tache']; ?>, '<?= esc($task['titre']); ?>', '<?= esc($task['description_tache']); ?>', '<?= esc($task['importance_tache']); ?>', '<?= esc($task['echeance_tache']); ?>', '<?= esc($task['etat_tache']); ?>', '<?= esc($task['titre_categorie']); ?>')">
                <td><?= esc($task['titre']); ?></td>
                <td><?= esc($task['titre_categorie'] ?? ''); ?></td>
                <td><?= esc($task['etat_tache']); ?></td>
                <td><?= esc(strftime((date('Y') === (new DateTime($task['echeance_tache']))->format('Y') ? '%A %d %B' : '%A %d %B %Y'), (new DateTime($task['echeance_tache']))->getTimestamp())); ?></td>
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
=======
                ?>

                <tr class="<?= $isOverdue ? 'overdue' : '' ?>"
                    onclick="handleRowClick(event,
                            '<?= $task['titre']; ?>',
                            '<?= esc($task['description_tache']); ?>',
                            '<?= esc($task['importance_tache']); ?>',
                            '<?= esc($task['echeance_tache']); ?>',
                            '<?= esc($task['etat_tache']); ?>',
                            '<?= esc($task['titre_categorie']); ?>',
                            '<?= esc ($task['id_tache']); ?>'
                            )">
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
>>>>>>> 368ee05da15e3f98a05f74651cbbe793c316fd75
        <?php else: ?>
            <tr>
                <td colspan="5" class="text-center">Aucune tâche trouvée</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>


    <!-- Pagination -->
    <div id="paginationContainer">
        <?php if (isset($pager) && $pager !== null && $pager->getPageCount() > 1): ?>
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
<<<<<<< HEAD
    function handleRowClick(event, id, title, description, importance, dueDate, status, category) {
=======
    document.getElementById('perPage').addEventListener('change', function () {
        const url = new URL(window.location.href); // Récupère l'URL actuelle

        // Met à jour le paramètre `perPage` avec la nouvelle valeur
        url.searchParams.set('perPage', this.value);

        // Récupère les paramètres actuels de tri et les ajoute si présents
        const criteria = url.searchParams.get('criteria');
        const order = url.searchParams.get('order');

        if (criteria) {
            url.searchParams.set('criteria', criteria); // Ajoute ou conserve le critère de tri
        }

        if (order) {
            url.searchParams.set('order', order); // Ajoute ou conserve l'ordre de tri
        }

        // Redirige vers l'URL mise à jour pour recharger les données paginées
        window.location.href = url.toString();
    });


    function handleRowClick(event, titre, description, importance,echeance, etat, categorie,id_tache) {
>>>>>>> 368ee05da15e3f98a05f74651cbbe793c316fd75
        if (event.target.closest('.no-click')) {
            return; // Ignorer le clic
        }
        const taskDetailModal = new bootstrap.Modal(document.getElementById('taskDetailModal'));
        taskDetailModal.show();
<<<<<<< HEAD
        loadTaskDetails(title, description, importance, dueDate, status, category, id);
        
=======
        loadTaskDetails(titre, description, importance,echeance, etat, categorie,id_tache);

>>>>>>> 368ee05da15e3f98a05f74651cbbe793c316fd75
    }






</script>