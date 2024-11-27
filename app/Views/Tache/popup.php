<div class="modal fade" id="taskDetailModal" tabindex="-1" aria-labelledby="taskDetailModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="taskDetailModalLabel">Détails de la Tâche</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <p><strong>Titre : </strong> <span id="detail_titre"></span></p>
                <p><strong>Description : </strong> <span id="detail_description"></span></p>
                <p><strong>Catégorie : </strong> <span id="detail_categorie"></span></p>
                <p><strong>Échéance : </strong> <span id="detail_echeance"></span></p>
                <p><strong>Statut : </strong> <span id="detail_etat"></span></p>

                <hr>

                <h3>Commentaires</h3>

                <!-- Formulaire d'ajout de commentaire -->
                <form id="addCommentForm">
                    <?= csrf_field(); ?>
                    <input type="hidden" name="id_tache" id="comment_task_id">
                    <div class="mb-3">
                        <label for="comment_text" class="form-label">Ajouter un commentaire</label>
                        <textarea class="form-control" name="text_commentaire" id="comment_text" rows="2" required></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Ajouter</button>
                </form>

                <hr>

                <!-- Section des commentaires -->
                <div id="commentaires_section"></div>
            </div>
        </div>
    </div>
</div>
