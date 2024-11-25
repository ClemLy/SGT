<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestion des Tâches</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>

<div class="container my-5">
    <h1>Gestion des Tâches</h1>

    <div class="row">
        <!-- Colonne À Faire -->
        <div class="col-md-4">
            <h3>À faire</h3>
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#taskModal" onclick="setTaskStatus('À faire')">+</button>
            <div id="tasks-to-do">
                <?php if (!empty($tasksToDo)): ?>
                    <?php foreach ($tasksToDo as $task): ?>
                        <div class="card my-2">
                            <div class="card-body">
                                <h5 class="card-title"><?= esc($task['titre']); ?></h5>
                                <p class="card-text"><?= esc($task['description_tache']); ?></p>
                                <p><strong>Catégorie :</strong> <?= esc($task['titre_categorie']); ?></p>
                                <p><strong>Échéance :</strong> <?= esc($task['echeance_tache']); ?></p>
                            </div>
                        </div>
                     <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune tâche à faire.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Colonne En Cours -->
        <div class="col-md-4">
            <h3>En cours</h3>
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#taskModal" onclick="setTaskStatus('En cours')">+</button>
            <div id="tasks-in-progress">
                <?php if (!empty($tasksInProgress)): ?>
                    <?php foreach ($tasksInProgress as $task): ?>
                        <div class="card my-2">
                            <div class="card-body">
                                <h5 class="card-title"><?= esc($task['titre']); ?></h5>
                                <p class="card-text"><?= esc($task['description_tache']); ?></p>
                                <p><strong>Catégorie :</strong> <?= esc($task['titre_categorie']); ?></p>
                                <p><strong>Échéance :</strong> <?= esc($task['echeance_tache']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune tâche en cours.</p>
                <?php endif; ?>
            </div>
        </div>

        <!-- Colonne Terminé -->
        <div class="col-md-4">
            <h3>Terminé</h3>
            <button class="btn btn-primary mb-3" data-bs-toggle="modal" data-bs-target="#taskModal" onclick="setTaskStatus('Terminée')">+</button>
            <div id="tasks-completed">
                <?php if (!empty($tasksCompleted)): ?>
                    <?php foreach ($tasksCompleted as $task): ?>
                        <div class="card my-2">
                            <div class="card-body">
                                <h5 class="card-title"><?= esc($task['titre']); ?></h5>
                                <p class="card-text"><?= esc($task['description_tache']); ?></p>
                                <p><strong>Catégorie :</strong> <?= esc($task['titre_categorie']); ?></p>
                                <p><strong>Échéance :</strong> <?= esc($task['echeance_tache']); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p>Aucune tâche terminée.</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<!-- Modal de création de tâche -->
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


                    <button type="submit" class="btn btn-primary">Ajouter la tâche</button>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
    function setTaskStatus(status) {
        document.getElementById('etat_tache').value = status;
    }
</script>

</body>
</html>
